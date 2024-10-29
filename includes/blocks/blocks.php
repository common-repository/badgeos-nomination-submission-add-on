<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers the rest api routes
 *
 * @param $categories
 * @param $post
 *
 * @return none
 */
function badgeos_submission_nominations_register_api_end_points() {

    register_rest_route( 'badgeos', '/block-nominations-list', array(
      'methods' => 'GET', 
      'callback' => 'badgeos_subnom_block_nominations_list',
      'permission_callback' => '__return_true'
    ) ); 

    register_rest_route( 'badgeos', '/block-submissions-list', array(
      'methods' => 'GET',
      'callback' => 'badgeos_subnom_block_submissions_list',
      'permission_callback' => '__return_true'
    ) );
    
}
add_action( 'rest_api_init', 'badgeos_submission_nominations_register_api_end_points' );

/**
 * Returns the nominations list.
 *
 * @param $data
 *
 * @return nominations
 */
function badgeos_subnom_block_nominations_list( $data ) {
    $badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
    $types = badgeos_get_achievement_types_slugs();
    $step_key = array_search( trim( $badgeos_settings['achievement_step_post_type'] ), $types );
    if ( $step_key )
        unset( $types[$step_key] );
  
      $args = array(
          'post_type'                => $types,
          'suppress_filters'         => false,
          'achievement_relationsihp' => 'any',
          'posts_per_page' =>	-1,
          'post_status'=> 'publish'
      );
     
    // Get our achievement posts
    $achievements = get_posts( $args );
    $posts = array();
    foreach( $achievements as $achievement ) {
        $earned_by = get_post_meta( $achievement->ID, '_badgeos_earned_by', true );
		if ( $earned_by == 'nomination' ) {
            $posts[] = [ 'value'=> $achievement->ID, 'label'=>$achievement->post_title];
        }
    }
  
       return $posts;
}

/**
 * Returns the submission list.
 *
 * @param $data
 *
 * @return submissions
 */
function badgeos_subnom_block_submissions_list( $data ) {
    $badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
    $types = badgeos_get_achievement_types_slugs();
    $step_key = array_search( trim( $badgeos_settings['achievement_step_post_type'] ), $types );
    if ( $step_key )
        unset( $types[$step_key] );
  
      $args = array(
          'post_type'                => $types,
          'suppress_filters'         => false,
          'achievement_relationsihp' => 'any',
          'posts_per_page' =>	-1,
          'post_status'=> 'publish'
      );
     
    // Get our achievement posts
    $achievements = get_posts( $args );
    $posts = array();
    foreach( $achievements as $achievement ) {
        
        $earned_by = get_post_meta( $achievement->ID, '_badgeos_earned_by', true );
		if ( $earned_by == 'submission' || $earned_by == 'submission_auto' ) {
            $posts[] = [ 'value'=> $achievement->ID, 'label'=>$achievement->post_title];
        }
    }
  
       return $posts;
}

/**
 * Renders the nomination form block
 *
 * @param $attributes
 *
 * @return output html
 */
function badgeos_nomination_submission_render_nomination_form_block( $attributes ) {

    if( isset( $attributes['achievement'] ) && !empty( $attributes['achievement'] ) ){
        $data = json_decode( $attributes['achievement'] );
        if( ! empty( $data->value  ) ) {
            return do_shortcode( '[badgeos_nomination achievement_id="'.$data->value.'"]' );
        } else {
            return '<div class="inner-content">'.__( 'No nomination found.', 'badgeos' ).'</div>';
        }
    } else {
        return '<div class="inner-content">'.__( 'Please, select a nomination to display.', 'badgeos' ).'</div>';
    }
}

/**
 * Renders the submission form block
 *
 * @param $attributes
 *
 * @return output html
 */
function badgeos_nomination_submission_render_submission_form_block( $attributes ) {

    if( isset( $attributes['achievement'] ) && !empty( $attributes['achievement'] ) ){
        $data = json_decode( $attributes['achievement'] );
        if( ! empty( $data->value  ) ) {
            return do_shortcode( '[badgeos_submission achievement_id="'.$data->value.'"]' );
        } else {
            return '<div class="inner-content">'.__( 'No submission found.', 'badgeos' ).'</div>';
        }
    } else {
        return '<div class="inner-content">'.__( 'Please, select a submission to display.', 'badgeos' ).'</div>';
    }

}

/**
 * Renders the nominations list block
 *
 * @param $attributes
 *
 * @return output html
 */
function badgeos_nomination_submission_render_nominations_list_blocks( $attributes ) {

    $param = '';
    if( !empty( $attributes['limit'] ) && intval( $attributes['limit'] ) > 0 ) {
        $param .= ' limit="'.sanitize_text_field( $attributes['limit'] ).'"';
    }
    if( !empty( $attributes['status'] ) ) {
        $param .= ' status="'.sanitize_text_field( $attributes['status'] ).'"';
    }
    if( !empty( $attributes['show_filter'] ) ) {
        $param .= ' show_filter="'.sanitize_text_field( $attributes['show_filter'] ).'"';
    }
    if( !empty( $attributes['show_search'] ) ) {
        $param .= ' show_search="'.sanitize_text_field( $attributes['show_search'] ).'"';
    } else {
        $param .= ' show_search="true"';
    }

    return do_shortcode( '[badgeos_nominations '.$param.']' );
}

/**
 * Renders the submission list block
 *
 * @param $attributes
 *
 * @return output html
 */
function badgeos_nomination_submission_render_submission_list_blocks( $attributes ) {

    $param = '';
    if( !empty( $attributes['limit'] ) && intval( $attributes['limit'] ) > 0 ) {
        $param .= ' limit="'.sanitize_text_field( $attributes['limit'] ).'"';
    }
    if( !empty( $attributes['status'] ) ) {
        $param .= ' status="'.sanitize_text_field( $attributes['status'] ).'"';
    }
    if( !empty( $attributes['show_filter'] ) ) {
        $param .= ' show_filter="'.sanitize_text_field( $attributes['show_filter'] ).'"';
    } else {
        $param .= ' show_filter="true"';
    }
    if( !empty( $attributes['show_search'] ) ) {
        $param .= ' show_search="'.sanitize_text_field( $attributes['show_search'] ).'"';
    } else {
        $param .= ' show_search="true"';
    }
    if( !empty( $attributes['show_attachments'] ) ) {
        $param .= ' show_attachments="'.sanitize_text_field( $attributes['show_attachments'] ).'"';
    }
    if( !empty( $attributes['show_comments'] ) ) {
        $param .= ' show_comments="'.sanitize_text_field( $attributes['show_comments'] ).'"';
    }

    return do_shortcode( '[badgeos_submissions '.$param.']' );
}



/**
 * Registers the block types
 *
 * @return none
 */
function badgeos_nomination_submission_render_my_php_block(  ) {

    register_block_type( 'bos/badgeos-submission-form-block', array(
        'render_callback' => 'badgeos_nomination_submission_render_submission_form_block',
        'category' => 'badgeos-blocks',
        'attributes' => array(
            'achievement' => array(
                'type' => 'string',
                'default'=> ''
            )
        )
    ));

    register_block_type( 'bos/badgeos-nomination-form-block', array(
        'render_callback' => 'badgeos_nomination_submission_render_nomination_form_block',
        'category' => 'badgeos-blocks',
        'attributes' => array(
            'achievement' => array(
                'type' => 'string',
                'default'=> ''
            )
        )
    ));

    register_block_type( 'bos/badgeos-nominations-list-block', array(
        'render_callback' => 'badgeos_nomination_submission_render_nominations_list_blocks',
        'category' => 'badgeos-blocks',
        'attributes' => array(
            'limit' => array(
                'type' => 'string',
                'default'=> '10'
            ),
            'status' => array(
                'type' => 'string',
                'default'=> 'all'
            ),
            'show_filter' => array(
                'type' => 'string',
                'default'=> 'true'
            ),
            'show_search' => array(
                'type' => 'string',
                'default'=> 'true'
            ),
        )
    ));

    register_block_type( 'bos/badgeos-submission-list-block', array(
        'render_callback' => 'badgeos_nomination_submission_render_submission_list_blocks',
        'category' => 'badgeos-blocks',
        'attributes' => array(
            'limit' => array(
                'type' => 'string',
                'default'=> '10'
            ),
            'status' => array(
                'type' => 'string',
                'default'=> 'all'
            ),
            'show_filter' => array(
                'type' => 'string',
                'default'=> 'true'
            ),
            'show_search' => array(
                'type' => 'string',
                'default'=> 'true'
            ),
            'show_attachments' => array(
                'type' => 'string',
                'default'=> 'true'
            ),
            'show_comments' => array(
                'type' => 'string',
                'default'=> 'true'
            )
        )
    ));
}
add_action( 'init', 'badgeos_nomination_submission_render_my_php_block');