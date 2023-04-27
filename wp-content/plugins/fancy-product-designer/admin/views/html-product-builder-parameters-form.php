<div class="radykal-tabs radykal-disabled">
	<div class="radykal-tabs-nav">
		<a href="general-options" class="current"><?php _e('General', 'radykal'); ?></a>
		<a href="color-options"><?php _e('Colors', 'radykal'); ?></a>
		<a href="modifications-options"><?php _e('Modifications', 'radykal'); ?></a>
		<a href="bb-options"><?php _e('Bounding Box', 'radykal'); ?></a>
		<a href="text-options"><?php _e('Text', 'radykal'); ?></a>
		<a href="upload-zone-options"><?php _e('Upload Zone', 'radykal'); ?></a>
	</div>
	<form role="form" id="fpd-elements-form" class="radykal-tabs-content radykal-form">

		<!-- Hidden inputs for parameters set are set to true by default -->
		<input type="checkbox" name="locked" value="1" class="fpd-hidden" />
		<input type="checkbox" name="uploadZone" value="1" class="fpd-hidden" />

		<div data-id="general-options" class="current radykal-columns-two">

			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e('Position', 'radykal'); ?></th>
						<td>
							<label><?php _e('Left', 'radykal'); ?>: <input type="number" step="1" name="left" placeholder="0" value="" class="fpd-only-numbers"></label>
							<br />
							<label><?php _e('Top', 'radykal'); ?>: <input type="number" step="1" name="top" placeholder="0" value="" class="fpd-only-numbers"></label>
						</td>
					</tr>
					<tr>
						<th><?php _e('Scale', 'radykal'); ?></th>
						<td id="fpd-scale">
							<label><?php _e('X', 'radykal'); ?>: <input type="number" step="0.01" name="scaleX" placeholder="1" value="" class="fpd-only-numbers fpd-allow-dots fpd-text-hidden"></label>
							<br />
							<label class="radykal-disabled"><?php _e('Y', 'radykal'); ?>: <input type="number" step="0.01" name="scaleY" placeholder="1" value="" class="fpd-only-numbers fpd-allow-dots fpd-text-hidden"></label>
							<i class="fpd-admin-icon-lock" id="fpd-scale-locker"></i>
						</td>
					</tr>
					<tr>
						<th><?php _e('Angle', 'radykal'); ?></th>
						<td>
							<div class="radykal-input-slider radykal-clearfix">
								<div></div>
								<input type="number" step="1" min="0" max="359" name="angle" placeholder="0" value="0" class="fpd-only-numbers fpd-upload-zone-hidden" />
							</div>

						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<?php _e('Price', 'radykal'); ?>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Always use a dot as the decimal separator!', 'radykal'); ?>"></i>
						</th>
						<td>
							<input type="number" step="0.01" min="0" name="price" placeholder="0" value="" class="fpd-prevent-whitespace fpd-only-numbers fpd-allow-dots">
						</td>
					</tr>
					<tr>
						<th>
							<?php _e('Replace', 'radykal'); ?>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Elements with the same replace value are replaced by each other.', 'radykal'); ?>"></i>
						</th>
						<td>
							<input type="text" name="replace" value="" class="fpd-upload-zone-hidden large-text">
						</td>
					</tr>
					<tr>
						<th><?php _e('X-Axis Reference Point', 'radykal'); ?></th>
						<td>
							<span class="fpd-originX">
								<a href="#" class="fpd-originX-left button" data-value="left">
									<i class="fpd-admin-icon-originX-left"></i>
								</a>
								<a href="#" class="fpd-originX-center button" data-value="center">
									<i class="fpd-admin-icon-originX-center"></i>
								</a>
								<a href="#" class="fpd-originX-right button" data-value="right">
									<i class="fpd-admin-icon-originX-right"></i>
								</a>
								<input type="hidden" name="originX" value="center" class="fpd-radio-buttons" />
							</span>
						</td>
					</tr>
					<tr>
						<th><?php _e('Y-Axis Reference Point', 'radykal'); ?></th>
						<td>
							<span class="fpd-originY">
								<a href="#" class="fpd-originX-left button" data-value="top">
									<i class="fpd-admin-icon-originY-top"></i>
								</a>
								<a href="#" class="fpd-originX-center button" data-value="center">
									<i class="fpd-admin-icon-originY-center"></i>
								</a>
								<a href="#" class="fpd-originX-right button" data-value="bottom">
									<i class="fpd-admin-icon-originY-bottom"></i>
								</a>
								<input type="hidden" name="originY" value="center" class="fpd-radio-buttons" />
							</span>
						</td>
					</tr>
				</tbody>
			</table>

		</div><!-- General Options -->

		<div data-id="color-options">

			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<?php _e('Colorpicker for every path', 'radykal'); ?>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Every path in the SVG gets an own colorpicker', 'radykal'); ?>"></i>
						</th>
						<td>
							<label><input type="radio" name="colors" value="1" class="fpd-svg-options" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="colors" value="0" checked="checked" class="fpd-svg-options" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr class="fpd-color-options">
						<th>
							<?php _e('Available Colors', 'radykal'); ?>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('One color value: Colorpicker, Multiple color values: Fixed color palette', 'radykal'); ?>"></i>
						</th>
						<td>
							<input type="text" name="colors" class="tm-input" value="" placeholder="<?php _e('e.g. #000000,#ffffff', 'radykal') ; ?>" size="20" />
							<a href="#" class="button button-secondary" id="fpd-add-color"><?php _e('Add', 'radykal') ; ?></a>

						</td>
					</tr>
					<tr class="fpd-color-options">
						<th>
							<?php _e('Color Link Group', 'radykal'); ?>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('You can set color links between elements.', 'radykal'); ?>"></i>
						</th>
						<td>
							<input type="text" name="colorLinkGroup" size="25" value="" class="fpd-upload-zone-hidden" />
						</td>
					</tr>
					<tr class="fpd-color-options">
						<th><?php _e('Current Color', 'radykal'); ?></th>
						<td>
							<input type="text" name="fill" value="" placeholder="<?php _e('e.g. #000000', 'radykal') ; ?>" class="radykal-color-picker" />
						</td>
					</tr>
					<tr>
						<th><?php _e('Opacity', 'radykal'); ?></th>
						<td>
							<div class="radykal-input-slider radykal-clearfix">
								<div></div>
								<input type="number" name="opacity" step="0.01" min="0" max="1" placeholder="1" class="fpd-only-numbers fpd-allow-dots" value="">
							</div>
						</td>
					</tr>
				</tbody>
			</table>

		</div><!--- color options -->

		<div data-id="modifications-options" class="radykal-columns-two">

			<table class="form-table">
				<tbody>
					<tr>
						<th><label><?php _e('Removable', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="removable" value="1" class="fpd-upload-zone-hidden" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="removable" value="0" checked="checked" class="fpd-upload-zone-hidden" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Draggable', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="draggable" value="1"/> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="draggable" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Rotatable', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="rotatable" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="rotatable" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Resizable', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="resizable" value="1" class="fpd-text-box-hidden" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="resizable" value="0" checked="checked" class="fpd-text-box-hidden" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table">
				<tbody>
					<tr>
						<th><label><?php _e('Layer Position Unlockable', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="zChangeable" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="zChangeable" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Stay On Top', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="topped" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="topped" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Auto-Select', 'radykal'); ?></label></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="autoSelect" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="autoSelect" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><?php _e('Uni-Scaling Unlockable', 'radykal'); ?></th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="uniScalingUnlockable" value="1" class="fpd-text-hidden" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="uniScalingUnlockable" value="0" checked="checked" class="fpd-text-hidden" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e('Exclude From Export', 'radykal'); ?></label>
							<br>
							<i style="font-size: 10px;"><?php _e('Only available when viewing orders with the new <a href="http://admin.fancyproductdesigner.com" target="_blank">ADMIN solution</a>!', 'radykal'); ?></i>
						</th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="excludeFromExport" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="excludeFromExport" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
				</tbody>
			</table>

		</div><!-- Modifications Options -->

		<div data-id="bb-options">

			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="opt-bounding_box_control"><?php _e('Use another element as bounding box', 'radykal'); ?></label></th>
						<td>
							<input type="checkbox" name="bounding_box_control" id="opt-bounding_box_control" value="1" data-checkedsel="#bounding-box-element" data-uncheckedsel="#boundig-box-custom">
						</td>
					</tr>
					<tr>
						<th><?php _e('Define Bounding Box', 'radykal'); ?></th>
						<td>
							<div id="boundig-box-custom">
								<label><?php _e('X', 'radykal'); ?>:</label>
								<input type="number" name="bounding_box_x" size="3" placeholder="0" style="margin-right: 15px;" value="">
								<label><?php _e('Y', 'radykal'); ?>:</label>
								<input type="number" name="bounding_box_y" size="3" placeholder="0" value="">
								<label><?php _e('Width', 'radykal'); ?>:</label>
								<input type="number" name="bounding_box_width" size="3" placeholder="0" style="margin-right: 15px;" value="">
								<label><?php _e('Height', 'radykal'); ?>:</label>
								<input type="number" name="bounding_box_height" size="3" placeholder="0" value="">
							</div>
							<input type="text" id="bounding-box-element" name="bounding_box_by_other" size="40" placeholder="<?php _e('Title of an image element in the same view.', 'radykal'); ?>" style="display: none;" value="" />
						</td>
					</tr>
					<tr>
						<th><?php _e('Mode', 'radykal'); ?></th>
						<td>
							<select name="boundingBoxMode">
								<option value="inside" selected=""><?php _e('Inside', 'radykal'); ?></option>
								<option value="clipping"><?php _e('Clipping', 'radykal'); ?></option>
								<option value="limitModify"><?php _e('Limit Modification', 'radykal'); ?></option>
								<option value="none"><?php _e('None', 'radykal'); ?></option>
							</select>
						</td>
					</tr>

				</tbody>
			</table>

		</div><!-- Bounding Box Options -->

		<div data-id="text-options" class="radykal-columns-two">

			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e('Font', 'radykal'); ?></th>
						<td>
							<select name="fontFamily" data-placeholder="<?php _e('Select a font', 'radykal'); ?>" class="fpd-font-changer radykal-select2" style="width: 100%">
								<?php
								foreach(FPD_Fonts::get_enabled_fonts() as $font) {
									echo "<option value='$font' style='font-family: $font;'>$font</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php _e('Styling', 'radykal'); ?></th>
						<td>
							<span class="fpd-text-styling" style="margin-right: 20px;">
								<a href="#" class="fpd-bold button" data-value="bold">
									<i class="fpd-admin-icon-format-bold"></i>
								</a>
								<a href="#" class="fpd-italic button" data-value="italic">
									<i class="fpd-admin-icon-format-italic"></i>
								</a>
								<a href="#" class="fpd-underline button" data-value="underline">
									<i class="fpd-admin-icon-format-underline"></i>
								</a>
								<input type="checkbox" name="fontWeight" value="bold" data-unchecked="normal" class="fpd-hidden fpd-toggle-button" />
								<input type="checkbox" name="fontStyle" value="italic" data-unchecked="normal" class="fpd-hidden fpd-toggle-button" />
								<input type="checkbox" name="textDecoration" value="underline" data-unchecked="normal" class="fpd-hidden fpd-toggle-button" />
							</span>
						</td>
					</tr>
					<tr>
						<th><?php _e('Multiline Alignment', 'radykal'); ?></th>
						<td>
							<span class="fpd-text-align">
								<a href="#" class="fpd-align-left button" data-value="left">
									<i class="fpd-admin-icon-format-align-left"></i>
								</a>
								<a href="#" class="fpd-align-center button" data-value="center">
									<i class="fpd-admin-icon-format-align-center"></i>
								</a>
								<a href="#" class="fpd-align-right button" data-value="right">
									<i class="fpd-admin-icon-format-align-right"></i>
								</a>
								<a href="#" class="fpd-align-justify button" data-value="justify">
									<i class="fpd-admin-icon-format-align-justify"></i>
								</a>
								<input type="hidden" name="textAlign" value="left" class="fpd-radio-buttons" />
							</span>
						</td>
					</tr>
					<tr>
						<th><?php _e('Font Size', 'radykal'); ?></th>
						<td><input type="number" name="fontSize" min="1" step="1" placeholder="18" value="" class="fpd-only-numbers"></td>
					</tr>
					<tr>
						<th><?php _e('Line Height', 'radykal'); ?></th>
						<td><input type="number" name="lineHeight" min="0.1" step="0.01" placeholder="1" value="" class="fpd-only-numbers fpd-allow-dots"></td>
					</tr>
					<tr>
						<th><?php _e('Letter Spacing', 'radykal'); ?></th>
						<td><input type="number" name="letterSpacing" min="0" step="1" placeholder="0" value="" class="fpd-only-numbers"></td>
					</tr>
					<tr>
						<th><?php _e('Maximum Characters', 'radykal'); ?></th>
						<td><input type="number" name="maxLength" min="1" step="1" placeholder="0" value="" class="fpd-only-numbers"></td>
					</tr>
					<tr>
						<th><?php _e('Maximum Lines', 'radykal'); ?></th>
						<td><input type="number" name="maxLines" min="1" step="1" placeholder="0" value="" class="fpd-only-numbers"></td>
					</tr>
				</tbody>
			</table>

			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e('Stroke', 'radykal'); ?></th>
						<td class="">
							<div>
								<input type="text" name="stroke" value="" placeholder="<?php _e('e.g. #000000', 'radykal') ; ?>" class="radykal-color-picker" />
							</div>
							<div class="radykal-input-slider radykal-clearfix">
								<div></div>
								<input type="number" name="strokeWidth" min="0" max="50" step="0.1" placeholder="0" value="" class="fpd-only-numbers fpd-allow-dots" />
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e('Editable', 'radykal'); ?></label>
						</th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="editable" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="editable" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e('Charge After Editing', 'radykal'); ?></label>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('If the text has price, it will be charged first after the text has been edited.', 'radykal'); ?>"></i>
						</th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="chargeAfterEditing" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="chargeAfterEditing" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<!--<tr>
						<th>
							<label><?php _e('Patternable', 'radykal'); ?></label>
						</th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="patternable" value="1" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="patternable" value="0" checked="checked" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>-->
					<tr>
						<th>
							<label><?php _e('Curvable', 'radykal'); ?></label>
							<i class="fpd-admin-icon-info-outline fpd-admin-tooltip" title="<?php _e('Allow customer to switch between curvable and normal text.', 'radykal'); ?>"></i>
						</th>
						<td class="radykal-radio-group-inline">
							<label><input type="radio" name="curvable" value="1" class="fpd-text-box-hidden" /> <?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="curvable" value="0" checked="checked" class="fpd-text-box-hidden" /> <?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr class="fpd-curved-text-opts">
						<th>
							<?php _e('Curved Text Spacing', 'radykal'); ?>
						</th>
						<td>
							<input type="checkbox" name="curved" value="1" class="fpd-hidden">
							<input type="number" name="curveSpacing" min="0" step="0.1" placeholder="10" class="fpd-only-numbers" value="">
						</td>
					</tr>
					<tr class="fpd-curved-text-opts">
						<th>
							<?php _e('Curved Text Radius', 'radykal'); ?>
						</th>
						<td>
							<input type="number" name="curveRadius"  min="0" step="0.1" placeholder="80" value="" class="fpd-only-numbers">
						</td>
					</tr>
					<tr class="fpd-curved-text-opts">
						<th>
							<label for="opt-curveReverse"><?php _e('Curved Text Reverse', 'radykal'); ?></label>
						</th>
						<td>
							<input type="checkbox" name="curveReverse" id="opt-curveReverse" value="1">
						</td>
					</tr>
					<tr class="fpd-text-box-opts">
						<th>
							<?php _e('Text Box Width', 'radykal'); ?>
						</th>
						<td>
							<input type="checkbox" name="textBox" value="1" class="fpd-hidden">
							<input type="number" name="height" class="fpd-hidden" value="">
							<input type="number" name="width" min="0" step="1" class="fpd-only-numbers" value="">
						</td>
					</tr>
					<?php do_action('fpd_product_builder_text_options_end'); ?>
				</tbody>
			</table>

		</div><!-- Text Options -->

		<!-- Upload Zone Options -->
		<div data-id="upload-zone-options">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label><?php _e('Image Uploads', 'radykal'); ?></label></th>
						<td class="radio-group">
							<label><input type="radio" name="adds_uploads" value="1" checked=""><?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="adds_uploads" value="0"><?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Custom Texts', 'radykal'); ?></label></th>
						<td class="radio-group">
							<label><input type="radio" name="adds_texts" value="1" checked=""><?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="adds_texts" value="0"><?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><label><?php _e('Designs', 'radykal'); ?></label></th>
						<td class="radio-group">
							<label><input type="radio" name="adds_designs" value="1" checked=""><?php _e('Yes', 'radykal'); ?></label>
							<label><input type="radio" name="adds_designs" value="0"><?php _e('No', 'radykal'); ?></label>
						</td>
					</tr>
					<tr>
						<th><?php _e('Scale Mode', 'radykal'); ?></th>
						<td>
							<select name="scaleMode">
								<option value="fit" selected=""><?php _e('Fit', 'radykal'); ?></option>
								<option value="cover"><?php _e('Cover', 'radykal'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</form>
</div><!-- tabs content -->
