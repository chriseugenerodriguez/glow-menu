<?php

/**
 * Fired during plugin activation
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Applyonline
 * @subpackage Applyonline/includes
 * @author     Farhan Noor <farhan.noor@yahoo.com>
 */
class Applyonline_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            
            //Register CPT here for proper Flush Rules.
            $slug = get_option('aol_slug', 'ads');
            if(empty($slug)) $slug = 'ads';
            register_post_type('aol_ad', array('has_archive' => true, 'rewrite' => array('slug'=>  $slug)));
            flush_rewrite_rules();
            
            $term = wp_insert_term( 
                    'Admission',
                    'aol_ad_category',
                    array(
                        'slug'=>'admission',
                        'description'=>'Ads for admission in the school'
                        )
                    );
            if(!is_wp_error($term)):
                wp_insert_term( 
                        'Business School',
                        'aol_ad_category',
                        array(
                            'slug'=>'business',
                            'description'=>'Online admissions of Business school',
                            'parent' => $term['term_id'],
                            )
                        );

                wp_insert_term( 
                        'Computer Scinces',
                        'aol_ad_category',
                        array(
                            'slug'=>'computer-science',
                            'description'=>'Online admissions of Computer Sciences department',
                            'parent' => $term['term_id'],
                            )
                        );
            endif;
            
            $term = wp_insert_term( 
                    'Career',
                    'aol_ad_category',
                    array(
                        'slug'=>'career',
                        'description'=>'Use this category as a job board'
                        )
                    );
            
            if(!is_wp_error($term)):
                wp_insert_term( 
                        'Finance Department',
                        'aol_ad_category',
                        array(
                            'slug'=>'finance-department',
                            'description'=>'All job ads of Finance department',
                            'parent' => $term['term_id'],
                            )
                        );
                wp_insert_term( 
                        'Marketing Department',
                        'aol_ad_category',
                        array(
                            'slug'=>'marketing-department',
                            'description'=>'All job ads of Marketing department',
                            'parent' => $term['term_id'],
                            )
                        );
            endif;
            
            //Insert default fields.
            $fields = array (
                '_aol_app_Name' => 
                array (
                  'type' => 'text',
                  'options' => '',
                ),
                '_aol_app_eMail' => 
                array (
                  'type' => 'text',
                  'options' => '',
                ),
            );
            if(!get_option('aol_default_fields')) update_option('aol_default_fields', $fields);
            
            //@Since 1.6.1
            if(!get_option('aol_application_message')) update_option('aol_application_message', 'Your application has been submitted successfully. We will get back to you very soon.');
             
            self::fix_roles();
            self::bug_fix_before_16();
        }
        
        static function fix_roles(){
            $caps = array(
                'delete_ads' =>TRUE,
                'delete_others_ads' =>TRUE,
                'delete_published_ads' =>TRUE,
                'edit_ads' =>TRUE,
                'edit_others_ads' =>TRUE,
                'edit_private_ads' =>TRUE,
                'edit_published_ads' =>TRUE,
                'publish_ads' =>TRUE,
                'read_private_ads' =>TRUE,
                'delete_applications' =>TRUE,
                'delete_others_applications'=>TRUE,
                'delete_published_applications' =>TRUE,
                'edit_applications'         =>TRUE,
                'edit_others_applications'  =>TRUE,
                'edit_private_applications' =>TRUE,
                'edit_published_applications'=>TRUE,
                'publish_applications'       =>FALSE,
                'create_applications'       =>FALSE,
                'read_private_applications' =>TRUE,
                'read'                      =>TRUE,
                );
            
            $role = get_role('administrator');            
            foreach($caps as $cap => $val){
                $role->add_cap( $cap ); 
            }

            remove_role('aol_manager');
            add_role('aol_manager', 'AOL Manager', $caps);
        }
        
        /**
         * This function fixes a bug in versions prior to 1.6
         * 
         * The Bug: Application form fields(Post Metas) were serialized twice before save. 
         * 
         * The Fix: Check each app form field and converts it from dual serilized to single serialized value.
         * 
         * @since 1.6
         * 
         */
        static function bug_fix_before_16(){
            global $wpdb;
            $fields = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ID=post_id WHERE post_type = 'aol_ad' AND meta_key LIKE '_aol_app_%'");
            foreach ($fields as $field){
                if (is_string(unserialize($field->meta_value))) update_post_meta ($field->post_id, $field->meta_key, unserialize(unserialize($field->meta_value)));
            }
        }

}
