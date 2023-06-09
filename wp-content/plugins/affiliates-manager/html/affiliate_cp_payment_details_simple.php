<?php $model = $this->viewData['affiliate'];?>
<style type="text/css">
	div.detailsBlock
	{
		border: 1px solid #ddd;
		display: none;
		padding: 10px;
	}
</style>
<div class="wrap">

	 <h2><?php _e( 'Payment Details', 'affiliates-manager' ) ?></h2>
	 <h3><?php _e( 'Please provide your payment details.', 'affiliates-manager' ) ?></h3>
	 <p><?php _e( 'The following information will be used to disburse payments when your account reaches the minimum payout amount.', 'affiliates-manager' ) ?></p>

<?php
require_once WPAM_BASE_DIRECTORY . "/html/widget_form_errors_panel.php";
?>
	<form method="post" action="<?php echo esc_url($this->viewData['nextStepUrl'])?>" class="pure-form">
            <?php wp_nonce_field('affiliate_cp_submit_payment_details'); ?>
		<table class="pure-table">
			<tr>
				<td><label for="ddPaymentMethod"><?php _e( 'Method', 'affiliates-manager' ) ?></label> *</td>
				<td><select id="ddPaymentMethod" name="ddPaymentMethod">
					<?php foreach ($this->viewData['paymentMethods'] as $key => $val) {
						echo '<option value="'.esc_attr($key).'"';
						if ( isset( $this->viewData['request']['ddPaymentMethod'] ) && $this->viewData['request']['ddPaymentMethod'] == $key)
							echo ' selected="selected"';
						echo '>' . esc_html($val) . '</option>';
					}?>
				</select></td>
			</tr>
		</table>

		<br/>
		<div id="paypalDetails" class="detailsBlock">
			<img src="<?php echo WPAM_URL . "/images/icon_paypal.png"?>" />
			<table class="pure-table">
				<tr>
					<td><label for="txtPaypalEmail"><?php _e( 'PayPal E-Mail Address', 'affiliates-manager' ) ?></label> *</td>
					<td>
						<input id="txtPaypalEmail" type="text" name="txtPaypalEmail" size="30" value="<?php echo isset( $this->viewData['request']['txtPaypalEmail'] ) ? esc_attr($this->viewData['request']['txtPaypalEmail']) : '' ?>"/>
					</td>
				</tr>
			</table>
		</div>
                
                <div id="bankDetails" class="detailsBlock">
			<table width="500">
				<tr>
					<td width="200"><label for="txtBankDetails"><?php echo esc_html(get_option(WPAM_PluginConfig::$BankTransferInstructions)) ?></label> *</td>
					<td>
						<textarea id="txtBankDetails" name="txtBankDetails" class="large-text"><?php echo isset( $this->viewData['request']['txtBankDetails'] ) ? esc_textarea($this->viewData['request']['txtBankDetails']) : '' ?></textarea>
					</td>
				</tr>
			</table>
		</div>

                <div id="checkDetails" class="detailsBlock">
			<div style="float: left; width: 75px; height: 35px;">
				<img src="<?php echo WPAM_URL . "/images/bank-check.png"?>" />
			</div>
			<div style="padding-left: 10px; text-align: left; vertical-align: bottom;">
				<strong><?php _e( 'Paper Check', 'affiliates-manager' ) ?></strong>
			</div>

			<table class="pure-table">
				<tr>
					<td>
						<label for="txtCheckTo"><?php _e( 'Check Recipient', 'affiliates-manager' ) ?></label> *
					</td>
					<td>
						<input id="txtCheckTo" type="text" size="30" name="txtCheckTo" value="<?php echo isset( $this->viewData['request']['txtCheckTo'] ) ? esc_attr($this->viewData['request']['txtCheckTo']) : '' ?>" />
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<?php _e( 'Check will be mailed to the following address:', 'affiliates-manager' ) ?>
					</td>
				</tr>
			</table>

			<table class="pure-table">
				<tbody>
					<tr><td><?php _e( 'Recipient', 'affiliates-manager' ) ?></td>
					<td>
						<?php echo esc_html($model->firstName)?> <?php echo esc_html($model->lastName)?><br/>
						<?php echo esc_html($model->addressLine1)?><br />
						<?php if(strlen(trim($model->addressLine2)) > 0)
						{
							echo esc_html($model->addressLine2) . "<br />";
						}?>
						<?php echo esc_html($model->addressCity)?><?php if ($model->addressCountry == 'US')
						{
							echo ", " .esc_html($model->addressState);
						}?> <?php echo esc_html($model->addressZipCode)?><br/>
						<?php
                                                $countries = WPAM_Validation_CountryCodes::get_countries();
                                                echo  esc_html($countries[$model->addressCountry])?>
					</td>
					</tr>
				</tbody>
			</table>


		</div>

		<br />
		<div id="buttons" style="text-align: center;">
			<input type="submit" class="button-primary" name="submitButton" value="<?php _e( 'Submit Payment Details', 'affiliates-manager' ) ?>"/>
		</div>
	</form>
</div>

