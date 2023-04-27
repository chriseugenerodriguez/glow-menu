<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Designs') ) {

	class FPD_Admin_Designs {

		private $hierarchical_terms = array();

		public function __construct() {

			add_action( 'delete_term',  array( &$this, 'term_delete' ), 10, 4 );

		}

		//delete category parameters if fancy design category is deleted
		public function term_delete( $term_id, $tax_id, $tax_slug, $term ) {

			delete_option( 'fpd_category_parameters_'.$term->slug );

		}

		private function make_terms_hierarchical( $parent_terms = array() ) {

			foreach($parent_terms as $parent_term) {

				$children = get_terms(
				    'fpd_design_category',
				    array(
					    'hide_empty' => false,
					 	'orderby' 	 => 'name',
				        'parent' => $parent_term->term_id,
				    )
				);

				array_push($this->hierarchical_terms, $parent_term);

				if( !empty($children) ) {
					foreach($children as $child) {
						$this->make_terms_hierarchical(array($child));
					}

				}

			}

		}

		public function output() {

			require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-manage-categories.php');

			?>
			<div class="wrap" id="fpd-manage-designs">
				<h2 class="fpd-clearfix">
					<?php _e('Manage Designs', 'radykal'); ?>
						<a class="add-new-h2" href="#" id="fpd-manage-categories"><?php _e('Manage Categories', 'radykal'); ?></a>
					<?php fpd_admin_display_version_info(); ?>
				</h2>
				<?php

					//get all created categories
					$categories = get_terms( 'fpd_design_category', array(
					 	'hide_empty' => false,
					 	'orderby' 	 => 'name',
					 	'parent' => 0
					));

					$this->make_terms_hierarchical($categories);
					$categories = $this->hierarchical_terms;

					//check that categories are not empty
					if( empty($categories) ) {
						echo '<div class="error"><p><strong>'.__('No categories found. You need to create a category first!', 'radykal').'</strong></p></div></div>';
						return false;
					}

					//select first category id
					$selected_category = $categories[0];
					$selected_category_id = $selected_category->term_id;

					//loop through all categories
					foreach($categories as $category) {

						//check if a category is selected
						if( isset($_GET['category_id']) && $_GET['category_id'] == $category->term_id) {
							$selected_category = $category;
							$selected_category_id = $selected_category->term_id;
						}

					}

					if( isset($_POST['save_designs']) &&
						isset($_POST['image_ids']) &&
						!is_null($_POST['image_ids']) &&
						is_array($_POST['image_ids']) ) {

						check_admin_referer( 'fpd_save_designs' );

						//remove all designs from design category
						$args = array(
							'posts_per_page' => -1,
							'post_type' => 'attachment',
							'fpd_design_category' => $selected_category->slug
						);

						//get all attachments and remove the from category
						$designs = get_posts( $args );
						foreach( $designs as $design ) {
							//wp_delete_object_term_relationships($design->ID, 'fpd_design_category');
							wp_remove_object_terms($design->ID, $selected_category->slug, 'fpd_design_category');
						}

					 	$order = 0;
					 	//loop through all submitted images
					 	foreach( $_POST['image_ids'] as $image_id ) {

						 	//update menu order
					 		$attachment = array(
								'ID'           => $image_id,
								'menu_order' => $order
							);

							wp_update_post( $attachment );

							//set relation between image and design category
					 		wp_set_object_terms( $image_id, $selected_category->slug, 'fpd_design_category', true );

					 		update_post_meta( $image_id, $selected_category->slug.'_order', $order );
					 		//set parameters for design
					 		update_post_meta( $image_id, 'fpd_parameters', $_POST['parameters'][$order]);
					 		update_post_meta( $image_id, 'fpd_thumbnail', $_POST['thumbnail'][$order]);

					 		$order++;

					 	}

					 	update_option( 'fpd_category_parameters_'.$selected_category->slug, $_POST['fpd_category_options'] );

						echo '<div class="updated"><p><strong>'.__('Designs saved.', 'radykal').'</strong></p></div>';
					}

					//get category parametes
					$category_parameters = get_option( 'fpd_category_parameters_'.$selected_category->slug );

				?>

				<br class="clear" />
				<?php
				require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-edit-design-category-options.php');
				?>
				<form method="post" id="fpd-designs-form">
					<div>
						<p class="description"><?php _e('Categories', 'radykal'); ?></p>
						<select name="design_category" class="radykal-select2" style="width: 400px;">
							<?php
								foreach($categories as $category) {

									$parent_cat_str = get_category_parents($category->term_id, false, '%#%');
									$parent_cats_array = explode('%#%', $parent_cat_str);
									$level = max(sizeof($parent_cats_array) - 2, 0);

									$selected = '';
									//check if a category is selected
									if( isset($_GET['category_id']) && $_GET['category_id'] == $category->term_id) {
										$selected = 'selected="selected"';
									}

									//output category option
									echo '<option value="'.$category->term_id.'" '.$selected.'>'.str_repeat("- ", $level).$category->name.'</option>';

								}
							?>
						</select>
					</div>
					<div class="fpd-panel">
						<input type="hidden" value="<?php if( $category_parameters ) echo $category_parameters; ?>" name="fpd_category_options" />
					 	<a href="#" class="add-new-h2 fpd-add-designs"><?php _e('Add Designs', 'radykal'); ?></a>
					 	<a href="#" id="fpd-edit-category-options" class="add-new-h2"><?php _e('Edit Category Options', 'radykal'); ?></a>
					 	<div id="fpd-black-white-switcher" class="fpd-right">
						 	<a href="#" id="fpd-white"></a>
						 	<a href="#" id="fpd-black"></a>
					 	</div>

					 	<div class="inside">
						 	<ul id="fpd-designs-list" class="fpd-clearfix">
						 	<?php

							 //get designs by category id
							$args = array(
								'posts_per_page' => -1,
								'post_type' => 'attachment',
								'orderby' => 'menu_order',
								'order' => 'ASC',
								'fpd_design_category' => $selected_category->slug
							);

							$designs = get_posts( $args );

							//loop through all designs
							foreach( $designs as $design ) {

								$origin_image = wp_get_attachment_image_src( $design->ID, 'full' );
								$origin_image = $origin_image[0] ? $origin_image[0] : $design->guid;

								$parameters = get_post_meta($design->ID, 'fpd_parameters', true);
								$thumbnail = get_post_meta($design->ID, 'fpd_thumbnail', true);
								echo '<li><img src="'.$origin_image.'" /><a href="#" class="fpd-edit-parameters"><i class="fpd-admin-icon-settings"></i></a><a href="#" class="fpd-remove-design"><i class="fpd-admin-icon-close"></i></a><input type="hidden" value="'.$design->ID.'" name="image_ids[]" /><input type="hidden" value="'.$parameters.'" name="parameters[]" /><input type="hidden" value="'.$thumbnail.'" name="thumbnail[]" /></li>';

							}
						 	?>
						 	</ul>
					 	</div>

					 </div>
					<?php wp_nonce_field( 'fpd_save_designs'); ?>
					<input type="submit" name="save_designs"  value="<?php _e('Save Changes', 'radykal'); ?>" class="button button-primary" />
				</form>

			</div>
			<?php

		}

		private static function category_loop( $categories ) {

			foreach( $categories as $category ) {

				$category_children = isset($category->children) && sizeof($category->children) ? $category->children : null;

				echo self::get_category_item_html(
					$category->term_id,
					$category->name,
					get_option( 'fpd_category_thumbnail_url_'.$category->term_id, '' ),
					$category_children
				);

			}

		}

		public static function get_category_item_html( $id, $title, $thumbnail='', $category_children=null ) {

			if( !empty($thumbnail) ) {
				$thumbnail = '<img src="'.$thumbnail.'" />';
			}

			$children_html = '';
			if( $category_children ) {

				$children_html .= '<ul>';

				ob_start();
				self::category_loop( $category_children );
				$output = ob_get_contents();
				ob_end_clean();
				$children_html .= $output;

				$children_html .= '</ul>';

			}

			ob_start();
			?>

			<li id="<?php echo $id; ?>">
				<div class="fpd-clearfix">
					<span class="fpd-clearfix">
						<span class="fpd-item-thumbnail fpd-admin-tooltip" title="<?php _e('Category Thumbnail', 'radykal'); ?>">
							<span class="fpd-remove-item-thumbnail">
								<span class="dashicons dashicons-minus"></span>
							</span>
							<?php echo $thumbnail; ?>
						</span>
						<span class="fpd-category-title"><?php echo $title; ?></span>
					</span>
					<span class="fpd-category-actions fpd-clearfix">
						<a href="#" class="fpd-edit-category-title fpd-admin-tooltip" title="<?php _e('Edit Title', 'radykal'); ?>">
							<i class="fpd-admin-icon-mode-edit"></i>
						</a>
						<a href="#" class="fpd-delete-category fpd-admin-tooltip" title="<?php _e('Delete', 'radykal'); ?>">
							<i class="fpd-admin-icon-bin"></i>
						</a>
						<a href="#" class="fpd-collapse-list fpd-admin-tooltip" title="<?php _e('Collapse Toggle', 'radykal'); ?>">
							<i class="dashicons dashicons-minus"></i>
							<i class="dashicons dashicons-plus"></i>
						</a>
					</span>
				</div>
				<?php echo $children_html; ?>
			</li>

			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}
	}
}

new FPD_Admin_Designs();

?>