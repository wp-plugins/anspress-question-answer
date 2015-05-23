<?php
/**
 * AnsPress.
 *
 * @package   AnsPress
 * @author    Rahul Aryan <admin@rahularyan.com>
 * @license   GPL-2.0+
 * @link      http://rahularyan.com
 * @copyright 2014 Rahul Aryan
 */


class anspress_view {

	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	 /**
     * Return an instance of this class.
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {
        
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 */
	private function __construct() {
		add_action( 'template_redirect', array($this, 'insert_views') );
	}

	public function insert_views($template){
		if(is_question())
			
			
				ap_insert_views(get_question_id(), 'question');
		
	}
}

/**
 * Insert view data in ap_meta table and update post meta ANSPRESS_VIEW_META
 * @param  integer $data_id
 * @param  string $type	
 * @return boolean
 */
function ap_insert_views($data_id, $type){
	if($type == 'question'){
		$userid = get_current_user_id();
		
		// log in DB only if not viewed before and not anonymous
		if(!ap_is_already_viewed(get_current_user_id(), get_question_id()) || $userid == 0)
			ap_add_meta($userid, 'post_view', $data_id, $_SERVER['REMOTE_ADDR'] );
		
		$view = ap_get_qa_views($data_id);

		$view = $view+1;

		update_post_meta( $data_id, ANSPRESS_VIEW_META, apply_filters('ap_insert_views', $view ));

		do_action('after_insert_views', $data_id, $view);

		return true;
	}
	return false;
}

function ap_get_qa_views($id = false){	
	if(!$id) $id = get_the_ID();
	$views = get_post_meta( $id, ANSPRESS_VIEW_META, true );	
	$views = empty($views) ? 1 : $views;
	
	return apply_filters('ap_get_views', $views);
}

/**
 * @param integer $id
 */
function ap_get_views_db($id){
	return ap_meta_total_count('post_view', $id);
}

function ap_is_already_viewed($user_id, $data_id, $type ='question'){
	
	$ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
	
	$done = ap_meta_user_done('post_view', $user_id, $data_id, false, $ip);

	return $done > 0 ? true : false;
}