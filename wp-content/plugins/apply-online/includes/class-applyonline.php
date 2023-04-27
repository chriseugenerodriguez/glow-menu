<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    Applyonline
 * @subpackage Applyonline/includes
 * @author     Farhan Noor
 */
class Applyonline {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Applyonline_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'apply-online';
		$this->version = '1.6.3';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

                add_action( 'init', array( $this, 'register_aol_post_types' ), 1 );
                add_action('init', array($this, 'after_plugin_update'));
                new Applyonline_Shortcodes();
                new Applyonline_AjaxHandler();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Applyonline_Loader. Orchestrates the hooks of the plugin.
	 * - Applyonline_i18n. Defines internationalization functionality.
	 * - Applyonline_Admin. Defines all hooks for the admin area.
	 * - Applyonline_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-applyonline-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-applyonline-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-applyonline-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-applyonline-public.php';

		$this->loader = new Applyonline_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Applyonline_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Applyonline_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Applyonline_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Applyonline_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Applyonline_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
        
        function after_plugin_update(){
            require_once plugin_dir_path( __FILE__ ).'class-applyonline-activator.php';
            $saved_version = (float)get_option('aol_version', 0);
            if($saved_version < 1.6) {
                Applyonline_Activator::bug_fix_before_16();
            }
            
            if($saved_version < 1.61){
                Applyonline_Activator::fix_roles();
                update_option('aol_version', $this->get_version(), TRUE);                
            }
        }
        
        /*
         * @todo    make label of the CPT editable from plugin settings so user can show his own title on the archive page
         */
        public function register_aol_post_types(){
            $slug = get_option('aol_slug', 'ads');
            if(empty($slug)) $slug = 'ads';
            /*Register Main Post Type*/
            $labels=array(
                'add_new'  => 'Create Ad',
                'add_new_item'  => 'New Ad',
                'edit_item'  => 'Edit Ad',
                'all_items' => 'All Ads',
                'menu_name' => 'Apply Online'
            );
            $args=array(
                'label' => __( 'All Ads', 'applyonline' ),
                'labels'=> $labels,
                'public'=>  true,
                'show_in_nav_menus' => false,
                'capability_type'   => array('ad', 'ads'),
                'map_meta_cap'      => TRUE,
                'has_archive'   => true,
                'menu_icon'  => 'dashicons-admin-site',
                'description' => __( 'Ad Posting' ),
                'supports' => array('editor', 'excerpt', 'title'),
                'rewrite' => array('slug'=>  $slug),
            );
            register_post_type('aol_ad',$args);

            // Add new taxonomy, make it hierarchical (like categories)
            $labels = array(
                    'name'              => _x( 'Categories', 'taxonomy plural name',  'applyonline' ),
                    'singular_name'     => _x( 'Category','taxonomy singular name', 'applyonline' ),
                    'search_items'      => __( 'Search Categories' ),
                    'all_items'         => __( 'All Categories' ),
                    'parent_item'       => __( 'Parent Category' ),
                    'parent_item_colon' => __( 'Parent Category:' ),
                    'edit_item'         => __( 'Edit Category' ),
                    'update_item'       => __( 'Update Category' ),
                    'add_new_item'      => __( 'Add New Category' ),
                    'new_item_name'     => __( 'New Category Name' ),
            );

            $args = array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => array( 'slug' => 'careers' ),
            );

            register_taxonomy( 'aol_ad_category', array( 'aol_ad' ), $args ); 
            
            /*Register Applications Post Type*/
            $lables= array(
                'edit_item'=>'Application'
                );
            $args=array(
                'label' => __( 'Applications', 'applyonline' ),
                'labels' => $lables,
                'show_ui'           => true,
                'exclude_from_search'=> true,
                'capability_type'   => array('application', 'applications'),
                'show_in_menu' => 'edit.php?post_type=aol_ad',
                'description' => __( 'List of Applications with their resume', 'applyonline' ),
                'supports' => array('comments', 'editor'),
                'capabilities' => array( 'create_posts' => 'do_not_allow'),
                'map_meta_cap'      => TRUE,
        );
            register_post_type('aol_application',$args);
        }
        
}

class Applyonline_Shortcodes{
    function __construct() {
        add_shortcode( 'aol', array($this, 'aol') ); //archive of ads.
        add_shortcode( 'aol_ads', array($this, 'aol') ); //Backward compatibility to show ads archive.
        add_shortcode( 'aol_ad', array($this, 'aol_ad') ); //Single ad with form.
        add_shortcode( 'aol_form', array($this, 'aol_form') ); //Single ad form only.
    }
    
        /**
         * Shortcode Generator
         * @param type $atts
         * @return type
         */
        function aol( $atts ) {
            $a = shortcode_atts( array(
                'categories' => NULL,
                'ads' => NULL,
                'excerpt' => 'yes',
                'per_page' => '-1'
            ), $atts );

            $args=array(
                'posts_per_page'=> $a['per_page'],
                'post_type'     =>'aol_ad',
            );
            if(isset($a['categories'])) {
                $args['tax_query'] = array(
                        array('taxonomy' => 'aol_ad_category', 'terms'    => explode(',',$atts['categories']))
                    );
            }

            if(isset($a['ads'])) {
                $args['post__in'] = explode(',',$atts['ads']);
            }            

            query_posts( $args );
            function custom_excerpt_more( $more ) {
                return '....';
            }
            add_filter( 'excerpt_more', 'custom_excerpt_more' );
            ob_start();
            echo '<ol class="aol_ad_list">';
            if(have_posts()): while(have_posts()): the_post();
            ?>
                <li>
                    <strong><?php the_title(); ?></strong>
                    <?php if($a['excerpt'] == 'yes') the_excerpt(); ?>
                    <a href="<?php the_permalink() ?>" class="read-more">Read More</a>
                </li>
            <?php
            endwhile; endif;
            echo '</ol>';
            $html=ob_get_clean();
            wp_reset_query();
            return $html;
        }            
        
        function aol_form( $atts ) {
            $a = shortcode_atts( array(
                'id'   => NULL,
            ), $atts );

            if(isset($a['id'])) {
                echo aol_form($atts['id']);
            }    
        }         
        
        /*
         * @todo: this function should print complete ad with application form.
         */
        function aol_ad( $atts ) {
            $a = shortcode_atts( array(
                'id'   => NULL,
            ), $atts );

            if(isset($a['id'])) {
                //echo aol_form($atts['id']);
            }    
        }
}

/**
  * This class is responsible to hanld Ajax data.
  * 
  * 
  * @since      1.0
  * @package    AjaxHandler
  * @author     Farhan Noor
  **/
 class Applyonline_AjaxHandler{
        public function __construct() {
            add_action( 'wp_ajax_aol_app_form', array($this, 'aol_process_app_form') );
            add_action( 'wp_ajax_nopriv_aol_app_form', array($this, 'aol_process_app_form') );
        }
                
        public function aol_process_app_form(){
            $nonce=$_POST['wp_nonce'];
            if(!wp_verify_nonce($nonce, 'the_best_aol_ad_security_nonce')) die(json_encode( array( 'success' => false, 'error' => 'Session Expired, pease try again' )));

            /*Initialixing Variables*/
            $error = null;
            $error_assignment = null;
            
            //Check for required fields
            $form = get_post_meta($_POST['ad_id']);
            $app_fields = array();
            foreach($form as $key => $val){
                if(substr($key, 0, 9) == '_aol_app_'){
                    $app_fields[$key] = unserialize($val[0]);
                    if($app_fields[$key]['type'] == 'email'){
                        if(is_email(sanitize_email($_POST[$key]))==FALSE) $errors .= str_replace('_',' ', substr($key, 9)).' is invalid.<br />';
                    }
                    elseif($app_fields[$key]['type'] == 'file'){
                        if((!isset($app_fields[$key]['required']) and empty($_FILES[$key]['name'])) OR ($app_fields[$key]['required'] == '1' and empty($_FILES[$key]['name']))) $errors .= str_replace('_',' ', substr($key, 9)).' is required.<br />';
                    }
                    else{
                        if((!isset($app_fields[$key]['required']) and empty($_POST[$key])) OR ($app_fields[$key]['required'] == '1' and empty($_POST[$key]))) $errors .= str_replace('_',' ', substr($key, 9)).' is required.<br />';
                    }
                }
            }
            if($errors != NULL ) die(json_encode(array( 'success' => false, 'error' => $errors )));
            //End - Check for required fields
            

            if (!empty($_FILES)) {
                $upload_size = get_option('aol_upload_max_size', 1);
                if(empty($upload_size)) $upload_size = 1 ;

                $upload_folder = 'uploads/aol_ad';
                $upload_folder = apply_filters('aol_upload_folder', $upload_folder);
                
                $var_cp_assigment_type = get_option('aol_allowed_file_types');
                if(empty($var_cp_assigment_type)) $var_cp_assigment_type = 'jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt';
                
                foreach($_FILES as $key => $val):
                    /*Check if this file attachment is required or optional*/
                    $field = get_post_meta($_POST['ad_id'], $_POST[$key], true);
                    if($field['required'] == '1' AND $val == NULL)  die(json_encode(array('success'=>false, 'error'=> str_replace('_',' ', substr($key, 9)).' attachment is required<br />')));
                    /*END - Check if this file attachment is required or optional*/
                    $uploadfiles = $val;
                    if (is_array($uploadfiles)) {
                            $upload_dir = wp_upload_dir();
                            $time = (!empty($_SERVER['REQUEST_TIME'])) ? $_SERVER['REQUEST_TIME'] : (time() + (get_option('gmt_offset') * 3600)); // Fallback of now

                            $timestamp = strtotime(date('Y m d H i s'));
                            if($upload_folder){
                                $upload_dir = array(
                                    'path' => WP_CONTENT_DIR . '/' . $upload_folder,
                                    'url' => WP_CONTENT_URL . '/' . $upload_folder,
                                    'subdir' => '',
                                    'basedir' => WP_CONTENT_DIR . '/uploads',
                                    'baseurl' => WP_CONTENT_URL . '/uploads',
                                    'error' => false,
                                );
                             }
                            if(!is_dir($upload_dir['path'])) wp_mkdir_p($upload_dir['path']);

                            // look only for uploded files
                            if ($val['error'] == 0) {
                                $filetmp = $val['tmp_name'];
                                $filename = sanitize_file_name( $val['name'] );
                                $filesize = $val['size'];
                                $filetype = wp_check_filetype ( $filename );
                                $file_ext = strtolower($filetype['ext']);

                                $max_upload_size = $upload_size*1048576; //Multiply by KBs
                                if($max_upload_size < $filesize){
                                        $assignment_error[] = 'Maximum upload allowed file size is '.$upload_size.'MB';
                                        $error_assignment = 1;
                                }

                                $file_type_match = 0;
                                $var_cp_assigment_type_array = array();
                                 if($var_cp_assigment_type){
                                         $var_cp_assigment_type_array = explode(',',$var_cp_assigment_type);
                                }
                                if(in_array($file_ext, $var_cp_assigment_type_array)){
                                    $file_type_match = 1;
                                }
                                 /**
                                 * Check File Size
                                 */
                                if($file_type_match == 0){
                                    $assignment_error[] = 'Please upload file with one of the extentions: '.$var_cp_assigment_type;
                                    $error_assignment = 1;
                                }
                                // get file info
                                // @fixme: wp checks the file extension....
                                $filetype = wp_check_filetype( basename( $filename ), null );
                                $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
                                $filename = $filetitle . $timestamp . '.' . $filetype['ext'];
                                /**
                                 * Check if the filename already exist in the directory and rename the
                                 * file if necessary
                                 */
                                $i = 0;
                                while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
                                  $filename = $filetitle . $timestamp . '_' . $i . '.' . $filetype['ext'];
                                  $i++;
                                }
                                $filedest = $upload_dir['path'] . '/' . $filename;
                                /**
                                 * Check write permissions
                                 */
                                if ( !is_writeable( $upload_dir['path'] ) ) {
                                  $assignment_error[] = 'Unable to write to directory %s. Is this directory writable by the server?';
                                  $error_assignment = 1;
                                }
                                /**
                                 * Save temporary file to uploads dir
                                 */
                                if($error_assignment <> 1){
                                    if ( ! move_uploaded_file($filetmp, $filedest) ){
                                      $assignment_error[] = "Error, the file $filetmp could not move to : $filedest";
                                      $error_assignment = 1; 
                                    }
                                    
                                    else{
                                        $newupload = $upload_dir['url'].'/'.$filename;
                                        $uploadpath = $upload_dir['path'].'/'.$filename;
                                        
                                        //Seting post meta.
                                        $_POST[$key] = $newupload;
                                    }
                                }
                            }
                      }
                    
                endforeach;
            }
            $errors = NULL;
            if($error_assignment == 1){
                foreach($assignment_error as $error_value){
                        $errors .= esc_html__($error_value, 'applyonline').'<br />';
                }
                $response = json_encode( array( 'success' => false, 'error' => $errors ));    // generate the response.
                
                // response output
                header( "Content-Type: application/json" );
                die($response);
                exit;
            }
                
            else{
                $args=  array(
                    'post_type'     =>'aol_application',
                    'post_content'  =>'',
                    'post_parent'    =>$_POST['ad_id'],
                    'post_title'    =>get_the_title($_POST['ad_id']),
                    'post_status'   =>'publish',
                );
                $pid = wp_insert_post($args);

                foreach($_POST as $key => $val):
                    if(substr($key,0,9) == '_aol_app_') add_post_meta($pid, $key, wp_strip_all_tags ($val));
                endforeach;

                if($pid>0){
                    //send email alert.
                    $post_url = admin_url("post.php?post=$pid&action=edit");
                    
                    $admin_email = get_option('admin_email');
                    $emails_raw = get_option('aol_recipients_emails', $admin_email);
                    $emails = explode("\n", $emails_raw);
                    
                    $subject = 'Application Alert';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    
                    //@todo need a filter hook to modify content of this email message and to add a from field in the message.
                    $message = "<p>Hi,</p>"
                            . "<p>You just received an application against an add at ".get_bloginfo('url'). ".</p>"
                            . "<p> <b><a href='".$post_url."'>Click Here</a></b> to view this application.</p>"
                            . "<p>".  site_url()."</p>";
                    $message = apply_filters('aol_email_alert_message', $message, $pid);
                    add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
                    wp_mail( $emails, $subject, $message, $headers);
                    remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
                    
                    $divert_page = get_option('aol_thankyou_page');
                    
                    empty($divert_page) ? $divert_link = null :  $divert_link = get_page_link($divert_page);
                    
                    $response = json_encode( array( 'success' => true, 'divert' => $divert_link, 'message'=>get_option('aol_application_message') ));    // generate the response.
                }
                
                else $response = json_encode( array( 'success' => false ));    // generate the response.

                if($error) $response = json_encode( array( 'success' => false, 'error' => $error ));    // generate the response with error message.

                // response output
                header( "Content-Type: application/json" );
                echo $response;

                exit;
            }
        }
        
        function set_html_content_type() {
            return 'text/html';
        }
        
        private function sanitize_post_array(&$value,$key){
            $value = sanitize_text_field($value);
        }
        
        public function save_setting_template(){
            // Check the user's permissions.

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;

            } else {

                    if ( ! current_user_can( 'edit_post', $post_id ) ) {
                            return;
                    }
            }

            /* OK, it's safe for us to save the data now. */

            //Delete fields.
            $old_keys = "SELECT $wpdb->options WHERE option_name like '_aol_app_%'";
            $new_keys = array_keys($_POST);
            $removed_keys = array_diff($old_keys, $new_keys); //List of removed meta keys.
            foreach($removed_keys as $key => $val):
                if(substr($val, 0, 3) == '_ad') delete_post_meta($post_id, $val); //Remove meta from the db.
            endforeach;

            array_walk($_POST[$key], array($this, 'sanitize_post_array')); //Sanitizing each element of the array            
            // Add new value.
            foreach ($_POST as $key => $val):
                // Make sure that it is set.
                if ( substr($key, 0, 13)=='_aol_feature_' and isset( $val ) ) {
                    //Sanitize user input.
                    update_post_meta( $post_id, sanitize_key($key),  sanitize_text_field( $val )); // Add new value.
                }

                // Make sure that it is set.
                elseif ( substr($key, 0, 9)=='_aol_app_' and isset( $val ) ) {
                    $my_data = serialize($val); 
                    update_post_meta( $post_id, sanitize_key($key),  $my_data); // Add new value.
                }
                    //Update the meta field in the database.
            endforeach;
        }  
}