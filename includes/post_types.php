<?php

/**
 * Register all of our Submission and Nomination CPTs
 *
 * @since  1.0.0
 * @return void
 */
function badgeos_subnom_register_post_types() {
    global $badgeos;
    
    // Register Submissions CPT
	register_post_type( 'submission', array(
		'labels'             => array(
			'name'               => __( 'Submission Reviews', 'badgeos' ),
            'singular_name'      => __( 'Submission Review', 'badgeos' ),
            'add_new'            => __( 'Add New', 'badgeos' ),
            'add_new_item'       => __( 'Add New Submission Review', 'badgeos' ),
            'edit_item'          => __( 'Edit Submission Review', 'badgeos' ),
            'new_item'           => __( 'New Submission Review', 'badgeos' ),
            'all_items'          => __( 'Submission Reviews', 'badgeos' ),
            'view_item'          => __( 'View SubmissionReview', 'badgeos' ),
            'search_items'       => __( 'Search Submission Reviews', 'badgeos' ),
            'not_found'          => __( 'No submission Reviews found', 'badgeos' ),
            'not_found_in_trash' => __( 'No submission Reviews found in Trash', 'badgeos' ),
			'parent_item_colon'  => '',
            'menu_name'          => __( 'Submission Reviews', 'badgeos' )
		),
		'public'             => apply_filters( 'badgeos_public_submissions', false ),
		'publicly_queryable' => apply_filters( 'badgeos_public_submissions', false ),
		'show_ui'            => badgeos_user_can_manage_submissions(),
		'show_in_menu'       => 'badgeos_badgeos',
		'show_in_nav_menus'  => apply_filters( 'badgeos_public_submissions', false ),
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => apply_filters( 'badgeos_public_submissions', false ),
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'comments' )
	) );


	// Register Nominations CPT
	register_post_type( 'nomination', array(
		'labels'             => array(
            'name'               => __( 'Nomination Reviews', 'badgeos' ),
            'singular_name'      => __( 'Nomination Review', 'badgeos' ),
            'add_new'            => __( 'Add New', 'badgeos' ),
            'add_new_item'       => __( 'Add New Nomination Review', 'badgeos' ),
            'edit_item'          => __( 'Edit Nomination Review', 'badgeos' ),
            'new_item'           => __( 'New Nomination Review', 'badgeos' ),
            'all_items'          => __( 'Nomination Reviews', 'badgeos' ),
            'view_item'          => __( 'View Nomination Reviews', 'badgeos' ),
            'search_items'       => __( 'Search Nomination Reviews', 'badgeos' ),
            'not_found'          => __( 'No nomination Reviews found', 'badgeos' ),
            'not_found_in_trash' => __( 'No nomination Reviews found in Trash', 'badgeos' ),
			'parent_item_colon'  => '',
            'menu_name'          => __( 'Nomination Reviews', 'badgeos' )
		),
		'public'             => apply_filters( 'badgeos_public_nominations', false ),
		'publicly_queryable' => apply_filters( 'badgeos_public_nominations', false ),
		'show_ui'            => badgeos_user_can_manage_submissions(),
		'show_in_menu'       => 'badgeos_badgeos',
		'show_in_nav_menus'  => apply_filters( 'badgeos_public_nominations', false ),
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => apply_filters( 'badgeos_public_nominations', false ),
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'comments' ),
	) );

	// Hide link on cpt listing page
    if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'submission' || $_GET['post_type'] == 'nomination' ) ){
        	echo '<style type="text/css">
        			.wrap .wp-heading-inline+.page-title-action { display:none; }
    			</style>';
    }

    // Hide link on cpt edit page
    if ( isset( $_GET['post'] ) ) {
    	$post_type = get_post_type( $_GET['post'] );
    	if ( $post_type == 'submission' || $post_type == 'nomination' ) {
        	echo '<style type="text/css">
        			.wrap .wp-heading-inline+.page-title-action { display:none; }
    			</style>';
		}
    }


}
add_action( 'init', 'badgeos_subnom_register_post_types' );