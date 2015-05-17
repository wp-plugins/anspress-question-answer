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

/**
 * Get slug of base page
 * @return string
 * @since 2.0.0-beta
 */
function ap_base_page_slug(){
	$base_page = get_post(ap_opt('base_page'));

	$slug = $base_page->post_name;

	if( $base_page->post_parent > 0 ){
		$parent_page = get_post($base_page->post_parent);
		$slug = $parent_page->post_name . '/'.$slug;
	}

	return apply_filters('ap_base_page_slug', $slug) ;
}

/**
 * Retrive permalink to base page
 * @return string URL to AnsPress base page
 * @since 2.0.0-beta
 */
function ap_base_page_link(){
	return get_permalink(ap_opt('base_page'));
}


function ap_theme_list(){
	$themes = array();
	$dirs = array_filter(glob(ANSPRESS_THEME_DIR.'/*'), 'is_dir');
	foreach($dirs as $dir)
		$themes[basename($dir)] = basename($dir);		
	return $themes;
}

function ap_get_theme(){	
	$option = ap_opt('theme');
	if(!$option)
		return 'default';
	
	return ap_opt('theme');
}

/**
 * Get location to a file
 * First file is looked inside active WordPress theme directory /anspress.
 * @param  	string  	$file   	file name
 * @param  	mixed 		$plugin   	Plugin path
 * @return 	string 
 * @since 	0.1         
 */
function ap_get_theme_location($file, $plugin = false){
	// checks if the file exists in the theme first,
	// otherwise serve the file from the plugin
	if ( $theme_file = locate_template( array( 'anspress/'.$file ) ) ) {
		$template_path = $theme_file;
	} elseif($plugin !== false) {
		$template_path = $plugin .'/theme/'.$file;
	}else {
		$template_path = ANSPRESS_THEME_DIR .'/'.ap_get_theme().'/'.$file;
	}
	return $template_path;
}

/**
 * Get url to a file
 * Used for enqueue CSS or JS
 * @param  		string  $file   
 * @param  		mixed $plugin 
 * @return 		string          
 * @since  		2.0
 */
function ap_get_theme_url($file, $plugin = false){
	// checks if the file exists in the theme first,
	// otherwise serve the file from the plugin
	if ( locate_template( array( 'anspress/'.$file ) ) ) {
		$template_url = get_template_directory_uri().'/anspress/'.$file;
	} elseif($plugin !== false) {
		$template_url = $plugin .'theme/'.$file;
	}else {
		$template_url = ANSPRESS_THEME_URL .'/'.ap_get_theme().'/'.$file;
	}
	return $template_url;
}


//get current user id
function ap_current_user_id() {
	require_once(ABSPATH . WPINC . '/pluggable.php');
	global $current_user;
	get_currentuserinfo();
	return $current_user->ID;
}

function ap_question_content(){
	global $post;
	echo $post->post_content;
}


function is_anspress(){
	$queried_object = get_queried_object();

	// if buddypress installed
	if(function_exists('bp_current_component')){
		$bp_com = bp_current_component();
		if('questions' == $bp_com || 'answers' == $bp_com)
			return true;
	}
	
	if(!isset($queried_object->ID)) 
		return false;

	if( $queried_object->ID ==  ap_opt('base_page'))
		return true;
		
	return false;
}

function is_question(){
	$question_id = (int) get_query_var('question_id');
	if(is_anspress() && $question_id > 0 )
		return true;
		
	return false;
}

function is_ask(){
	if(is_anspress() && get_query_var('ap_page')=='ask')
		return true;
		
	return false;
}
function is_ap_users(){
	if(is_anspress() && get_query_var('ap_page')=='users')
		return true;
		
	return false;
}

function get_question_id(){
	if(is_question() && get_query_var('question_id')){
		return (int)get_query_var('question_id');
	}elseif(is_question() && get_query_var('question')){
		return get_query_var('question');
	}elseif(is_question() && get_query_var('question_name')){
		$post = get_page_by_path(get_query_var('question_name'), OBJECT, 'question');
		return $post->ID;
	}elseif(get_query_var('edit_q')){
		return get_query_var('edit_q');
	}elseif(ap_answer_the_object()){
		return ap_answer_get_the_question_id();
	}
	
	return false;
}

function ap_human_time($time, $unix = true){
	if(!$unix)
		$time = strtotime($time);
	
	return human_time_diff( $time, current_time('timestamp') );
}


function ap_please_login(){
	$o  = '<div id="please-login">';
	$o .= '<button>x</button>';
	$o .= __('Please login or register to continue this action.', 'ap');
	$o .= '</div>';
	
	echo apply_filters('ap_please_login', $o);
}

//check if user answered on a question
function ap_is_user_answered($question_id, $user_id){
	global $wpdb;
	
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts where post_parent = $question_id AND ( post_author = $user_id AND post_type = 'answer')");
	if($count)	
		return true;	
	return false;
}

/**
 * Count all answers of a question includes all post status
 * @param  int $id question id
 * @return int
 * @since 2.0.1.1
 */
function ap_count_all_answers($id){
	
	global $wpdb;
	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts where post_parent = %d AND post_type = %s", $id, 'answer'));

	return $count;
}

function ap_count_published_answers($id){
	
	global $wpdb;
	$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts where post_parent = %d AND (post_status = %s OR post_status = %s) AND post_type = %s", $id, 'publish', 'closed', 'answer'));

	return $count;
}

function ap_count_answer_meta($post_id =false){
	if(!$post_id) $post_id = get_the_ID();
	$count = get_post_meta($post_id, ANSPRESS_ANS_META, true);
	return $count ? $count : 0;
}

/**
 * Count all answers excluding best answer
 * @return int
 */
function ap_count_other_answer($question_id =false){
	if(!$question_id) $question_id = get_question_id();

	$count = ap_count_answer_meta($question_id);
	
	if(ap_question_best_answer_selected($question_id))
		return (int)($count - 1);

	return (int)$count;
	
}

function ap_last_active($post_id =false){
	if(!$post_id) $post_id = get_the_ID();
	return get_post_meta($post_id, ANSPRESS_UPDATED_META, true);
}

// link to asnwers
function ap_answers_link($question_id = false){
	if(!$question_id)
		return get_permalink().'#answers';
	
	return get_permalink($question_id).'#answers';
}


/**
 * Load comment form button
 * @param  boolean $echo
 * @return string        
 * @since 0.1
 */
function ap_comment_btn_html($echo = false){
	if(ap_user_can_comment()){
		global $post;
		
		if($post->post_type == 'question' && ap_opt('disable_comments_on_question'))
			return;

		if($post->post_type == 'answer' && ap_opt('disable_comments_on_answer'))
			return;

		$nonce = wp_create_nonce( 'comment_form_nonce' );
		$comment_count = get_comments_number( get_the_ID() );
		$output = '<a href="#comments-'.get_the_ID().'" class="comment-btn ap-tip" data-action="load_comment_form" data-query="ap_ajax_action=load_comment_form&post='.get_the_ID().'&__nonce='.$nonce.'" title="'.__('Comments', 'ap').'">'.__('Comment', 'ap').'<span class="ap-data-view ap-view-count-'.$comment_count.'" data-view="comments_count_'.get_the_ID().'">('.$comment_count.')</span></a>';

		if($echo)
			echo $output;
		else
			return $output;
	}
}

/**
 * Return edit link for question and answer
 * @param  int| object $post_id_or_object
 * @return string                 
 * @since 2.0.1
 */
function ap_post_edit_link($post_id_or_object){
	if(!is_object($post_id_or_object))
		$post_id_or_object = get_post($post_id_or_object);

	$post = $post_id_or_object;

	$nonce = wp_create_nonce( 'nonce_edit_post_'.$post->ID );

	$edit_link = add_query_arg( array('ap_page' => 'edit', 'edit_post_id' => $post->ID,  '__nonce' => $nonce), ap_base_page_link() );

	return apply_filters( 'ap_post_edit_link', $edit_link );
}

/**
 * Returns edit post button html
 * @param  boolean $echo
 * @param  int | object $post_id_or_object
 * @return null|string
 * @since 2.0.1
 */
function ap_edit_post_link_html($echo = false, $post_id_or_object = false){
	if(!is_object($post_id_or_object))
		$post_id_or_object = get_post($post_id_or_object);

	$post = $post_id_or_object;
	
	$edit_link = ap_post_edit_link($post);

	$output = '';

	if($post->post_type == 'question' && ap_user_can_edit_question($post->ID)){		
		$output = "<a href='$edit_link' data-button='ap-edit-post' title='".__('Edit this question', 'ap')."' class='apEditBtn'>".__('Edit', 'ap')."</a>";	
	}elseif($post->post_type == 'answer' && ap_user_can_edit_ans($post->ID)){
		$output = "<a href='$edit_link' data-button='ap-edit-post' title='".__('Edit this answer', 'ap')."' class='apEditBtn'>".__('Edit', 'ap')."</a>";
	}

	if($echo)
		echo $output;
	else
		return $output;
}

function ap_edit_a_btn_html( $echo = false ){
	if(!is_user_logged_in())
		return;
	$output = '';	
	$post_id = get_edit_answer_id();
	if(ap_user_can_edit_ans($post_id)){		
		$edit_link = ap_answer_edit_link();
		$output .= "<a href='$edit_link.' class='edit-btn ' data-button='ap-edit-post' title='".__('Edit Answer', 'ap')."'>".__('Edit', 'ap')."</a>";
	}
	if($echo)
		echo $output;
	else
		return $output;
}

function ap_post_edited_time() {
	if (get_the_time('s') != get_the_modified_time('s')){
		printf('<span class="edited-text">%1$s</span> <span class="edited-time">%2$s</span>',
			__('Edited on','ap'),
			get_the_modified_time()
			);
		
	}
	return;
}

function ap_answer_edit_link(){
	$post_id = get_the_ID();
	if(ap_user_can_edit_ans($post_id)){		
		$action = get_post_type($post_id).'-'.$post_id;
		$nonce = wp_create_nonce( $action );
		$edit_link = add_query_arg( array('edit_a' => $post_id, 'ap_nonce' => $nonce), get_permalink( ap_opt('base_page')) );
		return apply_filters( 'ap_answer_edit_link', $edit_link );
	}
	return;
}

/**
 * @param string $text
 * @param integer $limit
 */
function ap_truncate_chars($text, $limit, $ellipsis = '...') {
	if( strlen($text) > $limit ) {
		$endpos = strpos(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $text), ' ', $limit);
		if($endpos !== FALSE)
			$text = trim(substr($text, 0, $endpos)) . $ellipsis;
	}
	return $text;
}


function ap_get_all_users(){
	$paged 			= (get_query_var('paged')) ? get_query_var('paged') : 1;
	$per_page    	= ap_opt('tags_per_page');
	$total_terms 	= wp_count_terms('question_tags');
	$offset      	= $per_page * ( $paged - 1);
	
	$args = array(
		'number'		=> $per_page,
		'offset'       	=> $offset
		);
	
	$users = get_users( $args); 
	
	echo '<ul class="ap-tags-list">';
	foreach($users as $key => $user) :

		echo '<li>';
	echo $user->display_name;			
	echo '</li>';

	endforeach;
	echo'</ul>';
	
	ap_pagination(ceil( $total_terms / $per_page ), $range = 1, $paged);
}

function ap_ans_list_tab(){
	$order = isset($_GET['ap_sort']) ? $_GET['ap_sort'] : ap_opt('answers_sort');
	
	$link = '?ap_sort=';
	?>
	<ul class="ap-ans-tab ap-tabs clearfix" role="tablist">
		<li class="<?php echo $order == 'newest' ? ' active' : ''; ?>"><a href="<?php echo $link.'newest'; ?>"><?php _e('Newest', 'ap'); ?></a></li>
		<li class="<?php echo $order == 'oldest' ? ' active' : ''; ?>"><a href="<?php echo $link.'oldest'; ?>"><?php _e('Oldest', 'ap'); ?></a></li>
		<li class="<?php echo $order == 'voted' ? ' active' : ''; ?>"><a href="<?php echo $link.'voted'; ?>"><?php _e('Voted', 'ap'); ?></a></li>
	</ul>
	<?php
}



function ap_untrash_post( $post_id ) {
    // no post?
	if( !$post_id || !is_numeric( $post_id ) ) {
		return false;
	}
	$_wpnonce = wp_create_nonce( 'untrash-post_' . $post_id );
	$url = admin_url( 'post.php?post=' . $post_id . '&action=untrash&_wpnonce=' . $_wpnonce );
	return $url; 
}

function ap_user_can($id){
	get_user_meta( $id, 'ap_role', true );
}

/**
 * Return the ID of selected answer of a question
 * @param  false|integer $post_id
 * @return integer
 */
function ap_selected_answer($post_id = false){
	if(false === $post_id)
		$post_id = get_the_ID();
	
	return get_post_meta($post_id, ANSPRESS_SELECTED_META, true);
}

/**
 * Print select anser HTML button
 * @param integer $post_id
 * @return  null|string
 */
function ap_select_answer_btn_html($post_id){
	if(!ap_user_can_select_answer($post_id))
		return;
	
	$ans = get_post($post_id);
	$action = 'answer-'.$post_id;
	$nonce = wp_create_nonce( $action );	
	
	if(!ap_question_best_answer_selected($ans->post_parent)){		
		return '<a href="#" class="ap-btn-select ap-sicon '.ap_icon('check').' ap-tip" data-action="select_answer" data-query="answer_id='. $post_id.'&__nonce='. $nonce .'&ap_ajax_action=select_best_answer" title="'.__('Select this answer as best', 'ap').'">'.__('Select', 'ap').'</a>';
		
	}elseif(ap_question_best_answer_selected($ans->post_parent) && ap_answer_is_best($ans->ID)){
		return '<a href="#" class="ap-btn-select ap-sicon '.ap_icon('cross').' active ap-tip" data-action="select_answer" data-query="answer_id='. $post_id.'&__nonce='. $nonce .'&ap_ajax_action=select_best_answer" title="'.__('Unselect this answer', 'ap').'">'.__('Unselect', 'ap').'</a>';
		
	}
}

/**
 * Output frontend post delete button
 * @param  integer $post_id
 * @param  boolean $echo
 * @return void|string
 */
function ap_post_delete_btn_html($post_id = false, $echo = false){
	if($post_id === false){
		$post_id = get_the_ID();
	}
	if(ap_user_can_delete($post_id)){		
		$action = 'delete_post_'.$post_id;
		$nonce = wp_create_nonce( $action );
		
		$output = '<a href="#" class="delete-btn" data-action="ap_delete_post" data-query="post_id='. $post_id.'&__nonce='. $nonce .'&ap_ajax_action=delete_post" title="'.__('Delete', 'ap').'">'.__('Delete', 'ap').'</a>';

		if($echo)
			echo $output;
		else
			return $output;
	}
}

function ap_post_permanent_delete_btn_html($post_id = false, $echo = false){
	if($post_id === false){
		$post_id = get_the_ID();
	}
	if(ap_user_can_permanent_delete()){		
		$action = 'delete_post_'.$post_id;
		$nonce = wp_create_nonce( $action );
		
		$output = '<a href="#" class="delete-btn" data-action="ap_delete_post" data-query="post_id='. $post_id.'&__nonce='. $nonce .'&ap_ajax_action=permanent_delete_post" title="'.__('Delete permanently', 'ap').'">'.__('Delete permanently', 'ap').'</a>';

		if($echo)
			echo $output;
		else
			return $output;
	}
}

/**
 * Output chnage post status button
 * @param  boolean|integer $post_id
 * @return null|string
 */
function ap_post_change_status_btn_html($post_id = false){
	$post = get_post($post_id);

	if(ap_user_can_change_status($post_id)){		
		$action = 'change_post_status_'.$post_id;
		$nonce = wp_create_nonce( $action );
		
		$status = apply_filters('ap_change_status_dropdown', array('closed' => __('Close', 'ap'), 'publish' => __('Open', 'ap'), 'moderate' => __('Moderate', 'ap'), 'private_post' => __('Private', 'ap') ));

		$output = '<div class="ap-dropdown">
			<a class="ap-tip ap-dropdown-toggle" title="'.__('Change status of post', 'ap').'" href="#" >
				'.__('Status', 'ap').' <i class="caret"></i>
			</a>
			<ul id="ap_post_status_toggle_'.$post_id.'" class="ap-dropdown-menu" role="menu">';

			foreach($status as $k => $title){
				
				$can = true;

				if($k == 'closed' && ( !ap_user_can_change_status_to_closed() || $post->post_type == 'answer'))
					$can = false;

				elseif($k == 'moderate' && !ap_user_can_change_status_to_moderate() )
					$can = false;

				if($can){
					$output .= '<li class="'.$k.($k == $post->post_status ? ' active' : '').'">
						<a href="#" data-action="ap_change_status" data-query="post_id='.$post_id.'&__nonce='.$nonce.'&ap_ajax_action=change_post_status&status='.$k.'">'.$title.'</a>
					</li>';
				}
			}	
			$output .='</ul>
		</div>';

		return $output;
	}
}

function ap_get_child_answers_comm($post_id){
	global $wpdb;
	$ids = array();
	
	$query = "SELECT p.ID, c.comment_ID from $wpdb->posts p LEFT JOIN $wpdb->comments c ON c.comment_post_ID = p.ID OR c.comment_post_ID = $post_id WHERE post_parent = $post_id";
	
	$key = md5($query);	
	$cache = wp_cache_get($key, 'count');
	
	if($cache === false){
		$cols = $wpdb->get_results( $query, ARRAY_A);
		wp_cache_set($key, $cols, 'count');
	}else
	$cols = $cache;
	
	
	if($cols){
		foreach($cols as $c){
			if(!empty($c['ID']))
				$ids['posts'][] = $c['ID'];
			
			if(!empty($c['comment_ID']))
				$ids['comments'][] = $c['comment_ID'];
		}
	}else{
		return false;
	}
	
	if(isset($ids['posts']))
		$ids['posts']= array_unique ($ids['posts']);
	
	if(isset($ids['comments']))
		$ids['comments'] = array_unique ($ids['comments']);

	return $ids;
}

function ap_short_num($num, $precision = 2) {
	if ($num >= 1000 && $num < 1000000) {
		$n_format = number_format($num/1000,$precision).'K';
	} else if ($num >= 1000000 && $num < 1000000000) {
		$n_format = number_format($num/1000000,$precision).'M';
	} else if ($num >= 1000000000) {
		$n_format=number_format($num/1000000000,$precision).'B';
	} else {
		$n_format = $num;
	}
	return $n_format;
}

function sanitize_comma_delimited($str){
	return implode(",", array_map("intval", explode(",", $str)));
}


/**
 * Check if doing ajax request
 * @return boolean
 * @since 2.0.1
 */
function ap_is_ajax(){
	if (defined('DOING_AJAX') && DOING_AJAX)
		return true;

	return false;
}

/**
 * Allow HTML tags
 * @return array
 * @since 0.9
 */
function ap_form_allowed_tags(){
	$allowed_style = array(
		'align' => true
	);
	$allowed_tags = array(
		'p' => array(
			'style' => $allowed_style,
			'title' => true
			),
		'span' => array(
			'style' => $allowed_style,
			),
		'a' => array(
			'href' => true,
			'title' => true
			),
		'br' => array(),
		'em' => array(),
		'strong' => array(
			'style' => $allowed_style,
			),
		'pre' => array(),
		'code' => array(),
		'blockquote' => array(),
		'img' => array(
			'src' => true,
			'style' => $allowed_style,
			),
		'ul' => array(),
		'ol' => array(),
		'li' => array(),
		'del' => array(),
		'br' => array(),
		);
	
	/**
	 * FILTER: ap_allowed_tags
	 * Before passing allowed tags
	 */
	return apply_filters( 'ap_allowed_tags', $allowed_tags);
}

function ap_send_json($result = array()){
	$result['is_ap_ajax'] = true;

	wp_send_json( $result );
}

/**
 * Highlight matching words
 * @param  	string $text 
 * @param  	string $words
 * @return 	string 
 * @since 	2.0
 */
function ap_highlight_words($text, $words) {
	$words = explode(' ', $words);
	foreach ($words as $word)
	{
        //quote the text for regex
		$word = preg_quote($word);
		
        //highlight the words
		$text = preg_replace("/\b($word)\b/i", '<span class="highlight_word">\1</span>', $text);
	}

	return $text;
}

/**
 * Return response with type and message
 * @param  string $id messge id
 * @param  boolean $only_message return message string instead of array
 * @return string
 * @since 2.0.0-alpha2
 */
function ap_responce_message($id, $only_message = false)
{
	$msg =array(
		'success' => array('type' => 'success', 'message' => __('Success', 'ap')),
		'please_login' => array('type' => 'warning', 'message' => __('You need to login before doing this action.', 'ap')),
		'something_wrong' => array('type' => 'error', 'message' => __('Something went wrong, last action failed.', 'ap')),
		'no_permission' => array('type' => 'warning', 'message' => __('You do not have permission to do this action.', 'ap')),
		'draft_comment_not_allowed' => array('type' => 'warning', 'message' => __('You are commenting on a draft post.', 'ap')),
		'comment_success' => array('type' => 'success', 'message' => __('Comment successfully posted.', 'ap')),
		'comment_edit_success' => array('type' => 'success', 'message' => __('Comment updated successfully.', 'ap')),
		'comment_delete_success' => array('type' => 'success', 'message' => __('Comment deleted successfully.', 'ap')),
		'subscribed' => array('type' => 'success', 'message' => __('You are subscribed to this question.', 'ap')),
		'unsubscribed' => array('type' => 'success', 'message' => __('Successfully unsubscribed.', 'ap')),
		'question_submitted' => array('type' => 'success', 'message' => __('Question submitted successfully', 'ap')),
		'question_updated' => array('type' => 'success', 'message' => __('Question updated successfully', 'ap')),
		'answer_submitted' => array('type' => 'success', 'message' => __('Answer submitted successfully', 'ap')),
		'answer_updated' => array('type' => 'success', 'message' => __('Answer updated successfully', 'ap')),
		'voted' => array('type' => 'success', 'message' => __('Thank you for voting.', 'ap')),
		'undo_vote' => array('type' => 'success', 'message' => __('Your vote has been removed.', 'ap')),
		'undo_vote_your_vote' => array('type' => 'warning', 'message' => __('Undo your vote first.', 'ap')),
		'cannot_vote_own_post' => array('type' => 'warning', 'message' => __('You cannot vote on your own question or answer.', 'ap')),
		'unselected_the_answer' => array('type' => 'success', 'message' => __('Best answer is unselected for your question.', 'ap')),
		'selected_the_answer' => array('type' => 'success', 'message' => __('Best answer is selected for your question.', 'ap')),
		'question_moved_to_trash' => array('type' => 'success', 'message' => __('Question moved to trash.', 'ap')),
		'answer_moved_to_trash' => array('type' => 'success', 'message' => __('Answer moved to trash.', 'ap')),
		'no_permission_to_view_private' => array('type' => 'warning', 'message' => __('You dont have permission to view private posts.', 'ap')),
		'flagged' => array('type' => 'success', 'message' => __('Thank you for reporting this post.', 'ap')),
		'already_flagged' => array('type' => 'warning', 'message' => __('You have already reported this post.', 'ap')),
		'captcha_error' => array('type' => 'error', 'message' => __('Please check captcha field and resubmit it again.', 'ap')),
		'comment_content_empty' => array('type' => 'error', 'message' => __('Comment content is empty.', 'ap')),
		'status_updated' => array('type' => 'success', 'message' => __('Post status updated successfully', 'ap')),
		'post_image_uploaded' => array('type' => 'success', 'message' => __('Image uploaded successfully', 'ap')),
		'question_deleted_permanently' => array('type' => 'success', 'message' => __('Question has been deleted permanently', 'ap')),
		'answer_deleted_permanently' => array('type' => 'success', 'message' => __('Answer has been deleted permanently', 'ap')),
		);

	/**
	 * FILTER: ap_responce_message
	 * Can be used to alter response messages
	 * @var array
	 * @since 2.0.1 
	 */
	$msg = apply_filters( 'ap_responce_message', $msg );

	if(isset($msg[$id]) && $only_message)
		return $msg[$id]['message'];

	if(isset($msg[$id]))
		return $msg[$id];

	return false;
}

function ap_ajax_responce($results)
{

	if(!is_array($results)){
		$message_id = $results;
		$results = array();
		$results['message'] = $message_id;
	}

	$results['ap_responce'] = true;

	if( isset($results['message']) ){
		$error_message = ap_responce_message($results['message']);

		if($error_message !== false){
			$results['message'] = $error_message['message'];
			$results['message_type'] = $error_message['type'];
		}
	}

	/**
	 * FILTER: ap_ajax_responce
	 * Can be used to alter ap_ajax_responce
	 * @var array
	 * @since 2.0.1
	 */
	$results = apply_filters( 'ap_ajax_responce', $results );

	return $results;
}

function ap_meta_array_map( $a ) {
	return $a[0];
}

/**
 * Return the current page url
 * @param  array $args
 * @return string
 * @since 2.0.0-alpha2
 */
function ap_current_page_url($args){
	$base = rtrim(get_permalink(), '/');

	
	if(get_option('permalink_structure') != ''){

		$link = $base.'/';
		if(!empty($args))
			foreach($args as $k => $s)
				$link .= $k.'/'.$s.'/';
			
		}else{
			
			$link = add_query_arg($args, $base);
		}
		return $link ;
	}

/**
 * Sort array by order value. Group array which have same order number and then sort them.
 * @param  array $array
 * @return array
 * @since 2.0.0-alpha2
 */
function ap_sort_array_by_order($array){
	$new_array = array();
	if(!empty($array) && is_array($array) ){
		$group = array();
		foreach($array as $k => $a){
			$order = $a['order'];
			$group[$order][] = $a;
			$group[$order]['order'] = $order;
		}
		
		usort($group, 'ap_sort_order_callback');

		foreach($group as $a){
			foreach($a as $k => $newa){
				if($k !== 'order')
					$new_array[] = $newa;
			}
		}

		return $new_array;
	}
}

function ap_sort_order_callback($a, $b) {
	return $a['order'] - $b['order'];
}

/**
 * Append array to global var
 * @param  string 	$key
 * @param  array 	$args
 * @param string 	$var
 * @return void
 * @since 2.0.0-alpha2
 */
function ap_append_to_global_var($var, $key, $args){
	if(!isset($GLOBALS[$var]))
		$GLOBALS[$var] = array();
	
	$GLOBALS[$var][$key] = $args;
}

/**
 * Register an event
 * @return void
 * @since 0.1
 */
function ap_do_event(){
	$args = func_get_args ();
	do_action('ap_event', $args);
	//do_action('ap_event_'.$args[0], $args);
	$action = 'ap_event_'.$args[0];
	$args[0] = $action;
	call_user_func_array('do_action', $args);
}

/**
 * Echo anspress links
 * @return void
 * @since 2.1
 */
function ap_link_to($sub){
	echo ap_get_link_to($sub);
}

	/**
	 * Return link to AnsPress pages
	 * @param string|array $sub
	 */
	function ap_get_link_to($sub){
		
		$base = rtrim(get_permalink(ap_opt('base_page')), '/');
		$args = '';

		if(get_option('permalink_structure') != ''){		
			if(!is_array($sub))
				$args = $sub ? '/'.$sub : '';

			elseif(is_array($sub)){
				$args = '/';

				if(!empty($sub))
					foreach($sub as $s)
						$args .= $s.'/';
				}

				$args = rtrim($args, '/').'/';
		}else{

			if(!is_array($sub))
				$args = $sub ? '&ap_page='.$sub : '';
			
			elseif(is_array($sub)){
				$args = '';
				
				if(!empty($sub))
					foreach($sub as $k => $s)
						$args .= '&'.$k .'='.$s;
			}
		}

		return $base. $args ;
	}


/**
 * Return the total numbers of post
 * @param  string         $post_type
 * @param  boolean|string $ap_type
 * @return array
 * @since  2.0.0-alpha2
 */
function ap_total_posts_count($post_type = 'question', $ap_type =  false)
{
	global $wpdb;
	
	if('question' == $post_type)
		$type = "p.post_type = 'question'";
	elseif('answer' == $post_type)
		$type = "p.post_type = 'answer'";
	else
		$type = "(p.post_type = 'question' OR p.post_type = 'answer')";

	$meta = "";
	$join = "";
	
	if($ap_type){
		$meta = "AND m.apmeta_type='$ap_type'";
		$join = "INNER JOIN ".$wpdb->prefix."ap_meta m ON p.ID = m.apmeta_actionid";
	}

	$where = "WHERE $type $meta";
	
	$where = apply_filters( 'ap_total_posts_count', $where );
	
	$query = "SELECT count(*) as count, p.post_status FROM $wpdb->posts p $join $where GROUP BY p.post_status";
	
	$cache_key = md5( $query );

	$count = wp_cache_get( $cache_key, 'counts');
	
	if ( false !== $count )
		return $count;
		
	$count = $wpdb->get_results( $query, ARRAY_A);
	
	$counts = array();
	foreach ( get_post_stati() as $state )
		$counts[$state] = 0;	

	$counts['total'] = 0;

	foreach ( (array) $count as $row ){
		$counts[$row['post_status']] = $row['count'];
		$counts['total'] += $row['count'];
	}	
	wp_cache_set( $cache_key, (object)$counts, 'counts' );

	return (object)$counts;
}

function ap_total_published_questions(){
	$posts = ap_total_posts_count();
	return $posts->publish;
}

/**
 * Get total numbers of solved question
 * @param  string $type int|object
 * @return integer|object
 */
function ap_total_solved_questions($type = 'int'){
	global $wpdb;

	$query = "SELECT count(*) as count, p.post_status FROM $wpdb->posts p INNER JOIN ".$wpdb->prefix."postmeta m ON p.ID = m.post_id WHERE m.meta_key = '_ap_selected' AND m.meta_value !='' GROUP BY p.post_status";
	
	$cache_key = md5( $query );

	$count = wp_cache_get( $cache_key, 'counts');

	if ( false !== $count )
		return $count;

	$count = $wpdb->get_results( $query, ARRAY_A);
	
	$counts = array();
	foreach ( get_post_stati() as $state )
		$counts[$state] = 0;	

	$counts['total'] = 0;

	foreach ( (array) $count as $row ){
		$counts[$row['post_status']] = $row['count'];
		$counts['total'] += $row['count'];
	}	
	wp_cache_set( $cache_key, (object)$counts, 'counts' );

	$counts = (object)$counts;

	if($type == 'int')
		return $counts->publish + $counts->closed + $counts->private_post;

	return $counts;
}

/**
 * Get current sorting type
 * @return string
 * @since 2.1
 */
function ap_get_sort(){
	if(isset($_GET['ap_sort']))
		return sanitize_text_field( $_GET['ap_sort'] );
}

/**
 * Register AnsPress menu
 * @param  string $slug 
 * @param  string $title
 * @param  string $link
 * @return void
 */
function ap_register_menu($slug, $title, $link){
	anspress()->menu[$slug] = array('title' => $title, 'link' => $link);
}

/**
 * Check if first parameter is false, if yes then return other parameter
 * @param  mixed $param
 * @param  mixed $return
 * @return mixed
 * @since 2.1
 */
function ap_parameter_empty($param = false, $return){
	if($param === false || $param == '')
		return $return;

	return $param;
}


function ap_post_status_description($post_id = false){
    $post_id = ap_parameter_empty($post_id, @ap_question_get_the_ID());
    $post = get_post($post_id);
    $post_type = $post->post_type == 'question' ? __('Question','ap') : __('Answer','ap');

    if ( ap_have_parent_post($post_id) && $post->post_type != 'answer') : ?>
        <div id="ap_post_status_desc_<?php echo $post_id; ?>" class="ap-notice blue clearfix">
            <?php echo ap_icon('link', true) ?>
            <span><?php printf(__( 'Question is asked for %s.', 'ap' ), '<a href="'. get_permalink(ap_question_get_the_post_parent()) .'">'.get_the_title( ap_question_get_the_post_parent() ).'</a>'); ?></span>
        </div>
    <?php endif;

    if ( is_private_post($post_id)) : ?>
        <div id="ap_post_status_desc_<?php echo $post_id; ?>" class="ap-notice gray clearfix">
            <i class="apicon-lock"></i><span><?php printf(__( '%s is marked as a private, only admin and post author can see.', 'ap' ), $post_type); ?></span>
        </div>
    <?php endif;

    if ( is_post_waiting_moderation($post_id)) : ?>
        <div id="ap_post_status_desc_<?php echo $post_id; ?>" class="ap-notice yellow clearfix">
            <i class="apicon-info"></i><span><?php printf(__( '%s is waiting for approval by moderator.', 'ap' ), $post_type); ?></span>
        </div>
    <?php endif;

    if ( is_post_closed($post_id) && $post->post_type != 'answer') : ?>
        <div id="ap_post_status_desc_<?php echo $post_id; ?>" class="ap-notice red clearfix">
            <?php echo ap_icon('cross', true) ?><span><?php printf(__( '%s is closed, new answer are not accepted.', 'ap' ), $post_type); ?></span>
        </div>
    <?php endif;

    if ( $post->post_status=='trash') : ?>
        <div id="ap_post_status_desc_<?php echo $post_id; ?>" class="ap-notice red clearfix">
            <?php echo ap_icon('cross', true) ?><span><?php printf(__( '%s has been trashed, you can delete it permanently from wp-admin.', 'ap' ), $post_type); ?></span>
        </div>
    <?php endif;
}

function ap_post_upload_form($post_id = false){
	$html = '
    <div class="ap-post-upload-form">
        <div class="ap-btn ap-upload-o '.ap_icon('image').'">
        	<span>'.__('Add image to editor', 'ap').'</span>';
        	if(ap_user_can_upload_image())
	        	$html .= '	
	            <span class="ap-upload-link">
	            	'.__('upload', 'ap').'
	            	<input type="file" name="post_upload_image" class="ap-upload-input" data-action="ap_post_upload_field">
	            </span> '.__('or', 'ap');

            $html .= '<span class="ap-upload-remote-link">
            	'.__('add image from link', 'ap').'            	
            </span>
            <div class="ap-upload-link-rc">
        		<input type="text" name="post_remote_image" class="ap-form-control" placeholder="'.__('Enter images link', 'ap').'" data-action="post_remote_image">        		
        		<a data-action="post_image_ok" class="apicon-check ap-btn" href="#"></a>
        		<a data-action="post_image_close" class="apicon-x ap-btn" href="#"></a>
        	</div>
        </div>';
        
        if(ap_user_can_upload_image())
	        	$html .= '<script id="ap_post_upload_field" type="application/json">
        	'.json_encode(array( '__nonce' => wp_create_nonce( 'upload_image_'.get_current_user_id().'_'.get_question_id() ), 'post_id' => $post_id, 'question_id' => get_question_id() )).'
        	</script>';

    $html .= '</div>';

    return $html;

}

function ap_post_upload_hidden_form(){
	if(ap_opt('allow_upload_image'))
		return '<form id="hidden-post-upload" enctype="multipart/form-data" method="POST" style="display:none">
			<input type="hidden" name="ap_ajax_action" value="upload_post_image" />
			<input type="hidden" name="ap_form_action" value="upload_post_image" />
			<input type="hidden" name="action" value="ap_ajax" />
		</form>';
}

function ap_upload_user_file( $file = array(), $question_id ) {
	require_once( ABSPATH . 'wp-admin/includes/admin.php' );
	$file_return = wp_handle_upload( $file, array('test_form' => false, 'mimes' => array (
                'jpg|jpeg'=>'image/jpeg',
                'gif'=>'image/gif',
                'png'=>'image/png'
            ) ) );
	if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
		return false;
	} else {
		$filename = $file_return['file'];
		$attachment = array(
			'post_parent' => $question_id,
			'post_mime_type' => $file_return['type'],
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content' => '',
			'post_status' => 'inherit',
			'guid' => $file_return['url']
			);
		$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		if( 0 < intval( $attachment_id ) ) {
			return $attachment_id;
		}
	}
	return false;
}
