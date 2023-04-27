<div id="fpd-order-panel">
	<div id="fpd-order-designer-wrapper">

		<!-- Product Designer Container -->

		<div id="fpd-order-designer-wrapper">
			<div id="fpd-order-designer" class="fpd-views-outside fpd-container fpd-top-actions-centered fpd-topbar fpd-off-canvas-left"></div>
		</div>

		<!-- Tabs -->
		<div class="radykal-tabs">

			<div class="radykal-tabs-nav">
				<a href="export" class="current"><?php _e('Export', 'radykal'); ?></a>
				<a href="single-elements"><?php _e('Single Elements', 'radykal'); ?></a>
				<a href="misc"><?php _e('Misc', 'radykal'); ?></a>
				<?php do_action( 'fpd_order_viewer_tabs_end' ); ?>
			</div>

			<div class="radykal-tabs-content">

				<div data-id="export" class="current radykal-columns-two">

					<div>
						<table class="form-table">
							<tr>
								<th><?php _e('Output File', 'radykal' ); ?></th>
								<td>
									<label>
										<input type="radio" name="fpd_output_file" value="pdf" checked="checked" /><?php _e('PDF', 'radykal' ); ?>
									</label>
									<label>
										<input type="radio" name="fpd_output_file" value="image" /><?php _e('IMAGE', 'radykal' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php _e('Image Format', 'radykal' ); ?></th>
								<td>
									<label>
										<input type="radio" name="fpd_image_format" value="png" checked="checked" />PNG
									</label>
									<label>
										<input type="radio" name="fpd_image_format" value="jpeg" />JPEG
									</label>
									<label>
										<input type="radio" name="fpd_image_format" value="svg" />SVG
										<i class="fpd-admin-tooltip fpd-admin-icon-info-outline" title="<?php _e( 'Exporting as SVG format allows to create a multi-layer PDF. Bounding box clippings are ignored!', 'radykal' ); ?>"></i>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php _e('View(s)', 'radykal' ); ?></th>
								<td>
									<label>
										<input type="radio" name="fpd_export_views" value="all" checked="checked" /> <?php _e('ALL', 'radykal' ); ?>
									</label>
									<label>
										<input type="radio" name="fpd_export_views" value="current" /> <?php _e('CURRENT SHOWING', 'radykal' ); ?>
									</label>
								</td>
							</tr>
						</table>
						<button id="fpd-generate-file" class="button button-primary"><?php _e( 'Create', 'radykal' ); ?></button>
						<p class="description"><?php _e( 'The created pdfs will be stored in: ', 'radykal' ); ?><br /><?php echo content_url('/fancy_products_orders/pdfs'); ?></p>
					</div>

					<div>
						<table class="form-table">
							<tr>
								<th>
									<?php _e('Size', 'radykal' ); ?><br />
									<a href="http://www.pixelcalculator.com/" target="_blank" style="font-size: 11px;"><?php _e('DPI - Pixel Converter', 'radykal' ); ?></a>
								</th>
								<td>
									<label class="fpd-block">
										<input type="number" value="210" id="fpd-pdf-width" />
										<br />
										<?php _e('PDF width in mm', 'radykal' ); ?>
									</label>
									<label class="fpd-block">
										<input type="number" value="297" id="fpd-pdf-height" />
										<br />
										<?php _e('PDF height in mm', 'radykal' ); ?>
									</label>
									<label class="fpd-block">
										<input type="number" value="" name="fpd_scale" placeholder="1" />
										<br />
										<?php _e('Scale Factor', 'radykal' ); ?>
									</label>
									<label class="fpd-block">
										<input type="number" value="" id="fpd-pdf-dpi" placeholder="300" />
										<br />
										<?php _e('Image DPI', 'radykal' ); ?>
									</label>
								</td>
							</tr>
							<tr>
								<th>
									<?php _e('Include Text Summary', 'radykal' ); ?><br />
								</th>
								<td>
									<label>
										<input type="checkbox" id="fpd-pdf-include-text-summary" />
										<?php _e( 'Adds an additional page with information about all elements.', 'radykal' ); ?>
									</label>
								</td>
							</tr>
						</table>
					</div>

				</div><!-- Export -->

				<div data-id="single-elements">

					<h4><?php _e( 'Selected Element', 'radykal' ); ?></h4>
					<div id="fpd-editor-box-wrapper"></div>

					<h4><?php _e( 'Export Options', 'radykal' ); ?></h4>
					<div class="radykal-columns-two">

						<div>
							<table class="form-table">
								<tr>
									<th><?php _e('Image Format', 'radykal' ); ?></th>
									<td>
										<label>
											<input type="radio" name="fpd_single_image_format" value="png" checked="checked" /> PNG
										</label>
										<label>
											<input type="radio" name="fpd_single_image_format" value="jpeg" /> JPEG
										</label>
										<label>
											<input type="radio" name="fpd_single_image_format" value="svg" /> SVG
											<i class="fpd-admin-tooltip fpd-admin-icon-info-outline" title="<?php _e( 'When creating an SVG image with a text element, make sure that the font you are using is installed on your computer otherwise it will not be shown.', 'radykal' ); ?>"></i>
										</label>

									</td>
								</tr>
								<tr>
									<th><?php _e('Padding around exported element.', 'radykal' ); ?></th>
									<td>
										<input type="number" min="0" value="" name="fpd_single_element_padding" placeholder="0" />
									</td>
								</tr>
								<tr>
									<th><?php _e('DPI', 'radykal' ); ?></th>
									<td>
										<input type="number" min="0" value="" name="fpd_single_element_dpi" placeholder="72" />
									</td>
								</tr>
							</table>

							<button id="fpd-save-element-as-image" class="button button-primary"><?php _e( 'Create', 'radykal' ); ?></button>
						</div>
						<div>
							<table class="form-table">
								<tr>
									<td colspan="2">
										<label>
											<input type="checkbox" id="fpd-restore-oring-size" />
											<?php _e( 'Use origin size, that will set the scaling to 1, when exporting the image.', 'radykal' ); ?>
										</label>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<label>
											<input type="checkbox" id="fpd-save-on-server" />
											<?php _e( 'Save exported image on server.', 'radykal' ); ?>
											<i class="fpd-admin-tooltip fpd-admin-icon-info-outline" title="<?php _e( 'You can save all elements of the Product as an image on your server, to be stored in: ', 'radykal' ); echo content_url('/fancy_products_orders/images'); ?>"></i>
										</label>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<label>
											<input type="checkbox" id="fpd-without-bounding-box" />
											<?php _e( 'Export without bounding box clipping if element has one.', 'radykal' ); ?>
										</label>
									</td>
								</tr>
							</table>
						</div>

					</div><!-- export options -->

					<div id="fpd-elements-lists" class="fpd-clearfix">
						<div>
							<h4>
								<?php _e( 'Added By Customer', 'radykal' ); ?>
							</h4>

							<ul id="fpd-custom-elements-list"></ul>
						</div>
						<div>
							<h4><?php _e( 'Saved Images On Server', 'radykal' ); ?></h4>
							<ul id="fpd-order-image-list"></ul>
						</div>
					</div>

				</div><!-- Single Elements -->

				<div data-id="misc">
					<h4><?php _e('New Product', 'radykal' ); ?></h4>
					<p class="description"><?php _e( 'Create a new Product with the current showing views in the Order Viewer.', 'radykal' ); ?></p>
					<a href="#" class="button-primary" id="fpd-create-new-fp"><?php _e( 'Create', 'radykal' ); ?></a>
				</div>

				<?php do_action( 'fpd_order_viewer_tabs_content_end' ); ?>

				<div class="fpd-ui-blocker"></div>

			</div><!-- tabs content -->
		</div><!-- tabs -->
	</div><!-- wrapper -->
</div>