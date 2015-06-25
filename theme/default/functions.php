<?php

/**
 * This file contains theme script, styles and other theme related functions.
 *
 * This file can be overridden by creating a anspress directory in active theme folder.
 *
 * @package    AnsPress
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author    Rahul Aryan <support@anspress.io>
 */


/**
 * Enqueue scripts
 *
 */
add_action('wp_enqueue_scripts', 'ap_scripts_front', 1);
function ap_scripts_front(){
	//if(is_anspress()){
		wp_enqueue_script('jquery');				
		wp_enqueue_script('jquery-form', array('jquery'), false );			
		wp_enqueue_script('ap-functions-js', ANSPRESS_URL.'assets/ap-functions.js', 'jquery');		
		//wp_enqueue_script('ap-waypoints', ap_get_theme_url('js/jquery.waypoints.min.js'), 'jquery', AP_VERSION);
		wp_enqueue_script('ap-tooltipster', ap_get_theme_url('js/jquery.tooltipster.min.js'), 'jquery', AP_VERSION);	
		wp_enqueue_script('ap-anspress_script', ANSPRESS_URL.'assets/prod/anspress_site.min.js', 'jquery', AP_VERSION);		
		
		wp_enqueue_script('ap-peity-js', ap_get_theme_url('js/jquery.peity.min.js'), 'jquery', AP_VERSION);
		wp_enqueue_script('ap-initial.js', ap_get_theme_url('js/initial.min.js'), 'jquery', AP_VERSION);
		wp_enqueue_script('ap-scrollbar.js', ap_get_theme_url('js/jquery.scrollbar.min.js'), 'jquery', AP_VERSION);
		wp_enqueue_script('ap-js', ap_get_theme_url('prod/ap.min.js'), 'jquery', AP_VERSION);
		
		wp_enqueue_style('tooltipster', ap_get_theme_url('css/tooltipster.css'), array(), AP_VERSION);
		wp_enqueue_style('ap-style', ap_get_theme_url('css/main.css'), array(), AP_VERSION);

		$custom_css = "
                #anspress .ap-q-cells{
                        margin-left: ".(ap_opt('avatar_size_qquestion') + 10)."px;
                }
                #anspress .ap-a-cells{
                        margin-left: ".(ap_opt('avatar_size_qanswer') + 10)."px;
                }#anspress .ap-comment-content{
                        margin-left: ".(ap_opt('avatar_size_qcomment') + 15)."px;
                }";
        wp_add_inline_style( 'ap-style', $custom_css );
		
		wp_enqueue_style( 'ap-fonts', ap_get_theme_url('fonts/style.css'), array(), AP_VERSION);
		
		
		do_action('ap_enqueue');
		
		wp_enqueue_style( 'ap-overrides', ap_get_theme_url('css/overrides.css'), array(), AP_VERSION);
		
		?>
			<script type="text/javascript">
				var ajaxurl 	= '<?php echo admin_url('admin-ajax.php'); ?>',
				    ap_nonce 	= '<?php echo wp_create_nonce( "ap_ajax_nonce" ); ?>',
				    ap_max_tags = '<?php echo ap_opt('max_tags'); ?>';
				    disable_hover_card = <?php echo ap_opt('disable_hover_card') ? 'true' : 'false'; ?>;
			</script>
		<?php

		wp_localize_script( 'ap-anspress_script', 'aplang', array(
			'password_field_not_macthing' 	=> __( 'Password not matching', 'ap' ),
			'password_length_less' 			=> __( 'Password length must be 6 or higher', 'ap' ),
			'not_valid_email' 				=> __( 'Not a valid email', 'ap' ),
			'username_less' 				=> __( 'Username length must be 4 or higher', 'ap' ),
			'username_not_avilable' 		=> __( 'Username not available', 'ap' ),
			'email_already_in_use' 			=> sprintf(__( 'Email already in use. %sDo you want to reset your password?%s', 'ap' ), '<a href="'. wp_lostpassword_url() .'">', '</a>'),
			'loading' 						=> __( 'Loading', 'ap' ),
			'sending' 						=> __( 'Sending request', 'ap' ),
			'adding_to_fav' 				=> __( 'Adding question to your favorites', 'ap' ),
			'voting_on_post' 				=> __( 'Sending your vote', 'ap' ),
			'requesting_for_closing' 		=> __( 'Requesting for closing this question', 'ap' ),
			'sending_request' 				=> __( 'Submitting request', 'ap' ),
			'loading_comment_form' 			=> __( 'Loading comment form', 'ap' ),
			'submitting_your_question' 		=> __( 'Sending your question', 'ap' ),
			'submitting_your_answer' 		=> __( 'Sending your answer', 'ap' ),
			'submitting_your_comment' 		=> __( 'Sending your comment', 'ap' ),
			'deleting_comment' 				=> __( 'Deleting comment', 'ap' ),
			'updating_comment' 				=> __( 'Updating comment', 'ap' ),
			'loading_form' 					=> __( 'Loading form', 'ap' ),
			'saving_labels' 				=> __( 'Saving labels', 'ap' ),
			'loading_suggestions' 			=> __( 'Loading suggestions', 'ap' ),
			'uploading_cover' 				=> __( 'Uploading cover', 'ap' ),
			'saving_profile' 				=> __( 'Saving profile', 'ap' ),
			'sending_message' 				=> __( 'Sending message', 'ap' ),
			'loading_conversation' 			=> __( 'Loading conversation', 'ap' ),
			'loading_new_message_form' 		=> __( 'Loading new message form', 'ap' ),
			'loading_more_conversations' 	=> __( 'Loading more conversations', 'ap' ),
			'searching_conversations' 		=> __( 'Searching conversations', 'ap' ),
			'loading_message_edit_form' 	=> __( 'Loading message form', 'ap' ),
			'updating_message' 				=> __( 'Updating message', 'ap' ),
			'deleting_message' 				=> __( 'Deleting message', 'ap' ),
			'uploading' 					=> __( 'Uploading', 'ap' ),
			'error' 						=> ap_icon('error'),
			'warning' 						=> ap_icon('warning'),
			'success' 						=> ap_icon('success'),
		) );

		wp_localize_script( 'ap-site-js', 'apoptions', array(
			'ajaxlogin' => ap_opt('ajax_login'),
		));
	//}
}


if ( ! function_exists( 'ap_comment' ) ) :
	function ap_comment( $comment ) {
		$GLOBALS['comment'] = $comment;
		$class = '0' == $comment->comment_approved ? ' pending' : '';
		?>
		<li <?php comment_class('clearfix'.$class); ?> id="li-comment-<?php comment_ID(); ?>">
			<!-- comment #<?php comment_ID(); ?> -->
			<div id="comment-<?php comment_ID(); ?>" class="clearfix">
				<div class="ap-avatar ap-pull-left">
					<a href="<?php echo ap_user_link($comment->user_id); ?>">
					<!-- TODO: OPTION - Avatar size -->
					<?php echo get_avatar( $comment->user_id, 30 ); ?>
					</a>
				</div>
				<div class="ap-comment-content no-overflow">					
					<div class="ap-comment-header">
						<a href="<?php echo ap_user_link($comment->user_id); ?>" class="ap-comment-author"><?php echo ap_user_display_name($comment->user_id); ?></a>

						<?php $a=" e ";$b=" ";$time=get_option('date_format').$b.get_option('time_format').$a.get_option('gmt_offset');
								printf( ' - <a title="%4$s" href="#li-comment-%5$s" class="ap-comment-time"><time datetime="%1$s">%2$s %3$s</time></a>',
								get_comment_time( 'c' ),
								ap_human_time(get_comment_time('U')),
								__('ago', 'ap'),
								get_comment_time($time),
								$comment_id = get_comment_ID()
							);

							// Comment actions
							ap_comment_actions_buttons();
						?>
					</div>
					<div class="ap-comment-texts">
						<?php comment_text(); ?>						
					</div>
					<?php
						/**
						 * ACTION: ap_after_comment_content
						 * Action called after comment content
						 * @since 2.0.1
						 */
						do_action('ap_after_comment_content', $comment );
					?>
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'ap' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php
	}
endif;

add_action( 'widgets_init', 'ap_widgets_positions' );
function ap_widgets_positions(){
	register_sidebar( array(
		'name'         	=> __( 'AP Before', 'ap' ),
		'id'           	=> 'ap-before',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown before anspress body.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );
	
	register_sidebar( array(
		'name'         	=> __( 'AP Lists Top', 'ap' ),
		'id'           	=> 'ap-top',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown before questions list.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );
	
	register_sidebar( array(
		'name'         	=> __( 'AP Sidebar', 'ap' ),
		'id'           	=> 'ap-sidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown in AnsPress sidebar.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );
	
	register_sidebar( array(
		'name'         	=> __( 'AP Question Sidebar', 'ap' ),
		'id'           	=> 'ap-qsidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown in question page sidebar.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );

	register_sidebar( array(
		'name'         	=> __( 'AP Category Page', 'ap' ),
		'id'           	=> 'ap-category',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown in category listing page.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );

	register_sidebar( array(
		'name'         	=> __( 'AP Tag page', 'ap' ),
		'id'           	=> 'ap-tag',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown in tag listing page.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );

	register_sidebar( array(
		'name'         	=> __( 'AP about user', 'ap' ),
		'id'           	=> 'ap-user-about',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' 	=> '</div>',
		'description'  	=> __( 'Widgets in this area will be shown in about user page.', 'ap' ),
		'before_title' 	=> '<h3 class="ap-widget-title">',
		'after_title'  	=> '</h3>',
	) );
}

/* for overriding icon in social login plugin */
/*function ap_social_login_icons( $provider_id, $provider_name, $authenticate_url )
{
	?>
	<a rel = "nofollow" href = "<?php echo $authenticate_url; ?>" data-provider = "<?php echo  $provider_id ?>" class = "wp-social-login-provider wp-social-login-provider-<?php echo strtolower( $provider_id ); ?> btn btn-<?php echo strtolower( $provider_id ); ?>">
		<i class="ap-apicon-<?php echo strtolower( $provider_id ); ?>"></i> <span><?php echo $provider_name; ?></span>
	</a>
	<?php
}
add_filter( 'wsl_render_login_form_alter_provider_icon_markup', 'ap_social_login_icons', 10, 3 );*/