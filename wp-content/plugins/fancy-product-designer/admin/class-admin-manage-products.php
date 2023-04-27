<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Manage_Products') ) {

	class FPD_Admin_Manage_Products {

		public function output() {

			global $wpdb;
			$page_links = false;

			if( isset($_POST['run_updater']) ) {

				FPD_Install::do_upgrade('2.0.0');
				update_option('fpd_hide_run_updater', 'yes');

			}

			if( isset($_POST['fpd_filter_by']) )
				update_option('fpd_admin_filter_by', $_POST['fpd_filter_by']);

			if( isset($_POST['fpd_order_by']) )
				update_option('fpd_admin_order_by', $_POST['fpd_order_by']);

			$filter_by = get_option('fpd_admin_filter_by', 'title');
			$order_by = get_option('fpd_admin_order_by', 'ASC');

			$where = '';
			if( isset($_POST['fpd_search_products_string']) && !empty($_POST['fpd_search_products_string']) )
				$where = "WHERE title LIKE '%{$_POST['fpd_search_products_string']}%'";

			$categories = array();
			if( fpd_table_exists(FPD_CATEGORIES_TABLE) ) {
				$categories = $wpdb->get_results("SELECT * FROM ".FPD_CATEGORIES_TABLE." ORDER BY title ASC");
			}

			require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-import.php' );
			?>

			<!-- wrap beginn -->
			<div class="wrap" id="fpd-manage-products">
				<h2>
					<?php _e('Manage Products', 'radykal'); ?>
					<?php fpd_admin_display_version_info(true); ?>
				</h2>

			<?php
			$products = array();
			if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {

				$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
				$limit = 20;
				$offset = ( $pagenum - 1 ) * $limit;
				$total = $wpdb->get_var( "SELECT COUNT(ID) FROM ".FPD_PRODUCTS_TABLE."" );
				$num_of_pages = ceil( $total / $limit );

				$page_links = paginate_links( array(
				    'base' => add_query_arg( 'paged', '%#%' ),
				    'format' => '',
				    'prev_text' => '&laquo;',
				    'next_text' => '&raquo;',
				    'total' => $num_of_pages,
				    'current' => $pagenum
				) );

				$products = $wpdb->get_results("SELECT * FROM ".FPD_PRODUCTS_TABLE." ".$where." ORDER BY ".$filter_by." ".$order_by." LIMIT $limit OFFSET $offset");
			}

			//select by category
			if( isset($_GET['category_id']) ) {

				$page_links = false;
				$products = $wpdb->get_results("SELECT * FROM ".FPD_PRODUCTS_TABLE." WHERE ID IN (SELECT product_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id={$_GET['category_id']})");

			}

			if ( isset($_GET['info']) ) {

		        $info_url = $_GET['info'] == 'activated' ? 'http://assets.fancyproductdesigner.com/info-modals/installed.html' : 'http://assets.fancyproductdesigner.com/info-modals/updated.html';

		        require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-updated-installed-info.php');

		    }

			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-demo.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-template.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-edit-product-options.php');
			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-shortcodes.php');

			?>
				<?php if( get_option('fpd_hide_run_updater', 'yes') !== 'yes' ): ?>
				<div class="fpd-panel">
					<h3>Update</h3>
					<p>Your action is required to update to the latest version.</p>
					<form method="POST">
						<input type="submit" name="run_updater" value="Run Updater" class="button-primary" />
					</form>
				</div>
				<?php endif; ?>
				<div class="fpd-clearfix">

					<form action="<?php echo admin_url('admin.php?page=fancy_product_designer'); ?>" method="POST"enctype="multipart/form-data" name="fpd_import" class="fpd-hidden">
						<input type="checkbox" name="fpd_import_to_library" />
						<input name="fpd_import_file" type="file" />
					</form>

					<div id="fpd-products" class="fpd-panel">
						<h3>
							<?php _e('Products', 'radykal'); ?>
							<a href="#" id="fpd-add-product" class="add-new-h2" style="float: none; margin-left: 10px;"><?php _e('Add New', 'radykal'); ?></a>
							<a href="#" id="fpd-import-product" class="add-new-h2"><?php _e('Import', 'radykal'); ?></a>
							<a href="#" id="fpd-load-template" class="add-new-h2"><?php _e('Load Template', 'radykal'); ?></a>
							<a href="#" id="fpd-load-demo" class="add-new-h2 fpd-admin-tooltip" title="<?php _e('Download a demo to get started', 'radykal'); ?>"><?php _e('Download Demo', 'radykal'); ?></a>
						</h3>

						<div id="fpd-products-nav" class="fpd-clearfix">
							<form method="POST">
								<span class="description"><?php _e('Filter:', 'radykal') ?></span>
								<select name="fpd_filter_by">
									<option value="ID" <?php selected($filter_by, 'ID'); ?> ><?php _e('ID', 'radykal') ?></option>
									<option value="title" <?php selected($filter_by, 'title'); ?> ><?php _e('Title', 'radykal') ?></option>
								</select>
								<select name="fpd_order_by">
									<option value="ASC" <?php selected($order_by, 'ASC'); ?> ><?php _e('Ascending', 'radykal') ?></option>
									<option value="DESC" <?php selected($order_by, 'DESC'); ?>><?php _e('Descending', 'radykal') ?></option>
								</select>
							</form>
							<form method="POST" name="fpd_search_products">
								<input type="text" name="fpd_search_products_string" placeholder="<?php _e('Search Products...', 'radykal') ?>" />
								<input type="submit" class="button button-secondary" value="<?php _e('Search', 'radykal') ?>" />
							</form>
						</div>

						<?php if( empty($products) ): ?>
						<p class="fpd-error-message"><strong><?php _e('No Products found!', 'radykal') ?></strong></p>
						<?php endif; ?>

						<ul id="fpd-products-list">
							<?php

							foreach($products as $product) {
								$fancy_product = new FPD_Product($product->ID);
								$category_ids = $fancy_product->get_category_ids();

								echo self::get_product_item_html(
									$product->ID,
									$product->title,
									implode(',', $category_ids),
									stripslashes($product->options),
									isset($product->thumbnail) ? stripslashes($product->thumbnail) : ''
								);

								echo '<ul class="fpd-views-list">';
								$product_views = $fancy_product->get_views();
								if( !empty($product_views) ) {

									foreach($product_views as $view) {
										echo self::get_view_item_html($view->ID, $view->thumbnail, $view->title);
									}

								}
								echo '</ul>';

							}

							?>
						</ul>
						<?php
						if ( $page_links ) {
						    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 0;">' . $page_links . '</div></div>';
						}
						?>
						<div class="fpd-ui-blocker"></div>

					</div>
					<div id="fpd-categories" class="fpd-panel">
						<h3>
							<?php _e('Categories', 'radykal'); ?>
							<a href="#" id="fpd-add-category" class="add-new-h2"><?php _e('Add New', 'radykal'); ?></a>
						</h3>
						<ul id="fpd-categories-list">
							<?php

							foreach($categories as $category) {
								echo self::get_category_item_html($category->ID, $category->title);
							}

							?>
						</ul>
						<div class="fpd-ui-blocker"></div>

					</div>

				</div>

			</div>
			<?php

		}

		public static function get_product_item_html( $id, $title, $category_ids='', $options='', $thumbnail='' ) {

			if( !empty($thumbnail) ) {
				$thumbnail = '<img src="'.$thumbnail.'" />';
			}

			return '<li id="'.$id.'" data-categories="'.$category_ids.'" class="fpd-product-item fpd-clearfix"><span class="fpd-clearfix"><span class="fpd-single-image-upload fpd-admin-tooltip" title="'.__('Product Thumbnail', 'radykal').'"><span class="fpd-remove"><span class="dashicons dashicons-minus"></span></span>'.$thumbnail.'</span><span class="fpd-product-meta"><span class="fpd-item-id">#'.$id.'</span><span class="fpd-product-title">'.$title.'</span></span></span><span><a href="#" class="fpd-add-view fpd-admin-tooltip" title="'.__('Add View', 'radykal').'"><i class="fpd-admin-icon-add-box"></i></a><a href="#" class="fpd-edit-product-title fpd-admin-tooltip" title="'.__('Edit Title', 'radykal').'"><i class="fpd-admin-icon-mode-edit"></i></a><a href="#" class="fpd-edit-product-options fpd-admin-tooltip" title="'.__('Edit Options', 'radykal').'"><input type="hidden" class="fpd-product-options fpd-hidden" value="'.$options.'" /><i class="fpd-admin-icon-settings"></i></a><a href="#" class="fpd-export-product fpd-admin-tooltip" title="'.__('Export', 'radykal').'"><i class="fpd-admin-icon-cloud-download"></i></a><a href="#" class="fpd-save-as-template fpd-admin-tooltip" title="'.__('Save as template', 'radykal').'"><i class="fpd-admin-icon-template"></i></a><a href="#" class="fpd-duplicate-product fpd-admin-tooltip" title="'.__('Duplicate', 'radykal').'"><i class="fpd-admin-icon-content-copy"></i></a><a href="#" class="fpd-remove-product fpd-admin-tooltip" title="'.__('Delete', 'radykal').'"><i class="fpd-admin-icon-bin"></i></a></span></li>';

		}

		public static function get_view_item_html( $id, $image, $title ) {

			$product_builder_url = admin_url().'admin.php?page=fpd_product_builder&view_id='.$id;

			return '<li id="'.$id.'" class="fpd-view-item fpd-clearfix"><span><img src="'.$image.'" class="fpd-admin-tooltip" title="'.__('View Thumbnail', 'radykal').'" /><label>'.$title.'</label></span><span><a href="'.$product_builder_url.'" target="_self" class="fpd-admin-tooltip" title="'.__('Edit view in product builder', 'radykal').'"><i class="fpd-admin-icon-layers"></i></a><a href="#" class="fpd-edit-view-title fpd-admin-tooltip" title="'.__('Edit Title', 'radykal').'"><i class="fpd-admin-icon-mode-edit"></i></a><a href="#" class="fpd-duplicate-view fpd-admin-tooltip" title="'.__('Duplicate', 'radykal').'"><i class="fpd-admin-icon-content-copy"></i></a><a href="#" class="fpd-remove-view fpd-admin-tooltip" title="'.__('Delete', 'radykal').'"><i class="fpd-admin-icon-bin"></i></a></span></li>';

		}

		public static function get_category_item_html( $id, $title ) {

			$active_filter = '';
			$url_params = '?page=fancy_product_designer&category_id='.$id;
			if( isset($_GET['category_id']) &&  $_GET['category_id'] === $id ) {
				$active_filter = 'fpd-active';
				$url_params = '?page=fancy_product_designer';
			}


			return '<li id="'.$id.'" class="fpd-category-item fpd-clearfix"><span><label><input type="checkbox" /><span class="fpd-category-title">'.$title.'</span></label></span><span><a href="'.$url_params.'" class="fpd-filter-category fpd-admin-tooltip '.$active_filter.'" title="'.__('Show only products of this category', 'radykal').'"><i class="fpd-admin-icon-remove-red-eye"></i></a><a href="#" class="fpd-edit-category-title fpd-admin-tooltip" title="'.__('Edit Title', 'radykal').'"><i class="fpd-admin-icon-mode-edit"></i></a><a href="#" class="fpd-remove-category fpd-admin-tooltip" title="'.__('Delete', 'radykal').'"><i class="fpd-admin-icon-bin"></i></a></span></li>';

		}

		public static function get_template_link_html( $template_id, $title) {

			return "<li><a href='#' id='".esc_attr( $template_id )."'>".$title."</a><a href='#' class='fpd-remove-template fpd-right'><i class='fpd-admin-icon-close'></i></a></li>";

		}
	}
}

new FPD_Admin_Manage_Products();

?>