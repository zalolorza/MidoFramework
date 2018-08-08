<?php
namespace Mido;
/**
 * Mido Mail Admin class
 *
 * @package Mido
 */

class MailAdmin {


    static function init(){

      add_action('admin_menu', array('\Mido\MailAdmin','admin_default_setup'));
      add_action('admin_init', array('\Mido\MailAdmin','admin_init'));
      add_action('admin_enqueue_scripts', array('\Mido\MailAdmin','admin_head'));

    }

    static function admin_head(){

      if (isset($_REQUEST['page']) && 'mido_mail_settings' == $_REQUEST['page']) {
          wp_enqueue_style('mido_mail_stylesheet', plugins_url('assets/css/mido_mail.css'));
          //wp_enqueue_script('mido_mail_script', plugins_url('assets/js/mido_mail.js'), array('jquery'));
      }

    }
    /**
     * Add menu and submenu.
     * @return void
     */
    static function admin_default_setup() {

      add_options_page(__('Mail', 'mido-admin'), __('Mail', 'mido-admin'), 'manage_options', 'mido_mail_settings', array('\Mido\MailAdmin','admin_settings'));
    }

    /**
     * Renders the admin settings menu of the plugin.
     * @return void
     */
    static function admin_settings() {
        echo '<div class="wrap" id="mido_mail-mail">';
        echo '<h2>' . __("Mail SMTP Settings", 'mido-admin') . '</h2>';
        echo '<div id="poststuff"><div id="post-body">';

        $display_add_options = $message = $error = $result = '';

        $mido_mail_options = get_option('mido_mail_options');


        if (isset($_POST['mido_mail_form_submit']) && check_admin_referer(plugin_basename(__FILE__), 'mido_mail_nonce_name')) {
            /* Update settings */
            $mido_mail_options['from_name'] = isset($_POST['mido_mail_from_name']) ? sanitize_text_field(wp_unslash($_POST['mido_mail_from_name'])) : '';
            if (isset($_POST['mido_mail_from_email'])) {
                if (is_email($_POST['mido_mail_from_email'])) {
                    $mido_mail_options['from_email'] = sanitize_email($_POST['mido_mail_from_email']);
                } else {
                    $error .= " " . __("Please enter a valid email address in the 'FROM' field.", 'mido-admin');
                }
            }

            $mido_mail_options['host'] = sanitize_text_field($_POST['mido_mail_smtp_host']);
            $mido_mail_options['type_encryption'] = ( isset($_POST['mido_mail_smtp_type_encryption']) ) ? sanitize_text_field($_POST['mido_mail_smtp_type_encryption']) : 'none';
            $mido_mail_options['autentication'] = ( isset($_POST['mido_mail_smtp_autentication']) ) ? sanitize_text_field($_POST['mido_mail_smtp_autentication']) : 'yes';
            $mido_mail_options['username'] = sanitize_text_field($_POST['mido_mail_smtp_username']);
            $smtp_password = stripslashes($_POST['mido_mail_smtp_password']);
            $mido_mail_options['password'] = base64_encode($smtp_password);

            /* Check value from "SMTP port" option */
            if (isset($_POST['mido_mail_smtp_port'])) {
                if (empty($_POST['mido_mail_smtp_port']) || 1 > intval($_POST['mido_mail_smtp_port']) || (!preg_match('/^\d+$/', $_POST['mido_mail_smtp_port']) )) {
                    $mido_mail_options['port'] = '25';
                    $error .= " " . __("Please enter a valid port in the 'SMTP Port' field.", 'mido-admin');
                } else {
                    $mido_mail_options['port'] = sanitize_text_field($_POST['mido_mail_smtp_port']);
                }
            }

            /* Update settings in the database */
            if (empty($error)) {
                update_option('mido_mail_options', $mido_mail_options);
                $message .= __("Settings saved.", 'mido-admin');
            } else {
                $error .= " " . __("Settings are not saved.", 'mido-admin');
            }
        }
        ?>


        <div class="updated fade" <?php if (empty($message)) echo "style=\"display:none\""; ?>>
            <p><strong><?php echo $message; ?></strong></p>
        </div>
        <div class="error" <?php if (empty($error)) echo "style=\"display:none\""; ?>>
            <p><strong><?php echo $error; ?></strong></p>
        </div>
        <div id="mido_mail-settings-notice" class="updated fade" style="display:none">
            <p><strong><?php _e("Notice:", 'mido-admin'); ?></strong> <?php _e("The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'mido-admin'); ?></p>
        </div>

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php _e('SMTP Configuration Settings', 'mido-admin'); ?></label></h3>
            <div class="inside">

                <p><?php _e('You can request your hosting provider for the SMTP details of your site. Use the SMTP details provided by your hosting provider to configure the following settings.','mido-admin');?></p>

                <form id="mido_mail_settings_form" method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e("From Email Address", 'mido-admin'); ?></th>
                            <td>
                                <input type="text" name="mido_mail_from_email" value="<?php echo esc_attr($mido_mail_options['from_email']); ?>"/><br />
                                <p class="description"><?php _e("This email address will be used in the 'From' field.", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e("From Name", 'mido-admin'); ?></th>
                            <td>
                                <input type="text" name="mido_mail_from_name" value="<?php echo esc_attr($mido_mail_options['from_name']); ?>"/><br />
                                <p class="description"><?php _e("This text will be used in the 'FROM' field", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('SMTP Host', 'mido-admin'); ?></th>
                            <td>
                                <input type='text' name='mido_mail_smtp_host' value='<?php echo esc_attr($mido_mail_options['host']); ?>' /><br />
                                <p class="description"><?php _e("Your mail server", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('Type of Encryption', 'mido-admin'); ?></th>
                            <td>
                                <label for="mido_mail_smtp_type_encryption_1"><input type="radio" id="mido_mail_smtp_type_encryption_1" name="mido_mail_smtp_type_encryption" value='none' <?php if ('none' == $mido_mail_options['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('None', 'mido-admin'); ?></label>
                                <label for="mido_mail_smtp_type_encryption_2"><input type="radio" id="mido_mail_smtp_type_encryption_2" name="mido_mail_smtp_type_encryption" value='ssl' <?php if ('ssl' == $mido_mail_options['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('SSL', 'mido-admin'); ?></label>
                                <label for="mido_mail_smtp_type_encryption_3"><input type="radio" id="mido_mail_smtp_type_encryption_3" name="mido_mail_smtp_type_encryption" value='tls' <?php if ('tls' == $mido_mail_options['type_encryption']) echo 'checked="checked"'; ?> /> <?php _e('TLS', 'mido-admin'); ?></label><br />
                                <p class="description"><?php _e("For most servers SSL is the recommended option", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('SMTP Port', 'mido-admin'); ?></th>
                            <td>
                                <input type='text' name='mido_mail_smtp_port' value='<?php echo esc_attr($mido_mail_options['port']); ?>' /><br />
                                <p class="description"><?php _e("The port to your mail server", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('SMTP Authentication', 'mido-admin'); ?></th>
                            <td>
                                <label for="mido_mail_smtp_autentication"><input type="radio" id="mido_mail_smtp_autentication" name="mido_mail_smtp_autentication" value='no' <?php if ('no' == $mido_mail_options['autentication']) echo 'checked="checked"'; ?> /> <?php _e('No', 'mido-admin'); ?></label>
                                <label for="mido_mail_smtp_autentication"><input type="radio" id="mido_mail_smtp_autentication" name="mido_mail_smtp_autentication" value='yes' <?php if ('yes' == $mido_mail_options['autentication']) echo 'checked="checked"'; ?> /> <?php _e('Yes', 'mido-admin'); ?></label><br />
                                <p class="description"><?php _e("This options should always be checked 'Yes'", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('SMTP username', 'mido-admin'); ?></th>
                            <td>
                                <input type='text' name='mido_mail_smtp_username' value='<?php echo esc_attr($mido_mail_options['username']); ?>' /><br />
                                <p class="description"><?php _e("The username to login to your mail server", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                        <tr class="ad_opt mido_mail_smtp_options">
                            <th><?php _e('SMTP Password', 'mido-admin'); ?></th>
                            <td>
                                <input type='password' name='mido_mail_smtp_password' value='<?php echo esc_attr(\Mido\Mail::get_password($mido_mail_options)); ?>' /><br />
                                <p class="description"><?php _e("The password to login to your mail server", 'mido-admin'); ?></p>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" id="settings-form-submit" class="button-primary" value="<?php _e('Save Changes', 'mido-admin') ?>" />
                        <input type="hidden" name="mido_mail_form_submit" value="submit" />
                        <?php wp_nonce_field(plugin_basename(__FILE__), 'mido_mail_nonce_name'); ?>
                    </p>
                </form>
            </div><!-- end of inside -->
        </div><!-- end of postbox -->



        <?php
        echo '</div></div>'; //<!-- end of #poststuff and #post-body -->
        echo '</div>'; //<!--  end of .wrap #mido_mail-mail .mido_mail-mail -->
    }

    /**
     * Plugin functions for init
     * @return void
     */
    static function admin_init() {
        /* Internationalization, first(!) */
        load_plugin_textdomain('mido-admin', false, dirname(plugin_basename(__FILE__)) . '/languages/');


        if (isset($_REQUEST['page']) && 'mido_mail_settings' == $_REQUEST['page']) {
            /* register plugin settings */
            self::admin_register_settings();
        }
    }

    /**
     * Register settings function
     * @return void
     */
    function admin_register_settings() {


        $options_default = array(
            'from_email' => 'no-reply@webform.email',
            'from_name' => get_bloginfo('name'),
            'host' => 'www.webform.email',
            'type_encryption' => 'none',
            'port' => 587,
            'autentication' => 'yes',
            'username' => 'no-reply@webform.email',
            'password' => 'Noreply44$'
        );

        /* install the default plugin options */

        if (!get_option('mido_mail_options')) {
            add_option('mido_mail_options', $options_default, '', 'yes');
        }
    }


}
