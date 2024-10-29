<?php

 /**
 * Register custom meta box for BadgeOS submission.
 *
 * @since  1.0.0
 * @param  array $field The field data array
 * @param  string $meta The stored meta for this field (which will always be blank)
 * @return string       HTML markup for our field
 * @param  none
 * @return none 
 */
function badgeos_subnom_submission_metaboxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_badgeos_';

    // Grab our achievement types as an array
    $achievement_types = badgeos_get_achievement_types_slugs();

    // Setup our $post_id, if available
    $post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

    // New Achievement Types

    $cmb_obj = new_cmb2_box( array(
        'id'            => 'submission_data',
        'title'         => __( 'Submission Status', 'badgeos' ),
        'object_types'  => array( 'submission' ), // Post type
        'context'    => 'side',
        'priority'   => 'default',
        'show_names' => true, // Show field names on the left
    ) );
    $cmb_obj->add_field(array(
        'name' => __( 'Current Status', 'badgeos' ),
        'desc' => ucfirst( get_post_meta( $post_id, $prefix . 'submission_status', true ) ),
        'id'   => $prefix . 'submission_current',
        'type' => 'text_only',
    ));
    $cmb_obj->add_field(array(
        'name' => apply_filters( 'badgeos_submission_cpt_status_update', 'Change Status' ),
        'desc' => badgeos_render_feedback_buttons( $post_id ),
        'id'   => $prefix . 'submission_update',
        'type' => 'text_only',
    ));
    $cmb_obj->add_field(array(
        'name' => __( 'Achievement ID to Award', 'badgeos' ),
        'desc' => '<a href="' . esc_url( admin_url('post.php?post=' . get_post_meta( $post_id, '_badgeos_submission_achievement_id', true ) . '&action=edit') ) . '">' . __( 'View Achievement', 'badgeos' ) . '</a>',
        'id'   => $prefix . 'submission_achievement_id',
        'type'	 => 'text_small',
        'attributes' => array(
            'readonly' => 'readonly'
        )
    ));
}
add_action( 'cmb2_admin_init', 'badgeos_subnom_submission_metaboxes' );

function badgeos_submission_cpt_new_status_update( $new_status ) {
    // update status if submission is approved/deny
    if ( ! empty( $_GET['post'] ) && isset( $_GET['post'] ) ) {
        $post_type = get_post_type( $_GET['post'] );
        $submission_status = get_post_meta( $_GET['post'], '_badgeos_submission_status', true );
        if ( $submission_status !='pending' && ( $post_type == 'submission' || $post_type == 'nomination' ) ) {
            $status = 'Reviewed'; 
            return $status;
        }
    }
    return $new_status;
}
add_filter('badgeos_submission_cpt_status_update', 'badgeos_submission_cpt_new_status_update', 10, 1);
add_filter('badgeos_nomination_cpt_status_update', 'badgeos_submission_cpt_new_status_update', 10, 1);

/**
 * Register custom meta box for BadgeOS Nominations.
 *
 * @since  1.0.0
 * @param  none
 * @return none
 */
function badgeos_subnom_nominations_metaboxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_badgeos_';

    // Grab our achievement types as an array
    $achievement_types = badgeos_get_achievement_types_slugs();

    // Setup our $post_id, if available
    $post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

    // New Achievement Types

    $cmb_obj = new_cmb2_box( array(
        'id'            => 'nomination_data',
        'title'         => __( 'Nomination Data', 'badgeos' ),
        'object_types'  => array( 'nomination' ), // Post type
        'context'    => 'side',
        'priority'   => 'default',
        'show_names' => true, // Show field names on the left
    ) );
    $cmb_obj->add_field(array(
        'name' => __( 'Current Status', 'badgeos' ),
        'desc' => ucfirst( get_post_meta( $post_id, $prefix . 'nomination_status', true ) ),
        'id'   => $prefix . 'nomination_current',
        'type' => 'text_only',
    ));
    $cmb_obj->add_field(array(
        'name' => apply_filters('badgeos_nomination_cpt_status_update', 'Change Status' ),
        'desc' => badgeos_render_feedback_buttons( $post_id ),
        'id'   => $prefix . 'nomination_update',
        'type' => 'text_only',
    ));

    $user_data = get_userdata( absint( get_post_meta( $post_id, '_badgeos_nomination_user_id', true ) ) );
    $display_name = '';
    if( $user_data ) {
        $display_name = $user_data->display_name;
    }

    $cmb_obj->add_field(array(
        'name' => __( 'Nominee', 'badgeos' ),
        'id'   => $prefix . 'nomination_user_id',
        'desc' => ( $post_id && get_post_type( $post_id ) == 'nomination' ) ? $display_name : '',
        'type' => 'text_medium',
        'attributes' => array(
            'readonly' => 'readonly'
        )
    ));

    $user_data2 = get_userdata( absint( get_post_meta( $post_id, '_badgeos_nominating_user_id', true ) ) );
    $display_name2 = '';
    if( $user_data2 ) {
        $display_name2 = $user_data2->display_name;
    }
    $cmb_obj->add_field(array(
        'name' => __( 'Nominated By', 'badgeos' ),
        'id'   => $prefix . 'nominating_user_id',
        'desc' => ( $post_id && get_post_type( $post_id ) == 'nomination' ) ? $display_name2 : '',
        'type' => 'text_medium',
        'attributes' => array(
            'readonly' => 'readonly'
        )
    ));
    $cmb_obj->add_field(array(
        'name' => __( 'Achievement ID to Award', 'badgeos' ),
        'desc' => '<a href="' . esc_url( admin_url('post.php?post=' . get_post_meta( $post_id, '_badgeos_nomination_achievement_id', true ) . '&action=edit') ) . '">' . __( 'View Achievement', 'badgeos' ) . '</a>',
        'id'   => $prefix . 'nomination_achievement_id',
        'type'	 => 'text_small',
        'attributes' => array(
            'readonly' => 'readonly'
        )
    ));
}
add_action( 'cmb2_admin_init', 'badgeos_subnom_nominations_metaboxes' );

/**
 * Register the Submission attachments meta box
 *
 * @since  1.1.0
 * @return void
 */
function badgeos_subnom_submission_attachments_meta_box() {

	//register the submission attachments meta box
	add_meta_box( 'badgeos_subnom_submission_attachments_id', __( 'Submission Attachments', 'badgeos' ), 'badgeos_subnom_submission_attachments', 'submission' );

}
add_action( 'add_meta_boxes', 'badgeos_subnom_submission_attachments_meta_box' );

/**
 * Display all Submission attachments in a meta box
 *
 * @since  1.1.0
 * @param  object $post The post content
 * @return object 		The modified post content
 */
function badgeos_subnom_submission_attachments( $post = null) {

	//return all submission attachments
	if ( $submission_attachments = badgeos_get_submission_attachments( absint( $post->ID ) ) ) {
		echo $submission_attachments;
	}else{
		_e( 'No attachments on this submission', 'badgeos' );
	}

}

/**
 * Register custom meta boxes for Badgeos achievements Submission.
 *
 * @since  1.0.0
 * @param  none
 * @return none
 */
function badgeos_subnom_achievment_metaboxes( ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_badgeos_';

    $badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();

    // Grab our achievement types as an array
    $achievement_types_temp = badgeos_get_achievement_types_slugs();
    $achievement_types = array();
    if( $achievement_types_temp ) {
        foreach( $achievement_types_temp as $key=>$ach ) {
            if( ! empty( $ach ) && $ach != trim( $badgeos_settings['achievement_step_post_type'] ) ) {
                if (!empty($ach) && $ach != 'step') {
                    $achievement_types[] = $ach;
                }
            }
        }
    }

    // Setup our $post_id, if available
    $post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

    // New Achievement Types
    $cmb_obj = new_cmb2_box( array(
        'id'            => 'achievement_subnome_data',
        'title'         => __( 'Submission Data', 'badgeos' ),
        'object_types'  => $achievement_types, // Post type
        'context'    => 'advanced',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
    ) );
    $cmb_obj->add_field(array(
        'name' => __( 'Allow Attachment for Submission', 'badgeos' ),
        'desc' => ' '.__( 'Yes, allow to display submission attachment.', 'badgeos' ),
        'id'   => $prefix . 'all_attachment_submission',
        'type' => 'checkbox',
    ));
    $cmb_obj->add_field(array(
        'name' => __( 'Allow Attachment for Submission Comment', 'badgeos' ),
        'desc' => ' '.__( 'Yes, allow to display submission comment attachment.', 'badgeos' ),
        'id'   => $prefix . 'all_attachment_submission_comment',
        'type' => 'checkbox',
    ));

}
add_action( 'cmb2_admin_init', 'badgeos_subnom_achievment_metaboxes' );