<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Modal') ) {

	class FPD_Admin_Modal {

		public static function output_header($id, $title='', $desc='', $css_classes='') {

			?>
			<div class="fpd-admin-modal-wrapper <?php echo $css_classes; ?>" id="<?php echo $id; ?>">

				<div class="fpd-admin-modal-dialog">
					<div class="fpd-ui-blocker"></div>
					<a href="#" class="fpd-close-admin-modal">&times;</a>
					<h2><?php echo $title; ?></h2>
					<p class="description"><?php echo $desc; ?></p>
					<div class="fpd-admin-modal-content">
			<?php

		}

		public static function output_footer($primaryBtnText=false) {

			?>
					</div><!-- modal content -->
					<?php if( $primaryBtnText !== false ): ?>
					<div class="fpd-admin-modal-btns">
						<button class="button button-primary fpd-save-admin-modal"><?php echo $primaryBtnText; ?></button>
					</div>
					<?php endif; ?>
				</div><!-- modal dialog -->
			</div><!-- modal wrapper -->
			<?php

		}

	}

}

?>