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


class AnsPress_Vote_Ajax extends AnsPress_Ajax
{
	public function __construct()
	{
		add_action( 'ap_ajax_subscribe_question', array($this, 'subscribe_question') ); 
		add_action( 'ap_ajax_vote', array($this, 'vote') ); 
	}

	public function subscribe_question()
	{
		if(!wp_verify_nonce( $_POST['__nonce'], 'subscribe_'. (int)$_POST['question_id'] ) ){
			ap_send_json(ap_ajax_responce('something_wrong'));
			return;
		}

		if(!is_user_logged_in()){
			ap_send_json(ap_ajax_responce('please_login'));
			return;
		}

		if(wp_verify_nonce( $_POST['__nonce'], 'subscribe_'. (int)$_POST['question_id'] )){

			$question_id = (int)$_POST['question_id'];

			$is_subscribed = ap_is_user_subscribed( $question_id );	
			$userid		 = get_current_user_id();	

			if($is_subscribed){
				// if already subscribed then remove	
				$row = ap_remove_vote('subscriber', $userid, $question_id);
				
				$counts = ap_post_subscribers_count($question_id);
				
				//update post meta
				update_post_meta($question_id, ANSPRESS_SUBSCRIBER_META, $counts);
				
				//register an action
				do_action('ap_removed_subscribe', $question_id, $counts);

				ap_send_json(ap_ajax_responce('unsubscribed'));
				return;
				
				$title = __('Add to subscribe list', 'ap');
				$action = 'removed';
				$message = __('Removed question from your subscribe list', 'ap');
			}else{
				$row = ap_add_vote($userid, 'subscriber', $question_id);
				$counts = ap_post_subscribers_count($question_id);
				
				//update post meta
				update_post_meta($question_id, ANSPRESS_SUBSCRIBER_META, $counts);
				
				//register an action
				do_action('ap_added_subscribe', $question_id, $counts);
				
				ap_send_json(ap_ajax_responce('subscribed'));
			}
			
		}
	}

	/**
	 * Process voting button
	 * @return void
	 * @since 2.0.1.1
	 */
	public function vote()
	{
		$post_id = (int)$_POST['post_id'];

		if(!wp_verify_nonce( $_POST['__nonce'], 'vote_'. $post_id ) ){
			ap_send_json(ap_ajax_responce('something_wrong'));
			return;
		}

		if(!is_user_logged_in()){
			ap_send_json(ap_ajax_responce('please_login'));
			return;
		}

		$post = get_post($post_id);
		if($post->post_author == get_current_user_id()){
			ap_send_json(ap_ajax_responce('cannot_vote_own_post'));
			return;
		}

		$type = sanitize_text_field( $_POST['type'] );

		$type 	= $type == 'up' ? 'vote_up' : 'vote_down' ;
		$userid = get_current_user_id();
		
		$is_voted = ap_is_user_voted($post_id, 'vote', $userid) ;

		if(is_object($is_voted) && $is_voted->count > 0){
			// if user already voted and click that again then reverse
			if($is_voted->type == $type){
				$row = ap_remove_vote($type, $userid, $post_id);
				$counts = ap_post_votes($post_id);
				
				//update post meta
				update_post_meta($post_id, ANSPRESS_VOTE_META, $counts['net_vote']);
				
				do_action('ap_undo_vote', $post_id, $counts);
				
				$action = 'undo';
				$count = $counts['net_vote'] ;
				$message = __('Your vote has been removed', 'ap');
				
				ap_do_event('undo_'.$type, $post_id, $counts);

				ap_send_json(ap_ajax_responce(array('action' => $action, 'type' => $type, 'count' => $count, 'message' => 'undo_vote')));
			}else{
				$result = ap_send_json(ap_ajax_responce('undo_vote_your_vote'));
			}				
				
		}else{
			
			$row = ap_add_vote($userid, $type, $post_id);				
			$counts = ap_post_votes($post_id);
			
			//update post meta
			update_post_meta($post_id, ANSPRESS_VOTE_META, $counts['net_vote']);				
			do_action('ap_voted_'.$type, $post_id, $counts);
			
				
			$action = 'voted';
			$count = $counts['net_vote'] ;

			ap_do_event($type, $post_id, $counts);

			ap_send_json(ap_ajax_responce(array('action' => $action, 'type' => $type, 'count' => $count, 'message' => 'voted')));
		}			

	}

}
new AnsPress_Vote_Ajax();

class anspress_vote
{
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
    public function __construct()
    {
		add_action( 'the_post', array($this, 'ap_append_vote_count') );


		
		// vote for closing, ajax request
		add_action( 'wp_ajax_ap_vote_for_close', array($this, 'ap_vote_for_close') ); 
		add_action( 'wp_ajax_nopriv_ap_vote_for_close', array($this, 'ap_nopriv_vote_for_close') ); 
		
		// Follow user
		add_action( 'wp_ajax_ap_follow', array($this, 'ap_follow') ); 
		add_action( 'wp_ajax_nopriv_ap_follow', array($this, 'ap_follow') ); 
		
		add_action( 'wp_ajax_ap_submit_flag_note', array($this, 'ap_submit_flag_note') ); 
    }

	/**
	 * Append variable to post Object
	 * @param  Object $post
	 * @return object
	 * @since unknown
	 */
	function ap_append_vote_count($post){
		if($post->post_type == 'question' || $post->post_type == 'answer'){
             if(is_object($post)){

                $votes = ap_post_votes($post->ID);              
                // net vote
                $post->voted_up     = $votes['voted_up'];
                $post->voted_down   = $votes['voted_down'];
                $post->net_vote     = $votes['voted_up'] - $votes['voted_down'];
            }
        }
	}


		
	// add to subscribe ajax action	 
	

	
	function ap_add_to_subscribe_nopriv(){
		echo json_encode(array('action'=> false, 'message' =>__('Please login for adding question to your subscribe', 'ap')));
		die();
	}
	
	function ap_vote_for_close(){
		$args = explode('-', sanitize_text_field($_POST['args']));
		if(wp_verify_nonce( $args[1], 'close_'.$args[0] )){

			$voted_closed = ap_is_user_voted_closed($args[0]);
			$type =  'close';
			$userid = get_current_user_id();
			
			if($voted_closed){
				// if already in voted for close then remove it
				$row = ap_remove_vote($type, $userid, $args[0]);
	
				$counts = ap_post_close_vote($args[0]);
				//update post meta
				update_post_meta($args[0], ANSPRESS_CLOSE_META, $counts);
				
				$result = apply_filters('ap_cast_unclose_result', array('row' => $row, 'action' => 'removed', 'text' => __('Close','ap').' ('.$counts.')', 'title' => __('Vote for closing', 'ap'), 'message' => __('Your close request has been removed', 'ap') ));
				
			}else{
				$row = ap_add_vote($userid, $type, $args[0]);

				$counts = ap_post_close_vote($args[0]);
				//update post meta
				update_post_meta($args[0], ANSPRESS_CLOSE_META, $counts);

				$result = apply_filters('ap_cast_close_result', array('row' => $row, 'action' => 'added', 'text' => __('Close','ap').' ('.$counts.')', 'title' => __('Undo your vote', 'ap'), 'message' => __('Your close request has been sent', 'ap') ));

			}
			
		}else{
			$result = array('action' => false, 'message' => _('Something went wrong', 'ap'));
		}
		
		die(json_encode($result));
	}
	
	function ap_nopriv_vote_for_close(){
		echo json_encode(array('action'=> false, 'message' =>__('Please login for requesting closing this question.', 'ap')));
		die();
	}
	
	public function ap_follow(){
		$args = $_POST['args'];
		if(wp_verify_nonce( $args['nonce'], 'follow_'.$args['user'] )){
			$userid = (int)sanitize_text_field($args['user']);
			
			$user_following = ap_is_user_voted($userid, 'follow', get_current_user_id());
			
			$user 			= get_userdata( $userid );
			$user_name 		= $user->data->display_name;
			if (!is_user_logged_in()){
				$action = 'pleazelogin';
				$message = sprintf(__('Register or log in to follow %s', 'ap'), $user_name);
			}	
			elseif(!$user_following){
				$row 	= ap_add_vote(get_current_user_id(), 'follow', $userid);
				$action = 'follow';
				$text 	= __('Unfollow','ap');
				$title 	= sprintf(__('Unfollow %s', 'ap'), $user_name);
				$message = sprintf(__('You are now following %s', 'ap'), $user_name);
			}else{
				$row = ap_remove_vote('follow', get_current_user_id(), $userid);
				$action = 'unfollow';
				$text 	= __('Follow','ap');
				$title 	= sprintf(__('Follow %s', 'ap'), $user_name);
				$message = sprintf(__('You unfollowed %s', 'ap'), $user_name);
			}
				
			if($row !== FALSE){
				$followers = ap_count_vote(false, 'follow', $userid);
				$following = ap_count_vote(get_current_user_id(), 'follow');
				update_user_meta( $userid, AP_FOLLOWERS_META, $followers);
				update_user_meta( get_current_user_id(), AP_FOLLOWING_META, $following);
				
				
				
				$result = apply_filters('ap_follow_result', array('row' => $row, 'action' => $action, 'text' => $text, 'id' => $userid, 'title' => $title, 'message' => $message, 'following_count' => $following, 'followers_count' => $followers ));
				
				echo json_encode($result);
			}else{
				echo json_encode(array('action' => false, 'message' => _('Unable to process your request, please try again.', 'ap')));
			}

		}else{
			echo json_encode(array('action' => false, 'message' => _('Something went wrong', 'ap')));
		}
		die();
	}
	
	// vote for closing, ajax request 
	public function ap_submit_flag_note(){
		$args = explode('-', sanitize_text_field($_POST['args']));
		$note_id = sanitize_text_field($_POST['note_id']);
		$other_note = sanitize_text_field($_POST['other_note']);
		
		if(wp_verify_nonce( $args[1], 'flag_submit_'.$args[0] ) && is_user_logged_in()){
			global $wpdb;
			$userid = get_current_user_id();
			$is_flagged = ap_is_user_flagged($args[0]);
			
			if($is_flagged){
				// if already then return
				echo json_encode(array('action' => false, 'message' => __('You already flagged this post', 'ap')));			
			}else{
				if($note_id != 'other')
					$row = ap_add_flag($userid, $args[0], $note_id);
				else
					$row = ap_add_flag($userid, $args[0], NULL, $other_note);
					
				$counts = ap_post_flag_count($args[0]);
				//update post meta
				update_post_meta($args[0], ANSPRESS_FLAG_META, $counts);
				
				echo json_encode(array('row' => $row, 'action' => 'flagged', 'text' => __('Flag','ap').' ('.$counts.')','title' =>  __('You have flagged this post', 'ap'), 'message' => __('This post is notified to moderator. Thank you for helping us', 'ap')));
			}
			
		}else{
			echo '0'.__('Please try again', 'ap');	
		}
		
		die();
	}
}

function ap_add_vote($userid, $type, $actionid){	
	return ap_add_meta($userid, $type, $actionid );
}

function ap_remove_vote($type, $userid, $actionid){
	return ap_delete_meta(array('apmeta_type' => $type, 'apmeta_userid' => $userid, 'apmeta_actionid' => $actionid));
}

function ap_count_vote($userid = false, $type, $actionid =false, $value = 1){
	global $wpdb;
	if(!$userid){
		return ap_meta_total_count($type, $actionid);		
	}elseif($userid && !$actionid){
		return ap_meta_total_count($type, false, $userid);
	}
}

// get $post up votes
function ap_up_vote($echo = false){
	global $post;
	
	if($echo) echo $post->voted_up;
	else return $post->voted_up;
}

// get $post down votes
function ap_down_vote($echo = false){
	global $post;
	
	if($echo) echo $post->voted_down;
	else return $post->voted_down;
}

// get $post net votes
function ap_net_vote($post =false){
	if(!$post)
		global $post;
	$net= $post->net_vote;
	return $net ? $net : 0;
}

function ap_net_vote_meta($post_id =false){
	if(!$post_id)
		$post_id = get_the_ID();
	$net= get_post_meta($post_id, ANSPRESS_VOTE_META, true);
	return $net ? $net : 0;
}

function ap_post_votes($postid){
	$vote = array();
	//voted up count
	$vote['voted_up'] = ap_meta_total_count('vote_up', $postid);
	
	//voted down count
	$vote['voted_down'] = ap_meta_total_count('vote_down', $postid);
	
	// net vote
	$vote['net_vote'] = $vote['voted_up'] - $vote['voted_down'];

	return $vote;
}

/**
 * Check if user voted on given post.
 * @param  	int $actionid
 * @param  	string $type     
 * @param  	int $userid   
 * @return 	boolean           
 * @since 	2.0
 */
function ap_is_user_voted($actionid, $type, $userid = false){
	if(!$userid)
		$userid = get_current_user_id();

	if($type == 'vote' && is_user_logged_in()){
		global $wpdb;
		
		$query = $wpdb->prepare('SELECT apmeta_type as type, IFNULL(count(*), 0) as count FROM ' .$wpdb->prefix .'ap_meta where (apmeta_type = "vote_up" OR apmeta_type = "vote_down") and apmeta_userid = %d and apmeta_actionid = %d GROUP BY apmeta_type', $userid, $actionid);
		
		$key = md5($query);

		$user_done = wp_cache_get($key, 'counts');

		if($user_done === false){
			$user_done = $wpdb->get_row($query);	
			wp_cache_set($key, $user_done, 'counts');
		}
			
		return $user_done;
		
	}elseif(is_user_logged_in()){
		$done = ap_meta_user_done($type, $userid, $actionid);
		return $done > 0 ? true : false;
	}
	return false;
}

//check if user added post to subscribe
function ap_is_user_subscribed($postid){
	if(is_user_logged_in()){
		$userid = get_current_user_id();
		$done = ap_meta_user_done('subscriber', $userid, $postid);
		return $done > 0 ? true : false;
	}
	return false;
}

function ap_post_subscribers_count($postid = false){
	//subscribe count
	global $post;

	$postid = $postid ? $postid : $post->ID;
	return ap_meta_total_count('subscriber', $postid);
}

/**
 * Output voting button
 * @param  int $post 
 * @return void
 * @since 0.1
 */
function ap_vote_btn($post = false){
	if(!$post)
		global $post;
		
	$nonce 	= wp_create_nonce( 'vote_'.$post->ID );
	$vote 	= ap_is_user_voted( $post->ID , 'vote');

	$voted 	= isset($vote) ? true : false;
	$type 	= isset($vote) ? $vote->type : '';
	?>
		<div data-id="<?php echo $post->ID; ?>" class="ap-vote net-vote" data-action="vote">
			<a class="<?php echo ap_icon('vote_up') ?> ap-tip vote-up<?php echo $voted ? ' voted' :''; echo ($type == 'vote_down') ? ' disable' :''; ?>" data-query="ap_ajax_action=vote&type=up&post_id=<?php echo $post->ID; ?>&__nonce=<?php echo $nonce ?>" href="#" title="<?php _e('Up vote this post', 'ap'); ?>"></a>
			
			<span class="net-vote-count" data-view="ap-net-vote" itemprop="upvoteCount"><?php echo ap_net_vote(); ?></span>
			
			<a data-tipposition="bottom" class="<?php echo ap_icon('vote_down') ?> ap-tip vote-down<?php echo $voted ? ' voted' :''; echo ($type == 'vote_up') ? ' disable' :''; ?>" data-query="ap_ajax_action=vote&type=down&post_id=<?php echo $post->ID; ?>&__nonce=<?php echo $nonce ?>" href="#" title="<?php _e('Down vote this post', 'ap'); ?>"></a>
		</div>
	<?php
}

/**
 * Output subscribe btn HTML
 * @param  object $question  post Object
 * @return string
 * @since 2.0.1
 */
function ap_subscribe_btn_html($post = false){
	if(!$post)
		global $post;
	
	$total_favs = ap_post_subscribers_count($post->ID);
	$subscribed = ap_is_user_subscribed($post->ID);

	$nonce = wp_create_nonce( 'subscribe_'.$post->ID );
	$title = (!$total_favs) ? (__('Subscribe', 'ap')) : (__('Subscribed', 'ap'));

	?>
		<div class="ap-subscribe<?php echo ($subscribed) ? ' active' :''; ?>">
			<span class="ap-subscribe-label"><?php echo $title ?></span>
			<span id="<?php echo 'subscribe_'.$post->ID; ?>" class="ap-radio-btn subscribe-btn <?php echo ($subscribed) ? ' active' :''; ?>" data-query="ap_ajax_action=subscribe_question&question_id=<?php echo $post->ID ?>&__nonce=<?php echo $nonce ?>" data-action="ap_subscribe" data-args="<?php echo $post->ID.'-'.$nonce; ?>"></span>
			<!-- <span class="ap-subscribers-count"> 
				<?php  
					/*if( $total_favs =='1' && $subscribed)
						_e('You are subscribed', 'ap'); 
					elseif($subscribed)
						printf( __( 'You and %s people subscribed this question', 'ap' ), ($total_favs -1));
					elseif($total_favs == 0)
						 _e( 'Subscribe this question', 'ap' );
					else
						printf( _n( '%d people subscribed this question', '%d peoples subscribed this question', $total_favs, 'ap' ), $total_favs); */
				?>
			</span> -->
		</div>
	<?php
}



/* ------------close button----------------- */

// post close vote count
function ap_post_close_vote($postid = false){
	global $post;

	$postid = $postid ? $postid : $post->ID;
	return ap_meta_total_count('close', $postid);
}

//check if user voted for close
function ap_is_user_voted_closed($postid = false){	
	if(is_user_logged_in()){
		global $post;
		$postid = $postid ? $postid : $post->ID;
		$userid = get_current_user_id();
		$done = ap_meta_user_done('close', $userid, $postid);
		return $done > 0 ? true : false;		
	}
	return false;
}

//TODO: re-add closing system as an extension
function ap_close_vote_html(){
	if(!is_user_logged_in())
		return;
		
	global $post;
	$nonce = wp_create_nonce( 'close_'.$post->ID );
	$title = (!$post->voted_closed) ? (__('Vote for closing', 'ap')) : (__('Undo your vote', 'ap'));
	?>
		<a id="<?php echo 'close_'.$post->ID; ?>" data-action="close-question" class="close-btn<?php echo ($post->voted_closed) ? ' closed' :''; ?>" data-args="<?php echo $post->ID.'-'.$nonce; ?>" href="#" title="<?php echo $title; ?>">
			<?php _e('Close ', 'ap'); echo ($post->closed > 0 ? '<span>('.$post->closed.')</span>' : ''); ?>
		</a>	
	<?php
}


/* ---------------Flag btn-------------------
------------------------------------------- */
function ap_add_flag($userid, $actionid, $value =NULL, $param =NULL){	
	return ap_add_meta($userid, 'flag', $actionid, $value, $param );
}

// count flags on the post
function ap_post_flag_count($postid=false){
	global $post;

	$postid = $postid ? $postid : $post->ID;
	return ap_meta_total_count('flag', $postid);
}

//check if user flagged on post
function ap_is_user_flagged($postid = false){
	if(is_user_logged_in()){
		global $post;
		$postid = $postid ? $postid : $post->ID;
		$userid = get_current_user_id();
		$done = ap_meta_user_done('flag', $userid, $postid);
		return $done > 0 ? true : false;
	}
	return false;
}

/**
 * Flag button html
 * @return string 
 * @since 0.9
 */
function ap_flag_btn_html($echo = false){
	if(!is_user_logged_in())
		return;
		
	global $post;
	$flagged 	= ap_is_user_flagged();
	$total_flag = ap_post_flag_count();
	$nonce 		= wp_create_nonce( 'flag_'.$post->ID );
	$title 		= (!$flagged) ? (__('Flag this post', 'ap')) : (__('You have flagged this post', 'ap'));
	
	$output ='<a id="flag_'.$post->ID.'" data-action="flag-modal" class="flag-btn'. (!$flagged ? ' can-flagged' :'') .'" data-args="'.$post->ID.'-'.$nonce.'" href="#flag_modal_'.$post->ID.'" title="'.$title.'">'.ap_icon('flag', true). __('Flag ', 'ap') . ($total_flag > 0 ? ' <span>('.$total_flag.')</span>':'').'</a>';

	if($echo)
		echo $output;
	else
		return $output;
}

// vote for closing, ajax request
add_action( 'wp_ajax_ap_flag_note_modal', 'ap_flag_note_modal' );  
function ap_flag_note_modal(){
	$args = explode('-', sanitize_text_field($_POST['args']));
	if(wp_verify_nonce( $args[1], 'flag_'.$args[0] )){
		$nonce = wp_create_nonce( 'flag_submit_'.$args[0] );
		?>
		<div class="ap-modal flag-note" id="<?php echo 'flag_modal_'.$args[0]; ?>" tabindex="-1" role="dialog">
			<div class="ap-modal-bg"></div>
			<div class="ap-modal-content">
				<div class="ap-modal-header">					
					<h4 class="ap-modal-title"><?php _e('I am flagging this post because', 'ap'); ?><span class="ap-modal-close">&times;</span></h4>
				</div>
				<div class="ap-modal-body">
				<?php 
					if(ap_opt('flag_note'))
					foreach( ap_opt('flag_note') as $k => $note){
						echo '<div class="note clearfix">';
						echo '<div class="note-radio pull-left"><input type="radio" name="note_id" value="'.$k.'" /></div>';
						echo '<div class="note-desc">';
						echo '<h4>'.$note['title'].'</h4>';
						echo '<p>'.$note['description'].'</p>';
						echo '</div>';
						echo '</div>';
					}
				?>
				<div class="note clearfix">
					<div class="note-radio pull-left"><input type="radio" name="note_id" value="other" /></div>
					<div class="note-desc">
						<h4><?php _e('Other (needs moderator attention)', 'ap'); ?></h4>
						<p><?php _e('This post needs a moderator\'s attention. Please describe exactly what\'s wrong. ', 'ap'); ?></p>
						<textarea id="other-note" class="other-note" name="other_note"></textarea>
					</div>
				</div>
				</div>
				<div class="ap-modal-footer">
					<input id="submit-flag-question" type="submit" data-update="<?php echo $args[0]; ?>" data-args="<?php echo $args[0].'-'.$nonce; ?>" class="btn btn-primary btn-sm" value="<?php _e('Flag post', 'ap'); ?>" />
				</div>
			</div>
		  
		  
		</div>
		<?php
		
	}else{
		echo '0_'.__('Please try again', 'ap');	
	}
	
	die();
}

function ap_follow_btn_html($userid, $small = false){
	if(get_current_user_id() == $userid)
		return;
		
	$followed = ap_is_user_voted($userid, 'follow', get_current_user_id());
	$text = $followed ? __('Unfollow', 'ap') : __('Follow', 'ap');
	echo '<a class="btn ap-btn ap-follow-btn '.($followed ? 'ap-unfollow '.ap_icon('unfollow') : ap_icon('follow')).($small ? ' ap-tip' : '').'" href="#" data-action="ap-follow" data-args=\''.json_encode(array('user' => $userid, 'nonce' => wp_create_nonce( 'follow_'.$userid))).'\' title="'.$text.'">'.($small ? '' : $text).'</a>';
}