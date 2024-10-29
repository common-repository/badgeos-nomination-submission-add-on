<?php
/**
 * Uninstall file, which would delete all user metadata and configuration settings
 *
 * @since 1.0
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

$badgeos_settings = get_option( 'badgeos_settings' );
$remove_data_on_uninstall = ( isset( $badgeos_settings['remove_data_on_uninstall'] ) ) ? $badgeos_settings['remove_data_on_uninstall'] : '';

/**
 * Return - if delete option is not enabled.
 */
if( "on" != $remove_data_on_uninstall ) {
    return;
}

global $wpdb;

/**
 * Delete submission post type data
 */
$submissions = $wpdb->get_results( "SELECT `ID` FROM $wpdb->posts WHERE post_type = 'submission';" );
if( is_array( $submissions ) && !empty( $submissions ) && !is_null( $submissions ) ) {
    foreach( $submissions as $submission ) {
        $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID = '$submission->ID';" );
        $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id = '$submission->ID';" );
    }
}

/**
 * Delete nomination post type data
 */
$nominations = $wpdb->get_results( "SELECT `ID` FROM $wpdb->posts WHERE post_type = 'nomination';" );
if( is_array( $nominations ) && !empty( $nominations ) && !is_null( $nominations ) ) {
    foreach( $nominations as $nomination ) {
        $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID = '$nomination->ID';" );
        $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id = '$nomination->ID';" );
    }
}