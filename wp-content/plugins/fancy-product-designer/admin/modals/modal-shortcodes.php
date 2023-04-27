<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-shortcodes',
	__('Shortcodes', 'radykal'),
	''
);

?>
<table class="form-table radykal-form-settings">
	<thead>
		<tr>
			<td><?php _e('Shortcode', 'radykal'); ?></td>
			<td><?php _e('Attribute(s)', 'radykal'); ?></td>
		</tr>
	</thead>
	<tbody>
		<tr valign="top">
			<td>
				<textarea readonly="readyonly" id="fpd-sc-pd">[fpd] [fpd_form]</textarea>
				<p class="description"><?php _e('Place the product designer anywhere you want with these two shortcodes.', 'radykal'); ?></p>
			</td>
			<td>
				<h5><?php _e('Price Format (%d is the placeholder for the price)', 'radykal'); ?></h5>
				<input type="text" placeholder="<?php _e('e.g. $%d', 'radykal'); ?>" id="fpd-sc-pd-price" />
			</td>
		</tr>
		<tr valign="top">
			<td>
				<textarea readonly="readyonly" id="fpd-sc-action"></textarea>
				<p class="description"><?php _e('Place an action button anywhere in your page.', 'radykal'); ?></p>
			</td>
			<td id="fpd-action-attr">
				<div>
					<h5><?php _e('Type', 'radykal'); ?></h5>
					<select id="fpd-sc-action-type">
						<option disabled selected value><?php _e('Select Type', 'radykal'); ?></option>
					</select>
				</div>
				<div>
					<h5><?php _e('Layout', 'radykal'); ?></h5>
					<select id="fpd-sc-action-layout">
						<option disabled selected value><?php _e('Select Layout', 'radykal'); ?></option>
						<option value="icon-tooltip"><?php _e('Icon Tooltip', 'radykal'); ?></option>
						<option value="icon-text"><?php _e('Icon Text', 'radykal'); ?></option>
						<option value="text"><?php _e('Text', 'radykal'); ?></option>
					</select>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<?php

	FPD_Admin_Modal::output_footer();

?>
