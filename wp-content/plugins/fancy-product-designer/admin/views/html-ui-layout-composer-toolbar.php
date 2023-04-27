<div id="fpd-composer-toolbar" class="fpd-panel">

	<div class="radykal-tabs">
		<div class="radykal-tabs-nav">
			<a href="layout" class="current"><?php _e('Layout', 'radykal'); ?></a>
			<a href="modules"><?php _e('Modules', 'radykal'); ?></a>
			<a href="actions"><?php _e('Actions', 'radykal'); ?></a>
			<a href="toolbar"><?php _e('Toolbar', 'radykal'); ?></a>
			<a href="colors"><?php _e('Colors', 'radykal'); ?></a>
			<a href="custom-css"><?php _e('Custom CSS', 'radykal'); ?></a>
		</div>

		<div class="radykal-tabs-content radykal-form">

			<div data-id="layout" class="current">

				<div class="radykal-columns-two">

					<div>
						<h4><?php _e('Main Bar', 'radykal'); ?></h4>
						<div class="radykal-form-group radykal-columns-three" id="fpd-mainbar-layout">

							<div>
								<label for="layout-topbar" class="radykal-checkbox-image">
									<input type="radio" name="layout" value="fpd-topbar" id="layout-topbar" class="radykal-hidden" checked="">
									<i><?php _e('Top Bar', 'radykal') ?></i>
									<img src="<?php echo plugins_url('/admin/img/topbar.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
								</label>
							</div>

							<div>
								<label for="layout-sidebar" class="radykal-checkbox-image">
									<input type="radio" name="layout" value="fpd-sidebar fpd-tabs" id="layout-sidebar" class="radykal-hidden">
									<i><?php _e('Side Bar Left', 'radykal') ?></i>
									<img src="<?php echo plugins_url('/admin/img/sidebar-left.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
								</label>
							</div>

							<div>
								<label for="layout-sidebar-right" class="radykal-checkbox-image">
									<input type="radio" name="layout" value="fpd-sidebar fpd-sidebar-right fpd-tabs" id="layout-sidebar-right" class="radykal-hidden">
									<i><?php _e('Side Bar Right', 'radykal') ?></i>
									<img src="<?php echo plugins_url('/admin/img/sidebar-right.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
								</label>
							</div>

						</div><!-- main bar -->

						<div id="fpd-topbar-layout">
							<h4><?php _e('Top Bar Layout', 'radykal'); ?></h4>
							<div class="radykal-form-group radykal-columns-three">

								<div>
									<label for="topbar-dynamic" class="radykal-checkbox-image">
										<input type="radio" name="topbar_layout" value="fpd-dynamic-dialog" id="topbar-dynamic" class="radykal-hidden" checked="">
										<i><?php _e('Dynamic Dialog', 'radykal') ?></i>
										<img src="<?php echo plugins_url('/admin/img/topbar-dynamic.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
									</label>
								</div>
								<div>
									<label for="topbar-off-canvas-left" class="radykal-checkbox-image">
										<input type="radio" name="topbar_layout" value="fpd-off-canvas-left" id="topbar-off-canvas-left" class="radykal-hidden" checked="">
										<i><?php _e('Off-Canvas Left', 'radykal') ?></i>
										<img src="<?php echo plugins_url('/admin/img/topbar-off-canvas-left.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
									</label>
								</div>
								<div>
									<label for="topbar-off-canvas-right" class="radykal-checkbox-image">
										<input type="radio" name="topbar_layout" value="fpd-off-canvas-right" id="topbar-off-canvas-right" class="radykal-hidden" checked="">
										<i><?php _e('Off-Canvas Right', 'radykal') ?></i>
										<img src="<?php echo plugins_url('/admin/img/topbar-off-canvas-right.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
									</label>
								</div>

							</div>
						</div><!-- top bar layouts -->

						<div id="fpd-sidebar-tabs-position">
							<h4><?php _e('Side Bar Tabs Position', 'radykal'); ?></h4>
							<div class="radykal-form-group radykal-columns-three">

								<div>
									<label for="sidebar-tabs-left" class="radykal-checkbox-image">
										<input type="radio" name="sidebar_tabs_position" value="fpd-tabs-side" id="sidebar-tabs-left" class="radykal-hidden" checked="">
										<i><?php _e('Tabs Side', 'radykal') ?></i>
										<img src="<?php echo plugins_url('/admin/img/sidebar-tabs-left.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
									</label>
								</div>
								<div>
									<label for="sidebar-tabs-top" class="radykal-checkbox-image">
										<input type="radio" name="sidebar_tabs_position" value="fpd-tabs-top" id="sidebar-tabs-top" class="radykal-hidden">
										<i><?php _e('Tabs Top', 'radykal') ?></i>
										<img src="<?php echo plugins_url('/admin/img/sidebar-tabs-top.png', FPD_PLUGIN_ADMIN_DIR); ?>" alt="" />
									</label>
								</div>

							</div>
						</div><!-- side bar tabs position -->

					</div><!-- first col in layout -->

					<div>

						<div class="radykal-columns-two">

							<div class="radykal-form-group">
								<h4><?php _e('Dimensions', 'radykal'); ?></h4>
								<label for="stageWidth">
									<i><?php _e('Canvas Width', 'radykal') ?>:</i>
									<input type="number" id="stageWidth" value="<?php echo $selected_layout['plugin_options']['stageWidth']; ?>" />
								</label>
								<label for="stageHeight">
									<i><?php _e('Canvas Height', 'radykal') ?>:</i>
									<input type="number" id="stageHeight" value="<?php echo $selected_layout['plugin_options']['stageHeight']; ?>" />
								</label>
							</div>

							<div class="radykal-form-group">
								<h4><?php _e('Container Shadow', 'radykal'); ?></h4>
								<select name="shadow" >
									<?php
									foreach(FPD_Settings_General::get_frame_shadows() as $key => $shadow) {
										echo '<option value="'.$key.'">'.$shadow.'</option>';
									}
									?>
								</select>
							</div>

						</div>

						<div class="radykal-columns-two">

							<div class="radykal-form-group">
								<h4>
									<?php _e('Image Grid Columns', 'radykal'); ?>
									<br />
									<span class="description"><?php _e('Used in products & designs module', 'radykal'); ?></span>
								</h4>
								<select name="grid_columns">
									<option value="1"><?php _e('One', 'radykal'); ?></option>
									<option value="2"><?php _e('Two', 'radykal'); ?></option>
									<option value="3"><?php _e('Three', 'radykal'); ?></option>
									<option value="4"><?php _e('Four', 'radykal'); ?></option>
									<option value="5"><?php _e('Five', 'radykal'); ?></option>
								</select>
							</div>

							<div class="radykal-form-group">
								<h4><?php _e('Initial Active Module', 'radykal'); ?></h4>
								<select name="initial_active_module">
									<option value=""><?php _e('None', 'radykal'); ?></option>
								</select>
							</div>

						</div>

						<div class="radykal-form-group radykal-radio-group-inline">
							<h4><?php _e('View Selection Position', 'radykal'); ?></h4>
							<label>
								<input type="radio" name="views_selection_pos" value="fpd-views-inside-top" >
								<i><?php _e('Inside Top', 'radykal'); ?></i>
							</label>
							<label>
								<input type="radio" name="views_selection_pos" value="fpd-views-inside-right" >
								<i><?php _e('Inside Right', 'radykal'); ?></i>
							</label>
							<label>
								<input type="radio" name="views_selection_pos" value="fpd-views-inside-bottom" >
								<i><?php _e('Inside Bottom', 'radykal'); ?></i>
							</label>
							<label>
								<input type="radio" name="views_selection_pos" value="fpd-views-inside-left" checked="" >
								<i><?php _e('Inside Left', 'radykal'); ?></i>
							</label>
							<label>
								<input type="radio" name="views_selection_pos" value="fpd-views-outside" >
								<i><?php _e('Outside', 'radykal'); ?></i>
							</label>
						</div>

					</div><!-- second col in layout -->

				</div>

			</div><!-- layout content -->

			<div data-id="modules">

				<div class="radykal-columns-two">

					<div>

						<h4><?php _e('Your Selected Modules', 'radykal'); ?></h4>
						<p class="description"><?php _e('These modules will be visible in your main navigation.', 'radykal'); ?></p>
						<div class="radykal-segment">
							<div class="radykal-dropzone" data-zone="top">
								<span class="radykal-dropzone-placeholder"><?php _e('Drop Modules Here', 'radykal'); ?></span>
							</div>
						</div>
						<p>
							<i class="dashicons dashicons-info"></i>
							<span class="description"><?php _e('Double-click on an item to remove it from the dropzone.', 'radykal'); ?></span>
						</p>

					</div><!-- left col in modules -->

					<div>

						<h4><?php _e('Available Modules', 'radykal'); ?></h4>
						<p class="description"><?php _e('Drag desired modules to dropzone.', 'radykal'); ?></p>
						<div class="radykal-segment" id="fpd-available-modules"></div>

					</div><!-- right col  in modules -->

				</div>

			</div>

			<div data-id="actions">

				<div class="radykal-columns-two">

					<div>

						<h4><?php _e('Your Selected Actions', 'radykal'); ?></h4>
						<div id="fpd-actions-dropzones" class="radykal-segment">

							<div class="radykal-dropzone" data-zone="top">
								<span class="radykal-dropzone-placeholder"><?php _e('Drop Actions here', 'radykal'); ?></span>
							</div>

							<div class="radykal-dropzone" data-zone="right">
								<span class="radykal-dropzone-placeholder"><?php _e('Drop Actions here', 'radykal'); ?></span>
							</div>

							<div class="radykal-dropzone" data-zone="bottom">
								<span class="radykal-dropzone-placeholder"><?php _e('Drop Actions here', 'radykal'); ?></span>
							</div>

							<div class="radykal-dropzone" data-zone="left">
								<span class="radykal-dropzone-placeholder"><?php _e('Drop Actions here', 'radykal'); ?></span>
							</div>

						</div>
						<p>
							<i class="dashicons dashicons-info"></i>
							<span class="description"><?php _e('Double-click on an item to remove it from the dropzone.', 'radykal'); ?></span>
						</p>

					</div><!-- left col in actions -->

					<div>

						<div class="radykal-form-group">
							<h4><?php _e('Available Actions', 'radykal'); ?></h4>
							<div class="radykal-segment" id="fpd-available-actions"></div>
							<p class="description"><?php _e('Drag desired actions to dropzone', 'radykal'); ?></p>
						</div>

						<div id="fpd-actions-alignment">
							<h4><?php _e('Alignment', 'radykal'); ?></h4>
							<div class="radykal-segment">
								<div>
									<i><?php _e('Top Actions', 'radykal'); ?></i>
									<label>
										<input type="radio" name="top_actions_align" class="fpd-class-toggle-radio" checked="" value="" ><?php _e('Left', 'radykal'); ?>
									</label>
									<label>
										<input type="radio" name="top_actions_align" class="fpd-class-toggle-radio" value="fpd-top-actions-centered"><?php _e('Center', 'radykal'); ?>
									</label>
								</div>
								<div>
									<i><?php _e('Right Actions', 'radykal'); ?></i>
									<label>
										<input type="radio" name="right_actions_align" class="fpd-class-toggle-radio" checked="" value="" ><?php _e('Top', 'radykal'); ?>
									</label>
									<label>
										<input type="radio" name="right_actions_align" class="fpd-class-toggle-radio" value="fpd-right-actions-centered"><?php _e('Center', 'radykal'); ?>
									</label>
								</div>
								<div>
									<i><?php _e('Bottom Actions', 'radykal'); ?></i>
									<label>
										<input type="radio" name="bottom_actions_align" class="fpd-class-toggle-radio" checked="" value="" ><?php _e('Left', 'radykal'); ?>
									</label>
									<label>
										<input type="radio" name="bottom_actions_align" class="fpd-class-toggle-radio" value="fpd-bottom-actions-centered"><?php _e('Center', 'radykal'); ?>
									</label>
								</div>
								<div>
									<i><?php _e('Left Actions', 'radykal'); ?></i>
									<label>
										<input type="radio" name="left_actions_align" class="fpd-class-toggle-radio" checked="" value="" ><?php _e('Top', 'radykal'); ?>
									</label>
									<label>
										<input type="radio" name="left_actions_align" class="fpd-class-toggle-radio" value="fpd-left-actions-centered"><?php _e('Center', 'radykal'); ?>
									</label>
								</div>
							</div>
						</div>

					</div><!-- right col in actions -->

				</div>

			</div>

			<div data-id="toolbar">

				<div class="radykal-columns-two">

					<div>
						<h4><?php _e('Exclude Tools', 'radykal'); ?></h4>
						<select name="toolbar_exclude_tools[]" class="radykal-select2" multiple style="width: 100%;">
						</select>
					</div><!-- left col in modules -->
					<div>

						<h4><?php _e('Placement', 'radykal'); ?></h4>
						<select name="toolbar_placement">
							<option value="dynamic"><?php _e('Dynamic', 'radykal'); ?></option>
							<option value="inside-top"><?php _e('Inside Top', 'radykal'); ?></option>
							<option value="inside-bottom"><?php _e('Inside Bottom', 'radykal'); ?></option>
						</select>

					</div><!-- right col in modules -->

				</div>

			</div>

			<div data-id="colors">

				<h4><?php _e('Create an own color scheme.', 'radykal'); ?></h4>
				<div class="radykal-columns-two radykal-segment">

					<div>
						<div>
							<p class="description"><?php _e('Primary', 'radykal'); ?></p>
							<input type="text" name="primary_color" class="fpd-color-picker" value="<?php echo $selected_layout['css_colors']['primary_color']; ?>" />
						</div>
						<div>
							<p class="description"><?php _e('Secondary', 'radykal'); ?></p>
							<input type="text" name="secondary_color" class="fpd-color-picker" value="<?php echo $selected_layout['css_colors']['secondary_color']; ?>" />
						</div>
						<div>
							<p class="description"><?php _e('Element Boundary', 'radykal'); ?></p>
							<input type="text" name="element_boundary_color" class="fpd-color-picker" value="<?php echo $selected_layout['plugin_options']['selectedColor']; ?>" />
						</div>
					</div>
					<div>
						<div>
							<p class="description"><?php _e('Bounding Box', 'radykal'); ?></p>
							<input type="text" name="bounding_box_color" class="fpd-color-picker" value="<?php echo $selected_layout['plugin_options']['boundingBoxColor']; ?>" />
						</div>
						<div>
							<p class="description"><?php _e('Out Of Bounding Box', 'radykal'); ?></p>
							<input type="text" name="out_of_bounding_box_color" class="fpd-color-picker" value="<?php echo $selected_layout['plugin_options']['outOfBoundaryColor']; ?>" />
						</div>
						<div>
							<p class="description"><?php _e('Corner Control Icons', 'radykal'); ?></p>
							<input type="text" name="corner_control_icons_color" class="fpd-color-picker" value="<?php echo $selected_layout['plugin_options']['cornerIconColor']; ?>" />
						</div>
					</div>

				</div>
				<button class="button-secondary radykal-disabled" id="fpd-update-preview"><?php _e('Update Preview', 'radykal'); ?></button>
				<div class="fpd-ui-blocker"></div>

			</div>

			<div data-id="custom-css">
				<h4><?php _e('You can add custom CSS styles to the pages where the product designer is included.', 'radykal'); ?></h4>
				<span><?php _e('Helpful CSS classes:', 'radykal'); ?></span>
				<ul>
					<li><code>.fpd-container</code> - <?php _e('The product designer container.', 'radykal'); ?></li>
					<li><code>.fpd-product-designer-wrapper</code> - <?php _e('Wrapper around the product designer container.', 'radykal'); ?></li>
					<li><code>.fpd-mainbar</code> - <?php _e('The main bar container.', 'radykal'); ?></li>
				</ul>
				<div class="radykal-segment">
					<div class="radykal-ace-editor" id="fpd-custom-css"><?php echo $selected_layout['custom_css']; ?></div>
				</div>

			</div>

		</div><!-- tabs content -->

	</div> <!-- radykal tabs -->

</div><!-- #fpd-composer-toolbar -->