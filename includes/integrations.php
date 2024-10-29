<?php
    
    class BOS_Nomination_Submission_Integrations {
        
        /**
         * constructor.
         */
        public function __construct() {

            add_action( 'badgeos_general_settings_tab_header', array( $this, 'settings_tab_header' ) );
            add_action( 'badgeos_general_settings_tab_content', array( $this, 'settings' ) );
            
            add_action( 'badgeos_tools_badgeos_information', array( $this, 'badgeos_tools_information' ) );
            add_action( 'badgeos_award_achievement', array( $this, 'badgeos_maybe_update_submission_nomination_meta_of_user'), 10, 6 );

            // nomination and submission email tool
            add_action( 'badgeos_email_tools_settings_tab_header', array($this, 'badgeos_nomination_submission_email_tools_settings_tab_header'), 10, 1 );

            add_action( 'badgeos_email_tools_settings_tab_content', array($this, 'badgeos_nomination_submission_email_tools_settings_tab_content'), 10, 1 );

            add_action( 'admin_init', [ $this, 'badgeos_nomination_submission_email_tools_settings_save' ] );
        }

        /**
         * Update user meta to limit the repeated award process
         *
         * @since  1.0.0
         * @param  integer $user_id
         * @param  integer $achievement_id
         * @param  integer $this_trigger
         * @param  integer $site_id
         * @param  integer $args
         * @param  integer $user_id
         * @param  integer $entry_id
         * @return void
         */
        function badgeos_maybe_update_submission_nomination_meta_of_user( $user_id = 0, $achievement_id = 0, $this_trigger = '', $site_id = 0, $args=array(), $entry_id = 0 ) {
            global $wpdb;
            if( isset( $args['submission_id'] ) && intval( $args['submission_id'] ) > 0 ) {

                $strQuery = 'Update '.$wpdb->prefix."badgeos_achievements set sub_nom_id='".$args['submission_id']."' where entry_id='".$entry_id."'";
                $wpdb->query( $strQuery );
            }
        }
        

        /**
         * Register add-on settings.
         *
         * @since 1.0.0
         */
        function badgeos_tools_information( $badgeos_settings = array() ) {
            $submission_manager_role = ( isset( $badgeos_settings['submission_manager_role'] ) ) ? $badgeos_settings['submission_manager_role'] : 'manage_options';
            $submission_email = ( isset( $badgeos_settings['submission_email'] ) && 'disabled' !=  $badgeos_settings['submission_email'] ) ? $badgeos_settings['submission_email'] : 'disabled';
            $submission_email_addresses = ( isset( $badgeos_settings['submission_email_addresses'] ) ) ? $badgeos_settings['submission_email_addresses'] : '';

            ?>
                <tr>
                    <th scope="row">
                        <label for="minimum_role_for_submission">
                            <?php _e( 'Min Role for Submission/Nomination?', 'badgeos' ); ?>
                        </label>
                    </th>
                    <td><?php echo $submission_manager_role; ?></td>
                </tr>
                <!-- <tr>
                    <th scope="row">
                        <label for="email_on_submission">
                            <?php _e( 'Email on submissions/nominations?', 'badgeos' ); ?>
                        </label>
                    </th>
                    <td><?php echo ( 'disabled' != $submission_email ) ? show_symbol_on_tool_page('success') : show_symbol_on_tool_page('failure') ; ?></td>
                </tr> -->
            <?php
        }

        /**
         * Register add-on settings.
         *
         * @since 1.0.0
         */
        function settings_tab_header( $settings = array() ) {
            ?>
                <li>
                    <a href="#badgeos_settings_submnomi_settings">
                        &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?php _e( 'Submissions/Nominations', 'badgeos' ); ?>
                    </a>
                </li>
            <?php
        }

        /**
         * Register Admin Email Tools Settings header
         *
         * @since 1.0
         * @return void
         */
        function badgeos_nomination_submission_email_tools_settings_tab_header( $badgeos_admin_tools = array() ){
        ?>
            <li>
                <a id="badgeos_tools_email_submissions_link" href="#badgeos_tools_email_submissions">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-bolt" aria-hidden="true"></i>&nbsp;&nbsp;
                    <?php _e( 'Submissions', 'badgeos' ); ?>
                </a>
            </li>
            <li>
                <a id="badgeos_tools_email_nominations_link" href="#badgeos_tools_email_nominations">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-podcast" aria-hidden="true"></i>&nbsp;&nbsp;
                    <?php _e( 'Nominations', 'badgeos' ); ?>
                </a>
            </li>
        <?php
        }

        /**
         * Register Admin Email Tools Settings content
         * @since 1.0
         * 
         * @return void
         */
        function badgeos_nomination_submission_email_tools_settings_tab_content( $badgeos_admin_tools = array() ){

            include_once(BOSNS_INCLUDES_DIR.'badgeos-nomination-submission-email-tool.php');
        }

        function badgeos_nomination_submission_email_tools_settings_save()
        {
            if(! $_POST || $_SERVER['REQUEST_METHOD'] != 'POST')
            {
                return false;
            }
        
            $badgeos_admin_tools = ($exists = get_option('badgeos_admin_tools')) ? $exists : array();


            // set submissions values

            if((isset($_POST['action']) && $_POST['action']=='badgeos_tools_email_submission'))
            {
                if(isset($_POST['badgeos_tools_email_submission']))
                {

                    $tools_data = $_POST['badgeos_tools'];

                    // Aproved Submissions
                    $email_disable_submission_approved_email = 'no';

                    if( isset( $tools_data['email_disable_submission_approved_email'] ) ) {
                        $email_disable_submission_approved_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_submission_approved_subject'] = sanitize_text_field( $tools_data[ 'email_submission_approved_subject' ] );

                    $badgeos_admin_tools['email_submission_approved_content']               = htmlentities( $tools_data[ 'email_submission_approved_content' ] );
                    $badgeos_admin_tools['email_disable_submission_approved_email']  = $email_disable_submission_approved_email;

                    // Denied Submissions
                    $email_disable_submission_denied_email = 'no';

                    if( isset( $tools_data['email_disable_submission_denied_email'] ) ) {
                        $email_disable_submission_denied_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_submission_denied_subject'] = sanitize_text_field( $tools_data[ 'email_submission_denied_subject' ] );

                    $badgeos_admin_tools['email_submission_denied_content']               = htmlentities( $tools_data[ 'email_submission_denied_content' ] );
                    $badgeos_admin_tools['email_disable_submission_denied_email']  = $email_disable_submission_denied_email;

                    $email_disable_submission_pending_email = 'no';

                    if( isset( $tools_data['email_disable_submission_pending_email'] ) ) {
                        $email_disable_submission_pending_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_submission_pending_subject'] = sanitize_text_field( $tools_data[ 'email_submission_pending_subject' ] );

                    $badgeos_admin_tools['email_submission_pending_content']               = htmlentities( $tools_data[ 'email_submission_pending_content' ] );
                    $badgeos_admin_tools['email_disable_submission_pending_email']  = $email_disable_submission_pending_email;

                    update_option( 'badgeos_admin_tools', $badgeos_admin_tools );
                    add_action( 'admin_notices', array( $this, 'badgeos_nomination_submission_tools_email_notice_success' ) );
                }
            }

            // set nominations option values

            if((isset($_POST['action']) && $_POST['action']=='badgeos_tools_email_nomination'))
            {
                if(isset($_POST['badgeos_tools_email_nomination']))
                {

                    $tools_data = $_POST['badgeos_tools'];

                    // Aproved Submissions
                    $email_disable_nomination_approved_email = 'no';

                    if( isset( $tools_data['email_disable_nomination_approved_email'] ) ) {
                        $email_disable_nomination_approved_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_nomination_approved_subject'] = sanitize_text_field( $tools_data[ 'email_nomination_approved_subject' ] );

                    $badgeos_admin_tools['email_nomination_approved_content'] = htmlentities( $tools_data[ 'email_nomination_approved_content' ] );
                    $badgeos_admin_tools['email_disable_nomination_approved_email']  = $email_disable_nomination_approved_email;

                    // Denied niminations
                    $email_disable_nomination_denied_email = 'no';

                    if( isset( $tools_data['email_disable_nomination_denied_email'] ) ) {
                        $email_disable_nomination_denied_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_nomination_denied_subject'] = sanitize_text_field( $tools_data[ 'email_nomination_denied_subject' ] );

                    $badgeos_admin_tools['email_nomination_denied_content']               = htmlentities( $tools_data[ 'email_nomination_denied_content' ] );
                    $badgeos_admin_tools['email_disable_nomination_denied_email']  = $email_disable_nomination_denied_email;

                    // pending nominations
                    $email_disable_nomination_pending_email = 'no';

                    if( isset( $tools_data['email_disable_nomination_pending_email'] ) ) {
                        $email_disable_nomination_pending_email = 'yes';
                    }
                    
                    $badgeos_admin_tools['email_nomination_pending_subject'] = sanitize_text_field( $tools_data[ 'email_nomination_pending_subject' ] );

                    $badgeos_admin_tools['email_nomination_pending_content'] = htmlentities( $tools_data[ 'email_nomination_pending_content' ] );
                    $badgeos_admin_tools['email_disable_nomination_pending_email']  = $email_disable_nomination_pending_email;

                    update_option( 'badgeos_admin_tools', $badgeos_admin_tools );
                    add_action( 'admin_notices', array( $this, 'badgeos_nomination_submission_tools_email_notice_success' ) );

                }
            }
        
        }
        
        /**
         * Update messages
         *
         * @return mixed|void
         */
        public function badgeos_nomination_submission_tools_email_notice_success() {
            ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( 'Settings Saved.', 'badgeos' ); ?></p>
                </div>
            <?php
        }

        /**
         * Register add-on settings.
         *
         * @since 1.0.0
         */
        function settings( $badgeos_settings = array() ) {
            
            $badgeos_admin_side_tab 	= ( ! empty ( $badgeos_settings['side_tab'] ) ) ? $badgeos_settings['side_tab'] : '#badgeos_settings_submnomi_settings';
            
            //load settings
            $submission_manager_role = ( isset( $badgeos_settings['submission_manager_role'] ) ) ? $badgeos_settings['submission_manager_role'] : 'manage_options';
            $submission_email = ( isset( $badgeos_settings['submission_email'] ) ) ? $badgeos_settings['submission_email'] : '';
            $submission_email_addresses = ( isset( $badgeos_settings['submission_email_addresses'] ) ) ? $badgeos_settings['submission_email_addresses'] : '';
            
            $submission_rte = ( ! empty( $badgeos_settings['submission_rte'] ) ) ? $badgeos_settings['submission_rte'] : 'disabled';
            
            ?>
            <div id="badgeos_settings_submnomi_settings">
                <table cellspacing="0" width="100%">
                    <?php if ( current_user_can( 'manage_options' ) ) { ?>
                        <tr valign="top">
                            <th scope="row" width="50%"><label for="submission_manager_role"><?php _e( 'Minimum Role to Administer Submissions/Nominations: ', 'badgeos' ); ?></label></th>
                            <td width="50%">
                                <select id="submission_manager_role" name="badgeos_settings[submission_manager_role]">
                                    <option value="manage_options" <?php selected( $submission_manager_role, 'manage_options' ); ?>><?php _e( 'Administrator', 'badgeos' ); ?></option>
                                    <option value="delete_others_posts" <?php selected( $submission_manager_role, 'delete_others_posts' ); ?>><?php _e( 'Editor', 'badgeos' ); ?></option>
                                    <option value="publish_posts" <?php selected( $submission_manager_role, 'publish_posts' ); ?>><?php _e( 'Author', 'badgeos' ); ?></option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr valign="top">
                        <th scope="row"><label for="submission_email"><?php _e( 'Send email when submissions/nominations are approved:', 'badgeos' ); ?></label></th>
                        <td>
                            <select id="submission_email" name="badgeos_settings[submission_email]">
                                <option value="enabled" <?php selected( $submission_email, 'enabled' ); ?>><?php _e( 'Enabled', 'badgeos' ) ?></option>
                                <option value="disabled" <?php selected( $submission_email, 'disabled' ); ?>><?php _e( 'Disabled', 'badgeos' ) ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label class="badgeos_subnom_email_addresses_cls" for="submission_email_addresses"><?php _e( 'Notification email addresses:', 'badgeos' ); ?></label></th>
                        <td>
                            <input class="badgeos_subnom_email_addresses_cls" id="submission_email_addresses" name="badgeos_settings[submission_email_addresses]" type="text" value="<?php echo esc_attr( $submission_email_addresses ); ?>" class="regular-text" />
                            <p class="description badgeos_subnom_email_addresses_cls"><?php _e( 'Comma-separated list of email addresses to send submission/nomination notifications, in addition to the Site Admin email.', 'badgeos' ); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="submission_rte"><?php _e( 'Enable rich text editor for Submissions/Nominations', 'badgeos' ); ?></label></th>
                        <td>
                            <select id="submission_rte" name="badgeos_settings[submission_rte]">
                                <option value="enabled" <?php selected( $submission_rte, 'enabled' ); ?>><?php _e( 'Enabled', 'badgeos' ) ?></option>
                                <option value="disabled" <?php selected( $submission_rte, 'disabled' ); ?>><?php _e( 'Disabled', 'badgeos' ) ?></option>
                            </select>
                            <p class="description submission_rte"><?php _e( 'Enabling this setting will change submission text area to rich text editor.', 'badgeos' ); ?></p>
                        </td>
                    </tr>
                </table>
                <input type="submit" name="badgeos_settings_update_btn" class="button button-primary" value="<?php _e( 'Save Settings', 'badgeos-congrats' ); ?>">
            </div>
            <?php
        } /* settings() */

    }

    new BOS_Nomination_Submission_Integrations();
?>