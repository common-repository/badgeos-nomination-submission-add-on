<?php 
/**
 * BadgeOS Nomination & Submissions Email Tools
 *
 * @package BadgeOS
 * @subpackage Tools
 * @author LearningTimes, LLC
 * @license http://www.gnu.org/licenses/agpl.txt GNU AGPL v3.0
 * @link https://credly.com
 */
$badgeos_admin_tools = ( $exists = get_option( 'badgeos_admin_tools' ) ) ? $exists : array();

// set submissions values
$email_disable_submission_approved_email = (isset($badgeos_admin_tools['email_disable_submission_approved_email']) && $badgeos_admin_tools['email_disable_submission_approved_email']=="yes" ) ? 'checked' : '';
$email_submission_approved_subject = isset($badgeos_admin_tools['email_submission_approved_subject']) ? $badgeos_admin_tools['email_submission_approved_subject'] : '' ;
$email_submission_approved_content  = isset( $badgeos_admin_tools['email_submission_approved_content'] ) ? $badgeos_admin_tools['email_submission_approved_content'] : '';
$email_submission_approved_content  = stripslashes( html_entity_decode( $email_submission_approved_content ) );

$email_disable_submission_denied_email = (isset($badgeos_admin_tools['email_disable_submission_denied_email']) && $badgeos_admin_tools['email_disable_submission_denied_email']=="yes" ) ? 'checked' : '';
$email_submission_denied_subject = isset($badgeos_admin_tools['email_submission_denied_subject']) ? $badgeos_admin_tools['email_submission_denied_subject'] : '' ;
$email_submission_denied_content  = isset( $badgeos_admin_tools['email_submission_denied_content'] ) ? $badgeos_admin_tools['email_submission_denied_content'] : '';
$email_submission_denied_content  = stripslashes( html_entity_decode( $email_submission_denied_content ) );

$email_disable_submission_pending_email = (isset($badgeos_admin_tools['email_disable_submission_pending_email']) && $badgeos_admin_tools['email_disable_submission_pending_email']=="yes" ) ? 'checked' : '';
$email_submission_pending_subject = isset($badgeos_admin_tools['email_submission_pending_subject']) ? $badgeos_admin_tools['email_submission_pending_subject'] : '' ;
$email_submission_pending_content  = isset( $badgeos_admin_tools['email_submission_pending_content'] ) ? $badgeos_admin_tools['email_submission_pending_content'] : '';
$email_submission_pending_content  = stripslashes( html_entity_decode( $email_submission_pending_content ) );

// set nominations values
$email_disable_nomination_approved_email = (isset($badgeos_admin_tools['email_disable_nomination_approved_email']) && $badgeos_admin_tools['email_disable_nomination_approved_email']=="yes" ) ? 'checked' : '';
$email_nomination_approved_subject = isset($badgeos_admin_tools['email_nomination_approved_subject']) ? $badgeos_admin_tools['email_nomination_approved_subject'] : '' ;
$email_nomination_approved_content  = isset( $badgeos_admin_tools['email_nomination_approved_content'] ) ? $badgeos_admin_tools['email_nomination_approved_content'] : '';
$email_nomination_approved_content  = stripslashes( html_entity_decode( $email_nomination_approved_content ) );

$email_disable_nomination_denied_email = (isset($badgeos_admin_tools['email_disable_nomination_denied_email']) && $badgeos_admin_tools['email_disable_nomination_denied_email']=="yes" ) ? 'checked' : '';
$email_nomination_denied_subject = isset($badgeos_admin_tools['email_nomination_denied_subject']) ? $badgeos_admin_tools['email_nomination_denied_subject'] : '' ;
$email_nomination_denied_content  = isset( $badgeos_admin_tools['email_nomination_denied_content'] ) ? $badgeos_admin_tools['email_nomination_denied_content'] : '';
$email_nomination_denied_content  = stripslashes( html_entity_decode( $email_nomination_denied_content ) );

$email_disable_nomination_pending_email = (isset($badgeos_admin_tools['email_disable_nomination_pending_email']) && $badgeos_admin_tools['email_disable_nomination_pending_email']=="yes" ) ? 'checked' : '';
$email_nomination_pending_subject = isset($badgeos_admin_tools['email_nomination_pending_subject']) ? $badgeos_admin_tools['email_nomination_pending_subject'] : '' ;
$email_nomination_pending_content  = isset( $badgeos_admin_tools['email_nomination_pending_content'] ) ? $badgeos_admin_tools['email_nomination_pending_content'] : '';
$email_nomination_pending_content  = stripslashes( html_entity_decode( $email_nomination_pending_content ) );

?>

<div id="badgeos_tools_email_submissions">
    <form method="POST" class="badgeos_tools_email_submissions" action="">
        <table cellspacing="0" class="badgeos_tools_email_submissions_approved_tabel">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_submission_approved_email]" id="badgeos_tools_email_disable_submission_approved_email" <?php echo $email_disable_submission_approved_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Submission Approved Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_approved_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_submission_approved_subject]" value="<?php echo $email_submission_approved_subject; ?>" size="50" id="badgeos_tools_email_submission_approved_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_approved_content"><?php _e( 'Content', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <?php wp_editor( $email_submission_approved_content, 'badgeos_tools_email_submission_approved_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_submission_approved_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name], [submission_type_link], [achievement_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table cellspacing="0" class="badgeos_tools_email_submissions_denied_tabel">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_submission_denied_email]" id="badgeos_tools_email_disable_submission_denied_email" <?php echo $email_disable_submission_denied_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Submission Denied Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_denied_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_submission_denied_subject]" value="<?php echo $email_submission_denied_subject; ?>" size="50" id="badgeos_tools_email_submission_denied_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_denied_content"><?php _e( 'Content', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <?php wp_editor( $email_submission_denied_content, 'badgeos_tools_email_submission_denied_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_submission_denied_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name], [achievement_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table cellspacing="0" class="badgeos_tools_email_submissions_pending_tabel">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_submission_pending_email]" id="badgeos_tools_email_disable_submissio_pending_email" <?php echo $email_disable_submission_pending_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Submission Pending Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_pending_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_submission_pending_subject]" value="<?php echo $email_submission_pending_subject; ?>" size="50" id="badgeos_tools_email_submission_pending_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_submission_pending_content"><?php _e( 'Content', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <?php wp_editor( $email_submission_pending_content, 'badgeos_tools_email_submission_pending_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_submission_pending_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name], [submission_link], [submission_type_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field( 'badgeos_tools_email', 'badgeos_tools_email_submission' ); ?>
        <input type="hidden" name="action" value="badgeos_tools_email_submission">
        <input type="hidden" name="badgeos_tools_email_tab" value="badgeos_tools_email_submissions" />
        <input type="submit" name="badgeos_tools_email_submission_save" class="button button-primary" value="<?php _e( 'Save', 'badgeos' ); ?>">
    </form>
</div>
<div id="badgeos_tools_email_nominations">
    <form method="POST" class="badgeos_tools_email_nominations" action="">
        <table cellspacing="0" class="badgeos_tools_email_nominations_approved_table">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_nomination_approved_email]" id="badgeos_tools_email_disable_nomination_approved_email" <?php echo $email_disable_nomination_approved_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Nomination Approved Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_nomination_approved_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_nomination_approved_subject]" value="<?php echo $email_nomination_approved_subject; ?>" size="50" id="badgeos_tools_email_nomination_approved_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [nominee], [nominated_by]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top"><label for="badgeos_tools_email_nomination_approved_content"><?php _e( 'Content', 'badgeos' ); ?></label></th>
                    <td>
                        <?php wp_editor( $email_nomination_approved_content, 'badgeos_tools_email_nomination_approved_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_nomination_approved_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [user_name], [nominated_by], [nomination_type_link], [achievement_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table cellspacing="0" class="badgeos_tools_email_nominations_denied_table">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_nomination_denied_email]" id="badgeos_tools_email_disable_nomination_denied_email" <?php echo $email_disable_nomination_denied_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Nomination Denied Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_nomination_denied_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_nomination_denied_subject]" value="<?php echo $email_nomination_denied_subject; ?>" size="50" id="badgeos_tools_email_nomination_denied_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [nominee], [nominated_by]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top"><label for="badgeos_tools_email_nomination_denied_content"><?php _e( 'Content', 'badgeos' ); ?></label></th>
                    <td>
                        <?php wp_editor( $email_nomination_denied_content, 'badgeos_tools_email_nomination_denied_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_nomination_denied_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [nominee], [nominated_by], [achievement_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <table cellspacing="0" class="badgeos_tools_email_nominations_pending_table">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="form-switcher form-switcher-lg">
                            <input type="checkbox" class="badgeos_tools_disable_email_checkboxes" name="badgeos_tools[email_disable_nomination_pending_email]" id="badgeos_tools_email_disable_nomination_pending_email" <?php echo $email_disable_nomination_pending_email; ?> data-com.bitwarden.browser.user-edited="yes">
                            <?php _e( 'Disable Nomination Pending Email', 'badgeos' ); ?>
                        </div>
                        <span class="tool-hint"><?php _e( 'Select the checkbox to stop sending the emails.', 'badgeos' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_nomination_pending_subject"><?php _e( 'Subject', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="badgeos_tools_email_achievement_field" name="badgeos_tools[email_nomination_pending_subject]" value="<?php echo $email_nomination_pending_subject; ?>" size="50" id="badgeos_tools_email_nomination_pending_subject" />
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [nominee], [nominated_by]</p>
                        <span class="tool-hint"><?php _e( 'Email Subject', 'badgeos' ); ?></span>
                    </td>
                </tr> 
                <tr>
                    <th scope="row" valign="top">
                        <label for="badgeos_tools_email_nomination_pending_content"><?php _e( 'Content', 'badgeos' ); ?></label>
                    </th>
                    <td>
                        <?php wp_editor( $email_nomination_pending_content, 'badgeos_tools_email_nomination_pending_content', array('media_buttons' => true, 'editor_height' => 500, 'textarea_rows' => 20, 'textarea_name' => 'badgeos_tools[email_nomination_pending_content]' ) ); ?>
                        <span class="badgeos_tools_email_achievement_field tool-hint"><?php _e( 'Content', 'badgeos' ); ?></span>
                        <p><b><?php _e( 'Shortcodes', 'badgeos' ); ?>:</b> [achievement_name], [nominee], [nominated_by], [nomination_link], [nomination_type_link]</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field( 'badgeos_tools_email', 'badgeos_tools_email_nomination' ); ?>
        <input type="hidden" name="action" value="badgeos_tools_email_nomination">
        <input type="hidden" name="badgeos_tools_email_tab" value="badgeos_tools_email_nominations" />
        <input type="submit" name="badgeos_tools_email_nomination_save" class="button button-primary" value="<?php _e( 'Save', 'badgeos' ); ?>">
    </form>
</div>