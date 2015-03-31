<?php
/**
 * Answers class
 *
 * @package   AnsPress
 * @author    Rahul Aryan <admin@rahularyan.com>
 * @license   GPL-2.0+
 * @link      http://rahularyan.com
 * @copyright 2014 Rahul Aryan
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('Answers_Query')):

/**
 * Question
 *
 * This class is for retriving answers based on $args
 */
class Answers_Query extends WP_Query {

    public $args = array();

    /**
     * Initialize class
     * @param array $args
     * @access public
     * @since  2.0
     */
    public function __construct( $args = array() ) {

        global $answers;

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        if(isset($args['question_id']))
            $question_id = $args['question_id'];

        if(isset($args['orderby']))
            $orderby = $args['orderby'];
        else
            $orderby = (get_query_var('ap_sort')) ? get_query_var('ap_sort') : 'active';

        $defaults = array(
            'post_status'       => array('publish', 'moderate', 'private_post'),
            'showposts'         => ap_opt('answers_per_page'),
            'orderby'           => $orderby,
            'paged'             => $paged,
            'only_best_answer'  => false,
            'include_best_answer'  => false,
        );

        $this->args = wp_parse_args( $args, $defaults );
        
        if(!empty($question_id))
            $this->args['post_parent'] = $question_id;

        $this->pre_answers();

        do_action('ap_pre_get_answers', $this);

        $this->args['post_type'] = 'answer';        

        $args = $this->args;

        /**
         * Initialize parent class
         */
        parent::__construct( $args );

        remove_action('ap_pre_get_answers', $this);
    }

    public function pre_answers(){
        add_action('ap_pre_get_answers', array($this, 'orderby_answers'));
    }

    /**
     * Modify orderby args
     * @return void
     */
    public function orderby_answers(){
        $this->args['meta_query'] = array();
        switch ( $this->args[ 'orderby' ] ) {
            case 'voted' :
                $this->args[ 'orderby'] = 'meta_value_num' ;
                $this->args['meta_query']  = array(
                    'relation' => 'AND',
                    array(
                        'key'       => ANSPRESS_VOTE_META,
                    )
                );
            break;
            case 'oldest' :
                $this->args['orderby'] = 'meta_value date';
                $this->args['order'] = 'ASC';
            break;
            case 'newest' :
                $this->args['orderby'] = 'meta_value date';
                $this->args['order'] = 'DESC';
            break;
            default :
                $this->args['orderby'] = 'meta_value';
                $this->args['meta_key'] = ANSPRESS_UPDATED_META;
                $this->args['meta_query']  = array(
                    'relation' => 'AND',
                    array(
                        'key' => ANSPRESS_UPDATED_META
                    )
                );
            break;
        }

        if(!$this->args['include_best_answer'])
            $this->args['meta_query'][] = array(
                'key'           => ANSPRESS_BEST_META,
                'type'          => 'BOOLEAN',
                'compare'       => '!=',
                'value'         => '1'
            );
         
    }

}

endif;

/**
 * Display answers of a question
 * @param  array  $args
 * @return void
 * @since  2.0
 */
function ap_get_answers($args = array()){
    global $answers;

    if(empty($args['question_id']))
        $args['question_id'] = get_question_id();

    $sort = get_query_var('ap_sort');

    if(empty($sort ))
        $sort = ap_opt('answers_sort');

    $args['orderby'] = $sort;
    
    
    $answers = new Answers_Query($args);
    
    // get answer sorting tab
    echo '<div id="ap-answers-c">';             
        include(ap_get_theme_location('answers.php'));      
    echo '</div>';
}

/** 
 * Get select answer object
 * @since   2.0
 */
function ap_get_best_answer($question_id = false){
    global $answers;
    
    if(!$question_id) 
        $question_id = get_question_id();

    $answer_id = ap_selected_answer($question_id);

    $answers = new WP_Query(array('p' => $answer_id, 'post_type' => 'answer'));    
    
    while ( $answers->have_posts() ) : $answers->the_post(); 
        include(ap_get_theme_location('answer.php'));
    endwhile;
}