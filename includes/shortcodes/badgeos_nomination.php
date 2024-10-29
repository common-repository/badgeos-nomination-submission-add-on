<?php

/**
 * Register the [badgeos_nomination] shortcode.
 *
 * @since 1.4.0
 */
function badgeos_register_nomination_shortcode() {

    $achievements = badgeos_get_achievements_id_title_pair();
	badgeos_register_shortcode( array(
		'name'            => __( 'Nomination Form', 'badgeos' ),
		'description'     => __( 'Render a nomination form for a specific achievement.', 'badgeos' ),
		'slug'            => 'badgeos_nomination',
		'output_callback' => 'badgeos_nomination_form',
		'attributes'      => array(
			'achievement_id' => array(
				'name'        => __( 'Achievement ID', 'badgeos' ),
				'description' => __( 'Achievement ID to award.', 'badgeos' ),
                'type'        => 'select',
                'values'      => $achievements,
                'default'     => '',
            ),
        ),
	) );
}
add_action( 'init', 'badgeos_register_nomination_shortcode' );

/**
 * Nomination Form Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function badgeos_nomination_form( $atts = array() ) {

	// Parse our attributes
	$atts = shortcode_atts( array(
		'achievement_id' => get_the_ID(),
	), $atts, 'badgeos_nomination' );

	$output = '';

	// Verify user is logged in to view any submission data
	if ( is_user_logged_in() ) {

        $badgeos_settings = get_option('badgeos_settings');
        $submission_rte_settings = $badgeos_settings['submission_rte'];

        if ( $submission_rte_settings == 'enabled' ) {
            wp_enqueue_script(
                'ck_editor_cdn',
                ('https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js'), array( 'jquery' ), 'v3', true
            );

            wp_enqueue_script(
                'custom_script',
                BOSNS_ASSETS_URL.'js/ckeditor.js',
                array( 'jquery' ),	BadgeOS::$version,true
            );
        }
		
		// If we've just saved nomination data
		if ( badgeos_save_nomination_data() ) {
			$output .= sprintf( '<p class="bos-submission-nomination-message bos-nomination-message">%s</p>', __( 'Nomination send successfully.', 'badgeos' ) ); 
		}

		// Return the user's nominations
		if ( badgeos_check_if_user_has_nomination( get_current_user_id(), $atts['achievement_id'] ) ) {
			$output .= badgeos_get_user_nominations( '', $atts['achievement_id'] );
		}

		// Include the nomination form
		$output .= badgeos_get_nomination_form( array( 'user_id' => get_current_user_id(), 'achievement_id' => $atts['achievement_id'] ) );

	} else {

		$output = sprintf( '<p><em>%s</em></p>', __( 'You must be logged in to post a nomination.', 'badgeos' ) );

	}

	return $output;

}
