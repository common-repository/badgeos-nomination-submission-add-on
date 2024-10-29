<?php
/**
 * Plugin Name: Badgeos Nomination & Submission Add-On
 * Plugin URI:  https://badgeos.org/downloads/badgeos-nominations-submissions/
 * Description: This add-on adds the nominations and submission support in BadgeOS.
 * Version:     1.2.5
 * Author:      BadgeOS
 * Author URI:  https://badgeos.org/
 * Text Domain: bos-ns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, ['BOS_Nomination_Submission', 'activation' ] );
register_deactivation_hook( __FILE__, ['BOS_Nomination_Submission', 'deactivation']  );

/**
 * Class BOS_Nomination_Submission
 */
final class BOS_Nomination_Submission {
	const VERSION = '1.2.5';

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @return BOS_Nomination_Submission
	 */
	public static function instance() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof BOS_Nomination_Submission ) ) {
			self::$instance = new self();
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();

		}
		return self::$instance;
	}


	/**
	 * Activation function hook
	 *
	 * @return void
	 */
	public static function activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$general_values = get_option( 'bos_ns_options' );
        
        if( false == $general_values ) {

            $general_form_data = array();

            update_option('bos_ns_options', $general_form_data);
        }

        $badgeos_admin_tools = ( $exists = get_option( 'badgeos_admin_tools' ) ) ? $exists : array();

		// set submissions values
		$badgeos_admin_tools['email_disable_submission_approved_email'] = isset( $badgeos_admin_tools['email_disable_submission_approved_email'] ) ? $badgeos_admin_tools['email_disable_submission_approved_email'] : 'yes';
		$badgeos_admin_tools['email_submission_approved_subject'] = isset($badgeos_admin_tools['email_submission_approved_subject']) ? $badgeos_admin_tools['email_submission_approved_subject'] : 'Approved Submission: [achievement_name] from [user_name]';
		$badgeos_admin_tools['email_submission_approved_content'] = isset($badgeos_admin_tools['email_submission_approved_content']) ? $badgeos_admin_tools['email_submission_approved_content'] : 'A new submission has been received and approved:

			In response to: [achievement_name]
			Submitted by: [user_name]

			To view all submissions, including this one, visit: [submission_type_link]';

		$badgeos_admin_tools['email_disable_submission_denied_email'] = isset($badgeos_admin_tools['email_disable_submission_denied_email']) ? $badgeos_admin_tools['email_disable_submission_denied_email'] : 'yes';
		$badgeos_admin_tools['email_submission_denied_subject'] = isset($badgeos_admin_tools['email_submission_denied_subject']) ? $badgeos_admin_tools['email_submission_denied_subject'] : 'Submission Not Approved: [achievement_name]';
		$badgeos_admin_tools['email_submission_denied_content'] = isset($badgeos_admin_tools['email_submission_denied_content']) ? $badgeos_admin_tools['email_submission_denied_content'] : 'Your submission has not been approved:

			In response to: [achievement_name]
			Submitted by: [user_name]
			[achievement_link]';

		$badgeos_admin_tools['email_disable_submission_pending_email'] = isset($badgeos_admin_tools['email_disable_submission_pending_email']) ? $badgeos_admin_tools['email_disable_submission_pending_email'] : 'yes';
		$badgeos_admin_tools['email_submission_pending_subject'] = isset($badgeos_admin_tools['email_submission_pending_subject']) ? $badgeos_admin_tools['email_submission_pending_subject'] : 'Submission: [achievement_name] from [user_name]';
		$badgeos_admin_tools['email_submission_pending_content'] = isset($badgeos_admin_tools['email_submission_pending_content']) ? $badgeos_admin_tools['email_submission_pending_content'] : 'A new submission has been received:

			In response to: [achievement_name]
			Submitted by: [user_name]

			Review the complete submission and approve or deny it at:
			[submission_link]

			To view all submissions, visit: [submission_type_link]';

		// set nominations values
		$badgeos_admin_tools['email_disable_nomination_approved_email'] = isset($badgeos_admin_tools['email_disable_nomination_approved_email']) ? $badgeos_admin_tools['email_disable_nomination_approved_email'] : 'yes';
		$badgeos_admin_tools['email_nomination_approved_subject'] = isset($badgeos_admin_tools['email_nomination_approved_subject']) ? $badgeos_admin_tools['email_nomination_approved_subject'] : 'Approved Nomination: [achievement_name] from [nominee]' ;
		$badgeos_admin_tools['email_nomination_approved_content'] = isset($badgeos_admin_tools['email_nomination_approved_content']) ? $badgeos_admin_tools['email_nomination_approved_content'] : 'A new nomination has been approved:

			In response to: [achievement_name]
			Nominee: [nominee]
			Nominated by: [nominated_by]

			To view all nominations, including this one, visit: [nomination_type_link]';

		$badgeos_admin_tools['email_disable_nomination_denied_email'] = isset($badgeos_admin_tools['email_disable_nomination_denied_email']) ? $badgeos_admin_tools['email_disable_nomination_denied_email'] : 'yes';
		$badgeos_admin_tools['email_nomination_denied_subject'] = isset($badgeos_admin_tools['email_nomination_denied_subject']) ? $badgeos_admin_tools['email_nomination_denied_subject'] : 'Nomination Not Approved: [achievement_name]';
		$badgeos_admin_tools['email_nomination_denied_content'] = isset($badgeos_admin_tools['email_nomination_denied_content']) ?$badgeos_admin_tools['email_nomination_denied_content'] : 'Your nomination has not been approved:

			In response to: [achievement_name]
			Nominee: [nominee]
			Nominated by: [nominated_by]
			[achievement_link]';

		$badgeos_admin_tools['email_disable_nomination_pending_email'] = isset($badgeos_admin_tools['email_disable_nomination_pending_email']) ? $badgeos_admin_tools['email_disable_nomination_pending_email'] : 'yes';
		$badgeos_admin_tools['email_nomination_pending_subject'] = isset($badgeos_admin_tools['email_nomination_pending_subject']) ?$badgeos_admin_tools['email_nomination_pending_subject'] : 'Nomination: [achievement_name] from [nominee]' ;
		$badgeos_admin_tools['email_nomination_pending_content']  = isset($badgeos_admin_tools['email_nomination_pending_content']) ? $badgeos_admin_tools['email_nomination_pending_content'] : 'A new nomination has been received:

			In response to: [achievement_name]
			Nominee: [nominee]
			Nominated by: [nominated_by]

			Review the complete nomination and approve or deny it at:
			[nomination_link]

			To view all nominations, visit: [nomination_type_link]';

		// update nomination and submission option
		update_option('badgeos_admin_tools', $badgeos_admin_tools);

		// BadgeOS Sub/Nom submission settings
        $badgeos_sub_nom_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
        $badgeos_sub_nom_settings['submission_manager_role'] = isset( $badgeos_sub_nom_settings['submission_manager_role'] ) ? $badgeos_sub_nom_settings['submission_manager_role'] : 'manage_options';
        $badgeos_sub_nom_settings['submission_email'] = isset( $badgeos_sub_nom_settings['submission_email'] ) ? $badgeos_sub_nom_settings['submission_email'] : '';
        $badgeos_sub_nom_settings['submission_email_addresses'] = isset( $badgeos_sub_nom_settings['submission_email_addresses'] ) ? $badgeos_sub_nom_settings['submission_email_addresses'] : '';
        $badgeos_sub_nom_settings['submission_rte'] = isset( $badgeos_sub_nom_settings['submission_rte'] ) ? $badgeos_sub_nom_settings['submission_rte'] : 'enabled';

		// Update BadgeOS Sub/Nom submission settings
		update_option('badgeos_settings', $badgeos_sub_nom_settings);

	}

	/**
	 * Deactivation function hook
	 *
	 * @return void
	 */
	public static function deactivation() {
		delete_option( 'bos_ns_options' );

		return false;
	}

	/**
	 * Setup Constants
	 */
	private function setup_constants() {

		/**
		 * Plugin Text Domain
		 */
		define( 'BOSNS_TEXT_DOMAIN', 'bos-ns' );

		/**
		 * Plugin Directory
		 */
		define( 'BOSNS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'BOSNS_DIR_FILE', BOSNS_DIR . basename( __FILE__ ) );
		define( 'BOSNS_INCLUDES_DIR', trailingslashit( BOSNS_DIR . 'includes' ) );
		define( 'BOSNS_BASE_DIR', plugin_basename( __FILE__ ) );

		/**
		 * Plugin URLS
		 */
		define( 'BOSNS_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
		define( 'BOSNS_ASSETS_URL', trailingslashit( BOSNS_URL . 'assets' ) );
	}

	/**
	 * Pugin Include Required Files
	 */
	private function includes() {
		global $wp_version;
		if( file_exists( BOSNS_INCLUDES_DIR . 'functions.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'functions.php' );
		}
		
		if( file_exists( BOSNS_INCLUDES_DIR . 'submission-actions.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'submission-actions.php' );
        }
		
		if (version_compare($wp_version, '5.0.0') >= 0) {
            require_once( BOSNS_INCLUDES_DIR . 'blocks/blocks.php' );
            require_once( BOSNS_INCLUDES_DIR . 'blocks/src/init.php' );
        }

        if( file_exists( BOSNS_INCLUDES_DIR . 'integrations.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'integrations.php' );
		}
		
		if( file_exists( BOSNS_INCLUDES_DIR . 'metaboxes.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'metaboxes.php' );
		}

		if( file_exists( BOSNS_INCLUDES_DIR . 'post_types.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'post_types.php' );
		}

		/**
		 * Shortcodes
		 */
		if( file_exists( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_nomination.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_nomination.php' );
		}
		if( file_exists( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_nominations.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_nominations.php' );
		}
		if( file_exists( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_submission.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . 'shortcodes/badgeos_submission.php' );
		}
		if( file_exists( BOSNS_INCLUDES_DIR . '/shortcodes/badgeos_submissions.php' ) ) {
            require_once ( BOSNS_INCLUDES_DIR . '/shortcodes/badgeos_submissions.php' );
		}
	}

	private function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
		add_filter( 'plugin_action_links_' . BOSNS_BASE_DIR, array( $this, 'settings_link' ), 10, 1 );
		add_action( 'plugins_loaded', add_filter( 'gettext', array( $this, 'activation_message' ), 99, 3 ) );
        add_filter( 'plugin_action_links_'.BOSNS_BASE_DIR, [ $this, 'settings_link' ], 10 ,1 );
	}

	/**
	 * Translate the "Plugin activated." string
	 *
	 * @param [type] $translated_text
	 * @param [type] $untranslated_text
	 * @param [type] $domain
	 * @return void
	 */

	public function activation_message( $translated_text, $untranslated_text, $domain ) {
		$old = array(
			'Plugin <strong>activated</strong>.',
			'Selected plugins <strong>activated</strong>.',
		);

		$new = 'The Core is stable and the Plugin is <strong>deactivated</strong>';

		return $translated_text;
	}
	/**
	 * Enqueue scripts on admin
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_enqueue_style( 'bos-ns-style-admin', BOSNS_ASSETS_URL . '/css/bos-ns-admin-style.css', false, 'all' );
		wp_enqueue_script( 'bos-ns-script-admin', BOSNS_ASSETS_URL . '/js/bos-ns-script.js', array( 'jquery' ), self::VERSION, true );
		wp_localize_script('bos-ns-script-admin', 'BOSNS_VARS', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	}

	/**
	 * Enqueue scripts on frontend
	 */
	public function frontend_enqueue_scripts() {

		wp_enqueue_style( 'bos-ns-style-front', BOSNS_ASSETS_URL . '/css/bos-ns-frontend-style.css', false, 'all' );
		wp_enqueue_script( 'bos-ns-script-front', BOSNS_ASSETS_URL . '/js/bos-ns-script.js', array( 'jquery' ), self::VERSION, true );
		wp_localize_script('bos-ns-script-front', 'BOSNS_VARS', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));

	}

	/**
	 * Add settings link on plugin page
	 *
	 * @return void
	 */
	public function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=badgeos_settings&bos_s_tab=general">' . __( 'Settings', BOSNS_TEXT_DOMAIN ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}

/**
 * Display admin notifications if dependency not found.
 */
function bosns_ready() {

	if ( ! is_admin() ) {
		return;
	}

	if ( ! class_exists('BadgeOS') ) {
		$class   = 'notice is-dismissible error';
		$message = __( 'BadgeOS Nomination & Submission Add-on requires <a href="https://wordpress.org/plugins/badgeos/" >BadgeOS</a> to be activated.','bos-ns' );
		printf( '<div id="message" class="%s"> <p>%s</p></div>', $class, $message );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	return true;
}

/**
 * @return bool
 */
function BOS_Nomination_Submission() {
	
	if ( ! class_exists( 'BadgeOS' ) ) {
		add_action( 'admin_notices', 'bosns_ready' );
		return false;
	}

	$GLOBALS['BOS_Nomination_Submission'] = BOS_Nomination_Submission::instance();
}

add_action( 'plugins_loaded', 'BOS_Nomination_Submission' );