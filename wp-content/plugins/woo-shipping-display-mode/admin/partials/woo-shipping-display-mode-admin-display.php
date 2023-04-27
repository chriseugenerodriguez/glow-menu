<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin/partials
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class WC_Settings_Shipping_Display_Mode_Methods {

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->hooks();
    }

    /**
     * Class hooks.
     *
     * @since 1.0.0
     */
    public function hooks() {
        // Add WC settings tab
        add_filter('woocommerce_settings_tabs_array', array($this, 'settings_tab'), 70);

        // Settings page contents
        add_action('woocommerce_settings_tabs_shipping_mode', array($this, 'settings_page'));

        // Save settings page
        add_action('woocommerce_update_options_shipping_mode', array($this, 'update_options'));
    }

    /**
     * Settings tab.
     *
     * Add a WooCommerce settings tab for the Receiptful settings page.
     *
     * @since 1.0.0
     *
     * @param 	array	$tabs 	Array of default tabs used in WC.
     * @return 	array 			All WC settings tabs including newly added.
     */
    public function settings_tab($tabs) {

        $tabs['shipping_mode'] = __('Woo Shipping Display Mode', 'woocommerce-shipping-display-mode');

        return $tabs;
    }

    /**
     * Settings page content.
     *
     * @since 1.0.0
     */
    public function settings_page() {
        global $wpdb;
        if (!defined('ABSPATH')) {
            exit;
        }
        ?>
        <?php
        //wp_register_script('jquery-ui-core', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js', false, '1.8.2');
        // wp_deregister_script( 'jquery-ui-core' );
        wp_enqueue_script('jquery-ui-core', get_stylesheet_directory_uri() . '/jquery.ui.core.min.js', array('jquery'), '1.9.2', 1);
        $current_user = wp_get_current_user();
        $shipping_method = '';
        $shiiping_method_get_value = get_option('woocommerce_shipping_method_format');
        if ($shiiping_method_get_value == 'select') {
            $shipping_method == 'select';
        }
        if ($shiiping_method_get_value == 'redio') {
            $shipping_method == 'redio';
        }
        ?>
        <h3><?php printf(__('Woo Shipping Display Mode', 'woocommerce')); ?></h3>
        <table class="form-table" id="shipping_display_table">
            <th scope="row" class="titledesc">
                <label for="woocommerce_shipping_method_format">Woo Shipping Display Mode</label>
                <img class="help_tip" src="<?php echo site_url(); ?>/wp-content/plugins/woocommerce/assets/images/help.png" height="16" width="16" data-tip="<?php echo 'Display shipping methods with Radio buttons and Dropdowns'; ?>">
            </th>
            <td class="forminp forminp-radio">
                <fieldset>
                    <ul>
                        <li>
                            <label><input name="woocommerce_shipping_method_format" value="radio" type="radio" style="" class=""  <?php
                                if ($shiiping_method_get_value == 'radio') {
                                    echo 'checked="checked"';
                                }
                                ?>> Display shipping methods with "radio" buttons</label>
                        </li>
                        <li>
                            <label><input name="woocommerce_shipping_method_format" value="select" type="radio" style="" class=" "  <?php
                                if ($shiiping_method_get_value == 'select') {
                                    echo 'checked="checked"';
                                }
                                ?> > Display shipping methods in a dropdown</label>
                        </li>
                    </ul>
                </fieldset>
            </td>
            <table> 
                <?php
                if (!get_option('wsdm_plugin_notice_shown')) {
                    echo '<div id="wsdm_dialog" title="Basic dialog"> <p> Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free! </p> <p><input type="text" id="txt_user_sub_wsdm" class="regular-text" name="txt_user_sub_wsdm" value="' . $current_user->user_email . '"></p></div>';
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function() {
                            jQuery("#wsdm_dialog").dialog({
                                modal: true, title: 'Subscribe Now', zIndex: 10000, autoOpen: true,
                                width: '500', resizable: false,
                                position: {my: "center", at: "center", of: window},
                                dialogClass: 'dialogButtons',
                                buttons: {
                                    Yes: function() {
                                        // $(obj).removeAttr('onclick');
                                        // $(obj).parents('.Parent').remove();
                                        var email_id = jQuery('#txt_user_sub_wsdm').val();

                                        var data = {
                                            'action': 'add_plugin_user_wsdm',
                                            'email_id': email_id
                                        };

                                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                        jQuery.post(ajaxurl, data, function(response) {
                                            jQuery('#wsdm_dialog').html('<h2>You have been successfully subscribed');
                                            jQuery(".ui-dialog-buttonpane").remove();
                                        });


                                    },
                                    No: function() {
                                        var email_id = jQuery('#txt_user_sub_wsdm').val();

                                        var data = {
                                            'action': 'hide_subscribe_wsdm',
                                            'email_id': email_id
                                        };

                                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                        jQuery.post(ajaxurl, data, function(response) {

                                        });

                                        jQuery(this).dialog("close");

                                    }
                                },
                                close: function(event, ui) {
                                    jQuery(this).remove();
                                }
                            });
                            jQuery("div.dialogButtons .ui-dialog-buttonset button").addClass("button-primary woocommerce-save-button");
                            jQuery("div.dialogButtons .ui-dialog-buttonpane .ui-button").css("width", "80px");
                            jQuery("div.dialogButtons .ui-dialog-buttonset button").removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only");

                        });
                    </script>
                    <?php
                }
            }

            /**
             * Save settings.
             *
             * Save settings based on WooCommerce save_fields() method.
             *
             * @since 1.0.0
             */
            public function update_options() {
                global $woocommerce, $post, $wpdb;
                if (!empty($_POST['woocommerce_shipping_method_format'])) {
                    $display_mode = $_POST['woocommerce_shipping_method_format'];
                    update_option('woocommerce_shipping_method_format', $display_mode, '', 'yes');
                }
            }

            /**
             * Save Shipping Display Mode
             *
             * @since 1.0.0
             */
            public function save_shiping_display_mode() {
                global $woocommerce, $post, $wpdb;
                $shipping_method = $_POST['woocommerce_shipping_method_format'];
            }

        }
        