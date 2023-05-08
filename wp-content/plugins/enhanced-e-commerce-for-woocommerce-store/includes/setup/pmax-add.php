<?php
class TVC_PMaxAdd {
  protected $TVC_Admin_Helper="";
  protected $subscriptionId = "";
  //protected $google_detail;
  protected $site_url;
  protected $google_ads_id;
  protected $currency_code;
  protected $currency_symbol;
  protected $merchant_id;
  public function __construct() {
    $this->includes();
    $this->site_url = "admin.php?page=conversios-pmax";
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->PMax_Helper = new Conversios_PMax_Helper();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->merchant_id = $this->TVC_Admin_Helper->get_merchantId();
    //$this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data(); 
    $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
    if(isset($this->subscription_data->google_ads_id) && $this->subscription_data->google_ads_id != ""){
      $this->google_ads_id = $this->subscription_data->google_ads_id;
    }
    $currency_code_rs = $this->PMax_Helper->get_campaign_currency_code($this->google_ads_id);
    if(isset($currency_code_rs->data->currencyCode)){
      $this->currency_code = $currency_code_rs->data->currencyCode;
    }
    $this->currency_symbol = $this->TVC_Admin_Helper->get_currency_symbols($this->currency_code);
    if($this->google_ads_id){     
      $this->load_html();
    }
  }
  public function includes() {
    if (!class_exists('Conversios_PMax_Helper')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
    }   
  }

  public function load_html(){
    do_action('conversios_start_html_'.sanitize_text_field($_GET['page']));
    $this->current_html();
    $this->current_js();
    do_action('conversios_end_html_'.sanitize_text_field($_GET['page']));
  }

  public function country_dropdown($selected_code = '', $is_disabled = false) {
    $getCountris = file_get_contents(__DIR__ . "/json/countries.json");
    $contData = json_decode($getCountris);
    $wooCountry = $this->TVC_Admin_Helper->get_woo_country();
    $is_disabled = ($is_disabled) ? "style=pointer-events:none;background:#f2f2f2;" : "";
    $data = '<select name="target_country" id="target_country" class="slect2bx fromfiled"  '.esc_attr($is_disabled).'>';
    foreach ($contData as $key => $value) {
      $selected = ($value->code == $wooCountry) ? "selected='selected'" : "";
      $data .= "<option value=" . esc_attr($value->code) . " " . esc_attr($selected) . " >" . esc_attr($value->name) . "</option>";
    }
    $data .= "</select>";
    return $data;
  }

  public function current_html() { 
    //REMOVED, PAUSED, ENABLED
    ?>
    <div class="pmax-campaign add-pmax-campaign">
      <div class="mt24 whiteroundedbx dshreport-sec">
        <h3><?php esc_html_e("Performance Max campaign","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
        <a href="<?php echo esc_url_raw($this->site_url); ?>" class="btn-withborder"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/left-angle-arrow.svg"); ?>"alt="back"/> <?php esc_html_e("Back to List","enhanced-e-commerce-for-woocommerce-store"); ?></a>
        <div class="tabs">
          <?php /*<ul class="tabs-list">
              <li class="active"><a href="#tab1">Campaign Details</a></li>
              <li ><a href="#tab2">Link the Assets</a></li>
          </ul> */ ?>
          <div id="tab1" class="tab active">
            <div class="campaignformwrp">
              <form id="add-pmax-campaign-form" method="post">
                <div class="form-row campform-row">
                  <label><?php esc_html_e("Campaign Name *","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <input type="text" class="fromfiled" name ="campaign_name" id="campaign_name" placeholder="<?php esc_html_e("Enter Campaign Name","enhanced-e-commerce-for-woocommerce-store"); ?>" required>
                </div>
                <div class="form-row campform-row">
                  <label><?php esc_html_e("Daily Budget","enhanced-e-commerce-for-woocommerce-store"); ?> (<spn class="ga_currency_symbols"><?php echo esc_attr($this->currency_symbol); ?></spn>) *</label>
                  <input type="number" class="fromfiled" name="budget" id="budget" placeholder="<?php esc_html_e("Enter your budget","enhanced-e-commerce-for-woocommerce-store"); ?>" maxlength="120" required>
                </div>
                <div class="form-row campform-row">
                  <label><?php esc_html_e("Country *","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <?php echo $this->country_dropdown();?>
                </div>
                <div class="form-row-grp campform-row cmp_urls">  
                  <div class="form-col-4 mb1">
                     <label><?php esc_html_e("Site URL Key","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    <input type="text" class="fromfiled" name="site_key[]" placeholder="<?php esc_html_e("Key","enhanced-e-commerce-for-woocommerce-store"); ?>" maxlength="100" onblur="this.value=removeSpaces(this.value);">
                  </div>
                  <div class="form-col-8 mb1">
                    <label><?php esc_html_e("Site URL Value","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    <input type="text" class="fromfiled" name="site_url[]" placeholder="<?php esc_html_e("Site URL","enhanced-e-commerce-for-woocommerce-store"); ?>">
                  </div>
                </div>
                
                <div id="more_cmp_urls"></div>
                <div class="form-row campform-row">
                  <button type="button" id="add_more_cmp_urls" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  <?php esc_html_e("Add Site URLs","enhanced-e-commerce-for-woocommerce-store"); ?></button>
                </div> 
                <div class="form-row campform-row"> 
                  <label><?php esc_html_e("Target ROAS (%)","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <small>Formula: Conversion value ÷ ad spend x 100% = target ROAS percentage</small>
                  <input type="number" name="target_roas" class="fromfiled smtext" placeholder="<?php esc_html_e("Add Number","enhanced-e-commerce-for-woocommerce-store"); ?>"> 
                </div>
                <div class="form-row campform-row"> 
                  <label><?php esc_html_e("FINAL URL Suffix","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <input type="text" name="final_url_suffix" class="fromfiled smtext" placeholder="<?php esc_html_e("Add Suffix","enhanced-e-commerce-for-woocommerce-store"); ?>"> 
                </div>
                <div class="form-row campform-row"> 
                  <label><?php esc_html_e("Start Date","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <input type="text" name="start_date" id="start_date" autocomplete="off" class="fromfiled smtext datepicker"> 
                </div>
                <div class="form-row campform-row"> 
                  <label><?php esc_html_e("End Date","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <input type="text" name="end_date" id="end_date" autocomplete="off" class="fromfiled smtext datepicker"> 
                </div>
                <p class="label"><b>Status</b></p>
                <div class="form-row form-row-grp campform-row"> 
                  <input type="radio" checked="checked" class="radio" value="ENABLED" name="status" id="cmp_active">
                  <label class="radio-label" for="cmp_active"><?php esc_html_e("Enable","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  <input type="radio" class="radio" value="PAUSED" name="status" id="cmp_inactive">
                  <label class="radio-label" for="cmp_inactive"><?php esc_html_e("Pause","enhanced-e-commerce-for-woocommerce-store"); ?></label> 
                </div>
                <div class="campfooterbtn">
                  <input type="hidden" name="customer_id" value="<?php echo esc_attr($this->google_ads_id); ?>">
                  <input type="hidden" name="merchant_id" value="<?php echo esc_attr($this->merchant_id); ?>">              
                  <button type="submit" class="ppblubtn cretemrchntbtn"><?php esc_html_e("Save","enhanced-e-commerce-for-woocommerce-store"); ?></button>
                </div>  
              </form>
              <div class="alert-message" id="tvc_pmax_popup_box"></div>
              <div id="add_loading"></div>
            </div>
            
            <a href="<?php echo esc_url_raw($this->site_url); ?>" class="btn-withborder"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/left-angle-arrow.svg"); ?>"alt="back"/> <?php esc_html_e("Back to List","enhanced-e-commerce-for-woocommerce-store"); ?></a>
          </div>
          <?php /*<div id="tab2" class="tab">
            <h3>Link the Assets</h3>
            <div class="assetformwrp">
              <form action="#">
                <div class="form-row campform-row">
                    <label>Headline* </label>
                    <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                    <p class="note">Required three headlines and Add up to 5 headlines</p>
                    <input type="text" class="fromfiled" value="" placeholder="Add Headline 1*">
                    <p class="score-line" style="float: right;">0/30</p>
                </div>
                <div class="form-row campform-row">
                   <input type="text" class="fromfiled" value="" placeholder="Add Headline 2*">
                   <p class="score-line" style="float: right;">0/30</p>
                </div>
                <div class="form-row campform-row">
                    <input type="text" class="fromfiled" value="" placeholder="Add Headline 3*">
                    <p class="score-line" style="float: right;">0/30</p>
                </div> 
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Headline</button>
                </div>
                <div class="form-row campform-row">
                    <label>Long Headline*</label>
                    <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                    <p class="note">Min 1 Required</p>
                    <input type="text" class="fromfiled" value="" placeholder="Add Long Headline*">
                    <p class="score-line" style="float: right;">0/30</p>
                </div>
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Long Headline</button>
                </div>                               
                <div class="form-row campform-row">
                    <label>Description*</label>
                    <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                    <p class="note">Min. 2 descriptions required, maximum up to 60-90 character</p>
                    <input type="text" class="fromfiled" value="" placeholder="Add Description 1*">
                    <p class="score-line" style="float: right;">0/30</p>
                </div>
                <div class="form-row campform-row">
                    <input type="text" class="fromfiled" value="" placeholder="Add Description 2*">
                    <p class="score-line" style="float: right;">0/30</p>
                </div>
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Description</button>
                </div>
                <div class="form-row campform-row">
                    <label>Buisness Name*</label>
                    <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                    <input type="text" class="fromfiled" value="" placeholder="Add your buisness name">
                    <p class="score-line" style="float: right;">0/30</p>
                </div>
              </form>
                <div class="campfooterbtn">
                  <button type="button" class="btn-withborder">Previous
                  </button>
                  <button type="button" class="ppblubtn cretemrchntbtn">Submit
                  </button>
                </div>
            </div>
            <div class="assetsection">
              <form action="#">
                <p class="label" style="display: inline-block;"><b>Images</b></p>
                <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                <p class="note">Min 2 Images required</p>
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Images</button>
                </div>
                <div class="form-row campform-row">
                    <label>Square Image* (1:1)</label>
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/square-image.png');?>">
                </div>
                <p class="label"><b>Landscape Image* (1.91:1)</b></p>
                <div class="form-row campform-row">
                    <img class="img-upload" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/landscape-image.png');?>">
                    <img class="img-upload" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/landscape-image.png');?>">
                </div>
                <p class="label" style="display: inline-block;"><b>Logo* </b></p>
                <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
               <p class="note"> min 1 logo require</p>
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Logo</button>
                </div>
                <div class="form-row campform-row">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/square-image.png');?>">
                </div>
                <div class="form-row campform-row">
                    <label>Videos</label>
                    <img class="img-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon-help.png');?>" alt="">
                    <input type="text" class="fromfiled" value="" placeholder="Add Video URL*">
                </div>
                <div class="form-row campform-row">
                  <button type="button" class="btn-fade-blue">
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png');?>" alt="">
                  Add Video</button>
                </div>
              </form>
            </div>              
          </div>*/ ?>
          
        </div>
      </div>
    </div>
    <?php /*
    <script>
      $(document).ready(function(){
        $(".tabs-list li a").click(function(e){
            e.preventDefault();
        });

        $(".tabs-list li").click(function(){
            var tabid = $(this).find("a").attr("href");
            $(".tabs-list li,.tabs div.tab").removeClass("active");   // removing active class from tab and tab content
            $(".tab").hide();   // hiding open tab
            $(tabid).show();    // show tab
            $(this).addClass("active"); //  adding active class to clicked tab

        });
      });
    </script>*/ ?>
<?php
  }
  /**
   * Page custom js code
   *
   * @since    4.1.4
   */
  public function current_js(){
    /*ga_currency :'<?php echo esc_attr($this->ga_currency); ?>',*/
    ?>
    <script>
    function removeSpaces(string) {
     return string.split(' ').join('');
    }  
    jQuery( document ).ready(function() { 
    /*jQuery(".datepicker").datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      startDate: new Date(),
      format: "yyyy-mm-dd"
    });*/ 
      jQuery(function(){
        jQuery("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
        jQuery("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });
      });    
      jQuery(document).on('click','.remove-row', function(event){
        event.preventDefault();
        jQuery(this).parent().remove();
      });
      jQuery(document).on('click','#add_more_cmp_urls', function(event){
        event.preventDefault();
        //let parent_id = document.querySelector('#cmp_urls');
        var html = '<div class="form-row-grp campform-row add-more-url cmp_urls"><div class="form-col-4 mb1"><input type="text" class="fromfiled" name="site_key[]" placeholder="Key" maxlength="100" onblur="this.value=removeSpaces(this.value);"></div><div class="form-col-8 mb1"><input type="text" class="fromfiled" name="site_url[]" placeholder="Site URL"></div><span class="form-col-1 remove-row">X</span></div>';
        jQuery("#more_cmp_urls").append(html);
      });
      jQuery(document).on('submit','#add-pmax-campaign-form', function(event){
        event.preventDefault();
        var site_key = jQuery("input[name='site_key\\[\\]']").map(function(){return jQuery(this).val();}).get();
        var site_url = jQuery("input[name='site_url\\[\\]']").map(function(){return jQuery(this).val();}).get();
        //console.log(site_key);
        //console.log(site_url);
        // code
        var fdata = jQuery(this).serialize();
        //console.log(fdata);
        var post_data = {
          action:'create_pmax_campaign',
          tvc_data:fdata,
          site_key:site_key,
          site_url:site_url,
          conversios_nonce:'<?php echo wp_create_nonce( 'conversios_nonce' ); ?>'
        };
        jQuery(':input[type="submit"]').prop('disabled', true);
        jQuery("#add_loading").addClass("is_loading");
        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url: tvc_ajax_url,
          data: post_data,
          success: function (response) {
            jQuery(':input[type="submit"]').prop('disabled', false);
            jQuery("#add_loading").removeClass("is_loading");
            console.log(response);
            //tvc_helper.add_message("success", "this is test", false);
            if(response.error == false){
              tvc_helper.add_message("success",response.message);
              setTimeout(function(){
                window.location = '<?php echo esc_url_raw($this->site_url); ?>';
              });
            }else{
              if(response.errors != ""){
                tvc_helper.add_message("error",response.message);
              }
            }
          }
        });// ajax
      });

    });
    </script>
    <?php
  } 
}
?>