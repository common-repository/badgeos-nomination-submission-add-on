<?php
	
/**
 * AJAX Helper for approving/denying feedback
 *
 * @since 1.1.0
 * @return void
 */
function badgeos_ajax_update_feedback() {

	// Verify our nonce
	check_ajax_referer( 'review_feedback', 'nonce' );
	global $wpdb;
	$achievement_id = sanitize_text_field( $_REQUEST['achievement_id'] );
	$user_id 		= sanitize_text_field( $_REQUEST['user_id'] );
	$feedback_type 	= sanitize_text_field( $_REQUEST['feedback_type'] );
	$status 		= sanitize_text_field( $_REQUEST['status'] );
	$feedback_id 	= sanitize_text_field( $_REQUEST['feedback_id'] );

	// Status workflow
	$status_args = array(
		'achievement_id' 	=> $achievement_id,
        'user_id' 			=> $user_id,
		'submission_type' 	=> $feedback_type
	);

	// Setup status
	$status = ( in_array( $status, array( 'approve', 'approved' ) ) ) ? 'approved' : 'denied';
	badgeos_set_submission_status( $feedback_id, $status, $status_args );

	// Send back our successful response
	wp_send_json_success( array(
		'message' => '<p class="badgeos-feedback-response success">' . __( 'Status Updated!', 'badgeos' ) . '</p>',
		'status' => ucfirst( $status )
	) );

}
add_action( 'wp_ajax_update-feedback', 'badgeos_ajax_update_feedback', 1 );
add_action( 'wp_ajax_nopriv_update-feedback', 'badgeos_ajax_update_feedback', 1 );

/**
 * AJAX Helper for returning feedback posts
 *
 * @since 1.1.0
 * @return void
 */
function badgeos_ajax_get_feedback() {

	$feedback = badgeos_get_feedback( array(
		'post_type'        => sanitize_text_field( $_REQUEST['type'] ),
		'posts_per_page'   => sanitize_text_field( $_REQUEST['limit'] ) ,
		'status'           => sanitize_text_field( $_REQUEST['status'] ),
		'show_attachments' => sanitize_text_field( $_REQUEST['show_attachments'] ),
		'show_comments'    => sanitize_text_field( $_REQUEST['show_comments'] ),
		's'                => sanitize_text_field( $_REQUEST['search'] ),
	) );

	wp_send_json_success( array(
		'feedback' => $feedback
	) );
}
add_action( 'wp_ajax_get-feedback', 'badgeos_ajax_get_feedback', 1 );
add_action( 'wp_ajax_nopriv_get-feedback', 'badgeos_ajax_get_feedback', 1 );

/**
 * Added approve/disapprove in bulk actions
 *
 * @since 1.0.0
 *
 * @param int $user_id        The ID of the user earning the achievement
 * @param int $achievement_id The ID of the achievement being earned
 */
function badgeos_register_approve_submission_bulk_actions( $bulk_actions ) {

    $bulk_actions['approve_submission'] = __( 'Approve', 'ldmqie' );
    $bulk_actions['disapprove_submission'] = __( 'Deny', 'ldmqie' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-submission', 'badgeos_register_approve_submission_bulk_actions' );

/**
 * Added approve/disapprove in bulk actions
 *
 * @since 1.0.0
 *
 * @param int $user_id        The ID of the user earning the achievement
 * @param int $achievement_id The ID of the achievement being earned
 */
function badgeos_register_approve_nomination_bulk_actions( $bulk_actions ) {

    $bulk_actions['approve_nomination'] = __( 'Approve', 'ldmqie' );
    $bulk_actions['disapprove_nomination'] = __( 'Deny', 'ldmqie' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-nomination', 'badgeos_register_approve_nomination_bulk_actions' );

/**
 * Displays the submission form on achievement type single pages if the meta option is enabled
 *
 * @since  1.0.0
 * @param  string $content The page content before meta box insertion
 * @return string          The page content after meta box insertion
 */
function badgeos_achievement_submissions( $content = '' ) {
	global $post;

	if ( is_single() ) {

		// get achievement object for the current post type
		$post_type = get_post_type( $post );
        $badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
        $achievement = get_page_by_title( $post_type, 'OBJECT', $badgeos_settings['achievement_main_post_type'] );
        if ( !$achievement ) {
			global $wp_post_types;

			$labels = array( 'name', 'singular_name' );
			// check for other variations
			foreach ( $labels as $label ) {
                $achievement = get_page_by_title( $wp_post_types[$post_type]->labels->$label, 'OBJECT', $badgeos_settings['achievement_main_post_type'] );
				if ( $achievement )
					break;
			}
		}

		if ( !$achievement )
			return $content;


		// check if submission or nomination is set
		$earned_by = get_post_meta( $post->ID, '_badgeos_earned_by', true );

		if ( ( $earned_by == 'submission' || $earned_by == 'submission_auto' ) && is_user_logged_in() ) {

			$submission = badgeos_submission_form();

			//return the content with the submission shortcode data
			return $content . $submission;

		} elseif ( $earned_by == 'nomination' && is_user_logged_in() ) {

			$nomination = badgeos_nomination_form();

			//return the content with the nomination shortcode data
			return $content . $nomination;

		}

	}

	return $content;

}
add_filter( 'the_content', 'badgeos_achievement_submissions' );

/**
 * Added approve/disapprove in bulk actions
 *
 * @since 1.0.0
 *
 * @param int $earned_by 
 * 
 * @return $earned_by
 */
function badgeos_subnom_achievement_earned_by( $earned_by ) {
    $earned_by['submission']        = __( 'Submission (Reviewed)', 'badgeos' );
    $earned_by['submission_auto']   = __( 'Submission (Auto-accepted)', 'badgeos' );
    $earned_by['nomination']        = __( 'Nomination', 'badgeos' );
    return $earned_by;
}
add_filter( 'badgeos_achievement_earned_by', 'badgeos_subnom_achievement_earned_by' );


/**
 * Handle approve/disapprove bulk actions
 *
 * @since 1.0.0
 *
 * @param int $user_id        The ID of the user earning the achievement
 * @param int $achievement_id The ID of the achievement being earned
 */
function badgeos_register_approve_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {

    if ( $doaction == 'approve_nomination' || $doaction == 'approve_submission' ) {
        foreach( $post_ids as $post_id ) {
            if( $doaction == 'approve_submission' ) {
                $achievement_id = get_post_meta($post_id, '_badgeos_submission_achievement_id', true);
                $status = get_post_meta($post_id, '_badgeos_submission_status', true);
                $post_data = get_post($post_id);
                $status_args = array(
                    'achievement_id' => $achievement_id,
                    'user_id' => $post_data->post_author,
                    'submission_type' => 'submission'
                );

                badgeos_set_submission_status( $post_id, 'approved', $status_args );
            } else if( $doaction == 'approve_nomination' ) {
                $achievement_id = get_post_meta($post_id, '_badgeos_nomination_achievement_id', true);
                $status = get_post_meta($post_id, '_badgeos_nomination_status', true);
                $nomination_user_id = get_post_meta($post_id, '_badgeos_nomination_user_id', true);
                $nominating_user_id = get_post_meta($post_id, '_badgeos_nominating_user_id', true);
                $status_args = array(
                    'achievement_id' => $achievement_id,
                    'user_id' => $nomination_user_id,
                    'from_user_id' => $nominating_user_id,
                    'submission_type' => 'nomination'
                );

                badgeos_set_submission_status( $post_id, 'approved', $status_args );
            }
        }
    } else if ( $doaction == 'disapprove_nomination' || $doaction == 'disapprove_submission' ) {
        foreach( $post_ids as $post_id ) {
            if( $doaction == 'disapprove_submission' ) {
                $post_data = get_post( $post_id );
                $achievement_id = get_post_meta($post_id, '_badgeos_submission_achievement_id', true);
                $status = get_post_meta($post_id, '_badgeos_submission_status', true);
                $status_args = array(
                    'achievement_id' => $achievement_id,
                    'user_id' => $post_data->post_author,
                    'submission_type' => 'submission'
                );

                badgeos_set_submission_status( $post_id, 'denied', $status_args );
            } else if( $doaction == 'disapprove_nomination' ) {
                $achievement_id = get_post_meta($post_id, '_badgeos_nomination_achievement_id', true);
                $status = get_post_meta($post_id, '_badgeos_nomination_status', true);
                $nomination_user_id = get_post_meta($post_id, '_badgeos_nomination_user_id', true);
                $nominating_user_id = get_post_meta($post_id, '_badgeos_nominating_user_id', true);
                $status_args = array(
                    'achievement_id' => $achievement_id,
                    'user_id' => $nomination_user_id,
                    'from_user_id' => $nominating_user_id,
                    'submission_type' => 'nomination'
                );

                badgeos_set_submission_status( $post_id, 'denied', $status_args );
            }
        }
    }

    return $redirect_to;
}
add_filter( 'handle_bulk_actions-edit-submission', 'badgeos_register_approve_bulk_actions_handler', 10, 3 ); 
add_filter( 'handle_bulk_actions-edit-nomination', 'badgeos_register_approve_bulk_actions_handler', 10, 3 );	


/**
 * Render a filterable list of feedback
 *
 * @since  1.1.0
 * @param  array  $atts Shortcode attributes
 * @return string       Contatenated markup
 */
function badgeos_render_feedback( $atts = array() ) { 

	$atts = wp_parse_args( $atts, array(
		'type'             => 'submission',
		'limit'            => '10',
		'status'           => 'all',
		'show_filter'      => true,
		'show_search'      => true,
		'show_attachments' => true,
		'show_comments'    => true
	) );

	$feedback = badgeos_get_feedback( array(
		'post_type'        => $atts['type'],
		'posts_per_page'   => $atts['limit'],
		'show_attachments' => $atts['show_attachments'],
		'show_comments'    => $atts['show_comments'],
		'status'           => $atts['status']
	) );

	$output = '';
	$output .= badgeos_render_feedback_search( $atts );
	$output .= badgeos_render_feedback_filters( $atts );
	$output .= '<div class="badgeos-spinner" style="display:none;"></div>';
	$output .= '<div class="badgeos-feedback-container">';
	$output .= $feedback;
	$output .= '</div>';

	return apply_filters( 'badgeos_render_feedback', $output, $atts );

}

/**
 * Render feedback search input.
 *
 * @since  1.4.0
 *
 * @param  array  $atts Shortcode attributes.
 * @return string       HTML Markup.
 */
function badgeos_render_feedback_search( $atts = array() ) {
	$output = '';
	if( isset( $_POST['feedback_search'] ) ) {
		$feedback_search 	= sanitize_text_field( $_POST['feedback_search'] );
	} else {
		$feedback_search 	= '';
	}
	
	$show_search 		= sanitize_text_field( $atts['show_search'] );
	$search 			= isset( $feedback_search ) ? $feedback_search : '';

	if ( 'false' !== $show_search ) {
		$output .= '<div class="badgeos-feedback-search">';
			$output .= '<form class="badgeos-feedback-search-form" action="" method="POST">';
			$output .= '<input type="text" class="badgeos-feedback-search-input" name="feedback_search" value="'. $search .'">';
			$output .= '<input type="submit" class="badgeos-feedback-search-button" name="feedback_search_button" value="' . __( 'Search', 'badgeos' ) . '">';
			$output .= '</form>';
		$output .= '</div><!-- .badgeos-feedback-search -->';
	}

	return apply_filters( 'badgeos_render_feedback_search', $output, $atts, $search );
}

/**
 * Render feedback filter inputs.
 *
 * @since  1.4.0
 *
 * @param  array  $atts Shortcode atts.
 * @return string       HTML Markup.
 */
function badgeos_render_feedback_filters( $atts = array() ) {
	$output = '';

	if ( 'false' !== $atts['show_filter'] ) {

		$output .= '<div class="badgeos-feedback-filter">';
			$output .= '<label for="status_filter">' . __( 'Status:', 'badgeos' ) . '</label>';
			$output .= ' <select name="status_filter" id="status_filter">';
				$output .= '<option' . selected( $atts['status'], 'all', false ) . ' value="all">' . __( 'All', 'badgeos' ) . '</option>';
				$output .= '<option' . selected( $atts['status'], 'pending', false ) . ' value="pending">' . __( 'Pending', 'badgeos' ) . '</option>';
				$output .= '<option' . selected( $atts['status'], 'approved', false ) . ' value="approved">' . __( 'Approved', 'badgeos' ) . '</option>';
				if ( 'submission' == $atts['type'] )
					$output .= '<option' . selected( $atts['status'], 'auto-approved', false ) . ' value="auto-approved">' . __( 'Auto-approved', 'badgeos' ) . '</option>';
				$output .= '<option' . selected( $atts['status'], 'denied', false ) . ' value="denied">' . __( 'Denied', 'badgeos' ) . '</option>';
			$output .= '</select>';
		$output .= '</div>';

	} else {
		$output .= '<input type="hidden" name="status_filter" id="status_filter" value="' . esc_attr( $atts['status'] ) . '">';
	}

	return apply_filters( 'badgeos_render_feedback_filters', $output, $atts );
}

/**
 * Render a given nomination
 *
 * @since  1.1.0
 * @param  object $nomination A nomination post object
 * @param  array  $args       Additional args for content options
 * @return string             Concatenated output
 */
function badgeos_render_nomination( $nomination = null, $args = array() ) {
    global $post;

    // If we weren't given a nomination, use the current post
	if ( empty( $nomination ) ) {
		$nomination = $post;
	}

	// Grab the connected achievement
	$achievement_id = get_post_meta( $nomination->ID, '_badgeos_nomination_achievement_id', true );
	$nominating_user_id = get_post_meta( $nomination->ID, '_badgeos_nomination_user_id', true );
	// Concatenate our output
	$output = '<div class="badgeos-nomination badgeos-feedback badgeos-feedback-' . $nomination->ID . '">';

		// Title
		$output .= '<h4>';
		$output .= sprintf( __( '%1$s nominated for %2$s', 'badgeos' ),
			get_userdata( $nominating_user_id )->display_name,
			'<a href="' . get_permalink( $achievement_id ) .'">' . get_the_title( $achievement_id ) . '</a>'
		);
		$output .= '</h4>';

		// Nomination content
		$output .= '<div class="badgeos-submission-content">';
		$output .= html_entity_decode(  $nomination->post_content , ENT_QUOTES, 'UTF-8' );
		$output .= '</div>';

		// Approval Status
		$output .= '<p class="badgeos-comment-date-by">';
			$output .= '<span class="badgeos-status-label">' . __( 'Status:', 'badgeos' ) . '</span> ';
			$output .= get_post_meta( $nomination->ID, '_badgeos_nomination_status', true );
		$output .= '</p>';
		
		$nominating_user_id = get_post_meta( $nomination->ID, '_badgeos_nominating_user_id', true );
		$nominating_user = '';
		if ( is_numeric( $nominating_user_id ) ) {
			$user_info = get_userdata( absint( $nominating_user_id ) );
			$nominating_user = $user_info->display_name;
		}

		$output .= '<p class="badgeos-nominated-by">';
		$output .= '<span class="badgeos-status-label">' . __( 'Nominated By:', 'badgeos' ) . '</span> ';
		$output .= $nominating_user;
		$output .= '</p>';
		// Approve/Deny Buttons for admins only
		if ( badgeos_user_can_manage_submissions() ) {
			$output .= badgeos_render_feedback_buttons( $nomination->ID );
		}

	$output .= '</div><!-- .badgeos-original-submission -->';

	// Return our filterable output
	return apply_filters( 'badgeos_render_nomination', $output, $nomination );
}

/**
 * Render a given submission
 *
 * @since  1.1.0
 * @param  object $submission A submission post object
 * @param  array  $args       Additional args for content options
 * @return string             Concatenated output
 */
function badgeos_render_submission( $submission = null, $args = array() ) {
	global $post;

    // If we weren't given a submission, use the current post
	if ( empty( $submission ) ) {
		$submission = $post;
	}

	// Get the connected achievement ID
	$achievement_id = get_post_meta( $submission->ID, '_badgeos_submission_achievement_id', true );
	$status = get_post_meta( $submission->ID, '_badgeos_submission_status', true );
	$submission_author = get_userdata( $submission->post_author );
	$display_name = is_object( $submission_author ) ? $submission_author->display_name : '';

	// Concatenate our output
	$output = '<div class="badgeos-submission badgeos-feedback badgeos-feedback-' . $submission->ID . '">';

		// Submission Title
		$output .= '<h2>' . sprintf( __( 'Submission: "%1$s"', 'badgeos' ), get_the_title( $achievement_id )) . '</h2>';
		// Submission Meta
		$output .= '<p class="badgeos-submission-meta">';
			$output .= sprintf( '<strong class="label">%1$s</strong> <span class="badgeos-feedback-author">%2$s</span><br/>', __( 'Author:', 'badgeos' ), $display_name );
			$output .= sprintf( '<strong class="label">%1$s</strong> <span class="badgeos-feedback-date">%2$s</span><br/>', __( 'Date:', 'badgeos' ), get_the_time( 'F j, Y h:i a', $submission ) );
			if ( $achievement_id != $submission->ID ) {
				$output .= sprintf( '<strong class="label">%1$s</strong> <span class="badgeos-feedback-link">%2$s</span><br/>', __( 'Achievement:', 'badgeos' ), '<a href="' . get_permalink( $achievement_id ) .'">' . get_the_title( $achievement_id ) . '</a>' );
			}
			$output .= sprintf( '<strong class="label">%1$s</strong> <span class="badgeos-feedback-status">%2$s</span><br/>', __( 'Status:', 'badgeos' ), ucfirst( $status ) );
		$output .= '</p>';

		// Submission Content
		$output .= '<div class="badgeos-submission-content">';
		$output .= html_entity_decode(  $submission->post_content , ENT_QUOTES, 'UTF-8' );
		$output .= '</div>';

		// Include any attachments
		if ( isset( $args['show_attachments'] ) && 'false' !== $args['show_attachments'] ) {
			$output .= badgeos_get_submission_attachments( $submission->ID );
		}

		// Approve/Deny Buttons for admins only
		if ( badgeos_user_can_manage_submissions() ) {
			$output .= badgeos_render_feedback_buttons( $submission->ID );
		}

		// Include comments and comment form
		if ( isset( $args['show_comments'] ) && 'false' !== $args['show_comments'] ) {
			$output .= badgeos_get_comments_for_submission( $submission->ID );
			$output .= badgeos_get_comment_form( $submission->ID );
		}

	$output .= '</div><!-- .badgeos-submission -->';


	if( badgeos_get_hidden_achievement_by_id( $achievement_id ) ){
		$output = '';
	}

	// Return our filterable output
	return apply_filters( 'badgeos_render_submission', $output, $submission );
}

/**
 * Renter a given submission attachment
 *
 * @since  1.1.0
 * @param  object $attachment The attachment post object
 * @return string             Concatenated markup
 */
function badgeos_render_submission_attachment( $attachment = null ) {
	// If we weren't given an attachment, use the current post
	if ( empty( $attachment ) ) {
		global $post;
		$attachment = $post;
	}

	$userdata = get_userdata( $attachment->post_author );
	$display_name = is_object( $userdata ) ? $userdata->display_name : '';

	// Concatenate the markup
	$output = '<li class="badgeos-attachment">';
	$output .= sprintf( __( '%1$s - uploaded %2$s by %3$s', 'badgeos' ),
		wp_get_attachment_link( $attachment->ID, 'full', false, null, $attachment->post_title ),
		get_the_time( 'F j, Y g:i a', $attachment ),
		$display_name
	);
	$output .= '</li><!-- .badgeos-attachment -->';

	// Return our filterable output
	return apply_filters( 'badgeos_render_submission_attachment', $output, $attachment );
}

/**
 * Render a given submission comment
 *
 * @since  1.1.0
 * @param  object $comment  The comment object
 * @param  string $odd_even Custom class to use for alternating comments (e.g. "odd" or "even")
 * @return string           The concatenated markup
 */
function badgeos_render_submission_comment( $comment = null, $odd_even = 'odd' ) {

	// Concatenate our output
	$output = '<li class="badgeos-submission-comment ' . $odd_even . '">';

		// Author and Meta info
		$output .= '<p class="badgeos-comment-date-by">';
		$output .= sprintf( __( '%1$s on %2$s', 'badgeos' ),
			'<cite class="badgeos-comment-author">' . get_userdata( $comment->user_id )->display_name . '</cite>',
			'<span class="badgeos-comment-date">' . get_comment_date( 'F j, Y g:i a', $comment->comment_ID ) . '<span>'
		);
		$output .= '</p>';

		// Content
		$output .= '<div class="badgeos-comment-text">';
		$output .= html_entity_decode( wpautop( $comment->comment_content ), ENT_QUOTES, 'UTF-8' );
		$output .= '</div>';

	$output .= '</li><!-- badgeos-submission-comment -->';

	// Return our filterable output
	return apply_filters( 'badgeos_render_submission_comment', $output, $comment, $odd_even );
}

/**
 * Render the approve/deny buttons for a given piece of feedback
 *
 * @since  1.0.0
 * @param  integer $feedback_id The feedback's post ID
 * @return string               The concatinated markup
 */
function badgeos_render_feedback_buttons( $feedback_id = 0 ) {
	global $post, $user_ID;

	// Use the current post ID if no ID provided
	$feedback_id    = ! empty( $feedback_id ) ? ( is_array( $feedback_id ) ? $feedback_id[0] : $feedback_id ) : ( isset( $post->ID ) ? $post->ID : 0 );
	$feedback       = get_post( $feedback_id );
	$feedback_type  = get_post_type( $feedback_id );
	$achievement_id = get_post_meta( $feedback_id, "_badgeos_{$feedback_type}_achievement_id", true );
	$user_id = get_post_meta($feedback_id, '_badgeos_nomination_user_id', true);
	if( $feedback && intval( $user_id ) < 1 ) {
		$user_id        = isset( $feedback->post_author ) ? $feedback->post_author : 0;
	}

	$submission_status = get_post_meta( $feedback_id, '_badgeos_submission_status', true );
	$nomination_status = get_post_meta( $feedback_id, '_badgeos_nomination_status', true );
	$output = '';
	if ( $submission_status == 'pending' || $nomination_status == 'pending'  ) {
		// Concatenate our output
		$output .= '<p class="badgeos-feedback-buttons">';
			$output .= '<a href="#" class="button approve" data-feedback-id="' . absint( $feedback_id ) . '" data-action="approve">' . __( 'Approve', 'badgeos' ) . '</a> ';
			$output .= '<a href="#" class="button deny" data-feedback-id="' . absint( $feedback_id ) . '" data-action="denied">' . __( 'Deny', 'badgeos' ) . '</a>';
			$output .= wp_nonce_field( 'review_feedback', 'badgeos_feedback_review', true, false );
			$output .= '<input type="hidden" name="user_id" value="' . absint( $user_id ) . '">';
			$output .= '<input type="hidden" name="feedback_type" value="' . esc_attr( $feedback_type ) . '">';
			$output .= '<input type="hidden" name="achievement_id" value="' . absint( $achievement_id ) . '">';
		$output .= '</p>';
	}

	// Enqueue and localize our JS
	$atts['ajax_url'] = esc_url( admin_url( 'admin-ajax.php', 'relative' ) );
	$atts['user_id']  = $user_ID;
	wp_enqueue_script( 'badgeos-achievements' );
	wp_localize_script( 'badgeos-achievements', 'badgeos_feedback_buttons', $atts );

	// Return our filterable output
	return apply_filters( 'badgeos_render_feedback_buttons', $output, $feedback_id );
}

/**
 * Handle Submissions/Nominations shortcode output
 *
 * @since  1.4.0
 *
 * @param array $atts Array of shortcode attributes.
 * @param string $type Submission type.
 * @param string $shortcode Shortcode name.
 * @param array $defaults_override Array of overrides to set for default attributes.
 *
 * @return string
 */
function badgeos_shortcode_submissions_handler( $atts = array(), $shortcode = '' ) {

	// Setup defaults and allow override
	$defaults = array(
		'type'             => 'submission',
		'limit'            => '10',
		'status'           => 'all',
		'user_id'          => get_current_user_id(),
		'show_filter'      => true,
		'show_search'      => true,
		'show_attachments' => true,
		'show_comments'    => true,
		'wpms'             => false,
		'filters'          => array(
			'status' => '.badgeos-feedback-filter select',
			'search' => '.badgeos-feedback-search-input',
		),
	);

	// Parse shortcode attributes
	$atts = shortcode_atts( $defaults, $atts, $shortcode );

	// Get initial feedback HTML
	$feedback_html = badgeos_render_feedback( $atts );

	// AJAX url
	$atts[ 'ajax_url' ] = esc_url( admin_url( 'admin-ajax.php', 'relative' ) );
	$atts[ 'action' ]   = 'get-feedback';

	// Enqueue and localize our JS
	wp_enqueue_script( 'badgeos-achievements' );
	wp_localize_script( 'badgeos-achievements', 'badgeos_feedback', $atts );

	// Return initial feedback HTML
	return $feedback_html;

}

/**
 * Get capability required for Submission management.
 *
 * @since  1.4.0
 *
 * @return string User capability.
 */
function badgeos_get_submission_manager_capability() {
	$badgeos_settings = get_option( 'badgeos_settings' );
	return isset( $badgeos_settings[ 'submission_manager_role' ] ) ? $badgeos_settings[ 'submission_manager_role' ] : badgeos_get_manager_capability();
}

/**
 * Check if a user can manage submissions.
 *
 * @since  1.4.0
 *
 * @param  integer $user_id User ID.
 * @return bool             True if user can manaage submissions, otherwise false.
 */
function badgeos_user_can_manage_submissions( $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

    return ( user_can( $user_id, badgeos_get_submission_manager_capability() ) );
}