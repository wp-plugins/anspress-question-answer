<?php
/**
 * Form and controls of ask form
 *
 * @link http://anspress.io
 * @since 2.0.1
 * @license GPL2+
 * @package AnsPress
 */

class AnsPress_Answer_Form
{

    public function ask_form_name_field($args){
        if(!is_user_logged_in() && ap_opt('allow_anonymous'))
            $args['fields'][] = array(
                'name' => 'name',
                'label' => __('Name', 'ap'),
                'type'  => 'text',
                'placeholder'  => __('Enter your name to display', 'ap'),
                'value' => sanitize_text_field(@$_POST['name'] ),
                'order' => 12
            );

        return $args;
    }
}

new AnsPress_Answer_Form;

/**
 * Generate answer form
 * @param  boolean $editing
 * @return void
 */
function ap_answer_form($question_id, $editing = false){

    if(!ap_user_can_answer($question_id) && !$editing)
        return;

    global $editing_post;

    $is_private = sanitize_text_field( @$_POST['is_private'] );
    if($editing){
        $is_private = $editing_post->post_status == 'private_post' ? true : false;
    }

    $args = array(
        'name'              => 'answer_form',
        'is_ajaxified'      => true,
        'submit_button'     => ($editing ? __('Update answer', 'ap') : __('Post answer', 'ap')),
        'nonce_name'        => 'nonce_answer_'.$question_id,
        'fields'            => array(
            array(
                'name' => 'description',
                'type'  => 'editor',
                'value' => ( $editing ? apply_filters('the_content', $editing_post->post_content) : @$_POST['description']  ),
                'settings' => apply_filters( 'ap_answer_form_editor_settings', array(
                    'textarea_rows' => 8,
                    'tinymce'           => ap_opt('answer_text_editor') ? false : true,
                    'quicktags'         => ap_opt('answer_text_editor') ? true : false ,
                    //'teeny'=>true,
                    'media_buttons'=>false,                  
                )),
                'placeholder'  => __('Your answer..'),
            ),
            array(
                'name' => 'form_question_id',
                'type'  => 'hidden',
                'value' => ( $editing ? $editing_post->post_parent : $question_id  ),
                'order' => 20
            ),
        ),
    );

    if(ap_opt('allow_private_posts'))
        $args['fields'][] = array(
            'name' => 'is_private',
            'type'  => 'checkbox',
            'desc'  => __('Only visible to admin and moderator.', 'ap'),
            'value' => $is_private,
            'order' => 12,
            'show_desc_tip' => false
        );
    if(ap_opt('recaptcha_site_key') == '')
        $reCaptcha_html = '<div class="ap-notice red">'.__('reCaptach keys missing, please add keys', 'ap').'</div>';
    else
        $reCaptcha_html = '<div class="g-recaptcha" id="recaptcha" data-sitekey="'.ap_opt('recaptcha_site_key').'"></div><script type="text/javascript"
src="https://www.google.com/recaptcha/api.js?hl='.get_locale().'&onload=onloadCallback&render=explicit"  async defer></script><script type="text/javascript">var onloadCallback = function() {
        widgetId1 = grecaptcha.render("recaptcha", {
          "sitekey" : "'.ap_opt('recaptcha_site_key').'"
        });
      };</script>';
    if(ap_opt('enable_recaptcha'))
        $args['fields'][] = array(
            'name' => 'captcha',
            'type'  => 'custom',
            'order' => 100,
            'html' => $reCaptcha_html
        );
    
    /**
     * FILTER: ap_ask_form_fields
     * Filter for modifying $args
     * @var array
     * @since  2.0
     */
    $args = apply_filters( 'ap_answer_form_fields', $args, $editing );

    
    $args['fields'][] = array(
        'name'  => 'ap_upload',
        'type'  => 'custom',
        'html' => ap_post_upload_form(),
        'order' => 11
    );
    

    if($editing){
        $args['fields'][] = array(
            'name'  => 'edit_post_id',
            'type'  => 'hidden',
            'value' => $editing_post->ID,
            'order' => 20
        );
    }

    anspress()->form = new AnsPress_Form($args);

    echo anspress()->form->get_form();
    echo ap_post_upload_hidden_form();
}

/**
 * Generate edit question form, this is a wrapper of ap_answer_form()
 * @return void
 * @since 2.0.1
 */
function ap_edit_answer_form($question_id)
{
    ap_answer_form($question_id, true);
}