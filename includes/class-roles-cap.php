<?php
/**
 * Roles and Capabilities
 *
 * @package     AnsPress
 * @subpackage  Classes/Roles
 * @copyright   Copyright (c) 2013, Rahul Aryan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.8
*/


class AP_Roles{

	/**
	 * Add roles and cap, called on plugin activation
	 *
	 * @since 2.0.1
	 */
	public function add_roles(){

		add_role( 'ap_moderator', __( 'AnsPress Moderator', 'ap' ), array(
			'read'                   => true,
			'edit_posts'             => true,
			'delete_posts'           => true,
			'upload_files'           => true,
			'delete_others_pages'    => true,
			'delete_others_posts'    => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'edit_others_posts'      => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_published_pages'   => true,
			'edit_published_posts'   => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'publish_pages'          => true,
			'publish_posts'          => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true
		) );

		add_role( 'ap_participant', __( 'AnsPress Participants', 'ap' ), array() );
	}

	/**
	 * Add new capabilities
	 *
	 * @access public
	 * @since  2.0
	 * @global WP_Roles $wp_roles
	 * @return void
	 */
	public function add_capabilities() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		if ( is_object( $wp_roles ) ) {
			$base_caps = array(
				'ap_read_question'         	=> true,
				'ap_read_answer'			=> true,

				'ap_new_question'			=> true,
				'ap_new_answer'				=> true,
				'ap_new_comment'			=> true,

				'ap_edit_question'			=> true,
				'ap_edit_answer'			=> true,
				'ap_edit_comment'			=> true,

				'ap_hide_question'			=> true,
				'ap_hide_answer'			=> true,

				'ap_delete_question'		=> true,
				'ap_delete_answer'			=> true,
				'ap_delete_comment'			=> true,

				'ap_vote_up'				=> true,
				'ap_vote_down'				=> true,
				'ap_vote_flag'				=> true,
				'ap_vote_close'				=> true,

				'ap_upload_cover'			=> true,
				'ap_message'				=> true,

				'ap_new_tag'				=> true,
				'ap_change_status'			=> true,
			);

			$mod_caps = array(
				'ap_edit_others_question'	=> true,
				'ap_edit_others_answer'		=> true,
				'ap_edit_others_comment'	=> true,
				'ap_hide_others_question'	=> true,
				'ap_hide_others_answer'		=> true,
				'ap_hide_others_comment'	=> true,
				'ap_delete_others_question'	=> true,
				'ap_delete_others_answer'	=> true,
				'ap_delete_others_comment'	=> true,
				'ap_change_label'			=> true,
				'ap_view_private'			=> true,
				'ap_view_moderate'			=> true,
				'ap_change_status_other'	=> true,
			);

			$roles = array('editor', 'contributor', 'author', 'ap_participant', 'ap_moderator', 'subscriber');

			foreach ($roles as $role_name) {

				// add base cpas to all roles
				foreach ($base_caps as $k => $grant){
					$wp_roles->add_cap($role_name, $k );
				}

				if($role_name == 'editor' || $role_name == 'ap_moderator'){
					foreach ($mod_caps as $k => $grant){
						$wp_roles->add_cap($role_name, $k );
					}
				}
			}

		}

	}


	public function remove_roles(){
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		$wp_roles->remove_role( 'ap_participant' );
		$wp_roles->remove_role( 'ap_editor' );
		$wp_roles->remove_role( 'ap_moderator' );

	}
}


/* Check if a user can ask a question */
function ap_user_can_ask(){

	if(is_super_admin())
		return true;

	if(is_user_logged_in() || ap_allow_anonymous())
		return true;

	return false;
}

/* Check if a user can answer on a question */
function ap_user_can_answer($question_id){
	if(ap_opt('only_admin_can_answer') && is_user_logged_in() && !current_user_can('activate_plugins'))
		return false;

	if(is_super_admin())
		return true;

	$question = get_post($question_id);

	if(!ap_opt('disallow_op_to_answer') && $question->post_author == get_current_user_id() && get_current_user_id() > 0)
		return false;

	if($question->post_type == 'closed' )
		return false;

	if(ap_allow_anonymous() && !is_user_logged_in())
		return true;

	if(is_user_logged_in()){
		if(!ap_opt('multiple_answers') && ap_is_user_answered($question_id, get_current_user_id()) && get_current_user_id() != '0')
			return false;
		else
			return true;
	}
	return false;
}

/* Check if a user can answer on a question */
function ap_user_can_see_answers(){
	if(is_super_admin())
		return true;

	if(ap_opt('logged_in_can_see_ans') && !is_user_logged_in() )
		return false;

	return true;
}

function ap_user_can_select_answer($post_id){
	if(!is_user_logged_in())
		return false;

	if(is_super_admin())
		return true;

	$post 		= get_post($post_id);
	$question 	= get_post($post->post_parent);

	if($post->post_type == 'answer' && $question->post_author ==  get_current_user_id())
		return true;

	return false;
}


/* Check if a user can edit answer on a question */
function ap_user_can_edit_ans($post_id){
	if(current_user_can('ap_edit_others_answer') || is_super_admin()){
		return true;
	}

	if(!is_user_logged_in())
		return false;

	$post = get_post($post_id);

	if($post->post_author ==  get_current_user_id())
		return true;

	return false;
}

/**
 * Check if user can edit a question
 * @param  boolean|integer $post_id
 * @return boolean
 */
function ap_user_can_edit_question($post_id = false){
	if(is_super_admin() || current_user_can('ap_edit_others_question') )
		return true;

	if(is_user_logged_in()){

		if($post_id )
			$post = get_post($post_id);
		else
			global $post;

		if(get_current_user_id() == $post->post_author)
			return true;
	}
	return false;
}

function ap_user_can_change_label(){
	if(is_super_admin() || current_user_can('ap_change_label'))
		return true;

	return false;
}

function ap_user_can_comment(){
	if(is_super_admin() || is_user_logged_in() || ap_opt('anonymous_comment'))
		return true;

	return false;
}
function ap_user_can_edit_comment($comment_id){
	if(is_super_admin() || current_user_can('ap_mod_comment'))
		return true;

	if(get_current_user_id() == get_comment($comment_id)->user_id)
		return true;

	return false;
}

function ap_user_can_delete_comment($comment_id){
	if(is_super_admin() || current_user_can('ap_mod_comment'))
		return true;

	if( get_current_user_id() == get_comment($comment_id)->user_id)
		return true;

	return false;
}

function ap_user_can_delete($postid){
	if(is_super_admin())
		return true;

	$post = get_post($postid);

	if(get_current_user_id() == $post->post_author){
		if( ($post->post_type == 'question' || $post->post_type == 'answer') && current_user_can('ap_delete_question'))
			return true;
	}else{
		if( $post->post_type == 'question' && current_user_can('ap_delete_others_question'))
			return true;
		elseif( $post->post_type == 'answer' && current_user_can('ap_delete_others_answer'))
			return true;
	}

	return false;
}

function ap_user_can_permanent_delete(){
	if(is_super_admin())
		return true;

	return false;
}

function ap_user_can_upload_cover(){
	if(is_super_admin() || is_user_logged_in())
		return true;

	return false;
}

function ap_user_can_message(){
	if(is_super_admin() || is_user_logged_in())
		return true;

	return false;
}

function ap_user_can_create_tag(){
	if(is_super_admin() || (current_user_can('ap_new_tag') && ap_get_points() >= ap_opt('min_point_new_tag') ))
		return true;

	return false;
}

/**
 * Check if user gave permission to view post
 * @param  int $post_id post ID
 * @return boolean
 * @since 2.0.1
 */
function ap_user_can_view_private_post($post_id){
	$post = get_post( $post_id );

	if($post->post_status != 'private_post')
		return true;

	if($post->post_author == get_current_user_id())
		return true;

	if(is_super_admin() || current_user_can('ap_view_private'))
		return true;


	if($post->post_type == 'answer'){
		$question = get_post($post->post_parent);

		if($question->post_author == get_current_user_id())
			return true;
	}

	return false;
}

function ap_user_can_view_moderate_post($question_id){
	if(is_super_admin() || current_user_can('ap_view_moderate'))
		return true;

	$post = get_post( $question_id );

	if($post->post_author == get_current_user_id())
		return true;

	return false;
}

/**
 * Check if user can view post
 * @param  boolean|integer $post_id
 * @return boolean
 */
function ap_user_can_view_post($post_id = false){
	if(is_super_admin())
		return true;

	if(!$post_id)
		$post_id = get_the_ID();

	$post = get_post( $post_id );

	if( $post->post_status == 'private_post' && ap_user_can_view_private_post($post_id))
		return true;

	if( $post->post_status == 'moderate' && ap_user_can_view_moderate_post($post_id))
		return true;

	if( $post->post_status == 'publish' || $post->post_status == 'closed')
		return true;

	return false;

}

function ap_allow_anonymous(){
	return ap_opt('allow_anonymous');
}

/**
 * Check if current user can change post status i.e. private_post, moderate, closed
 * @param  integer $post_id Question id
 * @return boolean
 * @since 2.1
 **/
function ap_user_can_change_status($post_id){
	if( current_user_can('ap_change_status_other') || is_super_admin())
		return true;

	$post = get_post( $post_id );

	if($post->post_author == get_current_user_id())
		return true;

	return false;
}

function ap_user_can_change_status_to_closed(){
	if(is_super_admin() || current_user_can('ap_change_status_other'))
		return true;

	return false;
}

function ap_user_can_change_status_to_moderate(){
	if(is_super_admin() || current_user_can('ap_change_status_other'))
		return true;

	return false;
}

function ap_user_can_upload_image(){
	if(is_user_logged_in() && ap_opt('allow_upload_image'))
		return true;

	return false;
}