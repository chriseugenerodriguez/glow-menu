<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Import') ) {

	class FPD_Admin_Import {

		private $extracted_dir_url;
		private $add_to_media_library = false;
		private $uploaded_file;

		public function __construct() {

			if(isset($_FILES['fpd_import_file'])) {

				$this->add_to_media_library = isset($_POST['fpd_import_to_library']);

				$upload_dir = wp_upload_dir();
				$upload_dir = $upload_dir['basedir'];

				$imports_dir = $upload_dir . '/fpd_imports/';

				wp_mkdir_p( $imports_dir );

				$zip_name = $_FILES["fpd_import_file"]["name"];
				$this->uploaded_file = $imports_dir . $zip_name;
				move_uploaded_file($_FILES['fpd_import_file']['tmp_name'], $this->uploaded_file);

				$zip = new ZipArchive;
				$res = $zip->open($this->uploaded_file);
				if ($res === true) {

					$extracted_dir = $imports_dir . basename( $zip_name, '.zip');
					$zip->extractTo($extracted_dir);
					$zip->close();

					@unlink($this->uploaded_file);

					$this->read_json($extracted_dir);

				}
				else {
					switch($res) {

				        case ZipArchive::ER_NOZIP:
				            $this->output_error(__('Not a zip archive.', 'radykal'));
				        break;
				        case ZipArchive::ER_INCONS :
				           $this->output_error( __('Consistency check failed.', 'radykal'));
				        break;
				        case ZipArchive::ER_CRC :
				            $this->output_error(__('Checksum failed.', 'radykal'));
				        break;
				        default:
				            $this->output_error(__('error ', 'radykal') . $res);

				    }

				    @unlink($this->uploaded_file);
				}

			}

		}

		private function read_json( $dir ) {

			if( !file_exists($dir . '/product.json') ) {
				$this->output_error(__('Zip does not contain the necessary product.json.', 'radykal'));
				return;
			}

			$json_content = fpd_admin_get_file_content($dir . '/product.json' );
			$json_content = json_decode($json_content);

			$uploads_dir_url = wp_upload_dir();
			$uploads_dir_url = $uploads_dir_url['baseurl'];
			$this->extracted_dir_url = $uploads_dir_url . '/fpd_imports/' . basename( $dir ) . '/';

			$fp_id = FPD_Product::create(
				$json_content->title,
				//? V3.4.2 or lower it was a string : V3.4.2+ is array
				is_string($json_content->options) ?  htmlspecialchars_decode($json_content->options) : json_encode($json_content->options),
				$this->import_image( $json_content->thumbnail )
			);

			if( $fp_id !== false ) {

				$fp = new FPD_Product($fp_id);

				$json_views = $json_content->views;
				$view_count = 0;

				foreach($json_views as $view) {

					$elements = $view->elements;
					foreach($elements as $element) {

						if($element->type == 'image') {

							$element->source = $this->import_image( $element->source );

						}

					}

					$fp->add_view(
						$view->title,
						$view->elements,
						$this->import_image( $view->thumbnail ),
						$view_count,
						$view->options
					);

					$view_count++;

				}

			}

			if( $this->add_to_media_library ) //only remove extraced dir when image are added to media library
				fpd_admin_delete_directory($dir);

		}

		private function import_image( $image ) {

			if( is_null($image) )
				return '';

			if( $this->add_to_media_library ) {

				$file_array = array();
				$file_array['name'] = $image;

				// Download file to temp location.
				$file_array['tmp_name'] = download_url( $this->extracted_dir_url . $image );

				if ( is_wp_error( $file_array['tmp_name'] ) ) {
					$error = $file_array['tmp_name'];
					$this->output_error($image. __(' image could not be added to media library. Error message: ', 'radykal') . $error->get_error_message());
					return '';
				}

				$id = media_handle_sideload( $file_array, 0 );

				if ( is_wp_error( $id ) ) {
					$this->output_error($image. __(' image could not be added to media library.', 'radykal') . $id->get_error_message());
					@unlink( $file_array['tmp_name'] );
					return '';
				}

				$src = wp_get_attachment_url( $id );

				return $src;

			}
			else {
				return $this->extracted_dir_url . $image;
			}

		}

		private function output_error( $error ) {

			echo '<div class="error"><p>'.$error.'</p></div>';

		}
	}
}

new FPD_Admin_Import();

?>