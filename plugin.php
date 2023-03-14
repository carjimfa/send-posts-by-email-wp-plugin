<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

/*
Plugin Name:	Send new post by email
Plugin URI:		https://drapergiggs.com
Description:	Send new posts by email to a mailing list in mailjet.
Version:		0.0.24
Author:			Carlos Draper Giggs
Author URI:		https://drapergiggs.com
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined('WPINC')) {
	die;
}

// SECTION: ADMIN PAGE

function admin_menu() {
	add_menu_page(
		__('Posts by Email', 'posts-by-email' ),
		__('Posts by email', 'posts-by-email' ),
		'manage_options',
		'posts-by-email',
		'admin_page_contents',
		'dashicons-schedule',
		3
	);
}


add_action('admin_menu', 'admin_menu');
add_action('admin_init', 'settings_init');

function settings_init() {
    add_settings_section(
        'page_setting_section',
        __('Posts by email settings', 'posts-by-email' ),
        'section_callback_function',
        'posts-by-email'
    );

	add_settings_field(
		'account-sid',
		__('Mailjet Account SID', 'posts-by-email' ),
		'account_sid_settings_markup',
		'posts-by-email',
		'page_setting_section'
	 );

    add_settings_field(
        'account-secret',
        __('Mailjet Account Secret', 'posts-by-email' ),
        'account_token_settings_markup',
        'posts-by-email',
        'page_setting_section'
    );

    add_settings_field(
        'mailing_list_id',
        __('Mailing list Id', 'posts-by-email' ),
        'mailing_list_id_settings_markup',
        'posts-by-email',
        'page_setting_section'
    );

	 register_setting('posts-by-email', 'account-sid');
	 register_setting('posts-by-email', 'account-secret');
	 register_setting('posts-by-email', 'mailing_list_id');
}

function section_callback_function() {
    echo '<p>Here you can setup everything for sending your new posts by email</p>';
}

function admin_page_contents() {
    ?>
    <h1> <?php esc_html_e('Welcome to my custom admin page.', 'my-plugin-textdomain'); ?> </h1>
    <form method="POST" action="options.php">
    <?php
    settings_fields('posts-by-email');
    do_settings_sections('posts-by-email');
    submit_button();
    ?>
    </form>
    <?php
}

function account_sid_settings_markup() {
    ?>
    <label for="account-sid"><?php _e('Account SID', 'account-sid'); ?></label>
    <input type="text" id="account-sid" name="account-sid" value="<?php echo get_option('account-sid'); ?>">
    <?php
}

function account_token_settings_markup() {
    ?>
    <label for="account-secret"><?php _e('Account secret', 'account-sid'); ?></label>
    <input type="text" id="account-secret" name="account-secret" value="<?php echo get_option('account-secret'); ?>">
    <?php
}

function mailing_list_id_settings_markup() {
    ?>
    <label for="mailing_list_id"><?php _e('Account secret', 'mailing_list_id'); ?></label>
    <input type="text" id="mailing_list_id" name="mailing_list_id" value="<?php echo get_option('mailing_list_id'); ?>">
    <?php
}


// SECTION: HOOK FOR NEW POSTS
add_action('transition_post_status', 'send_new_post', 10, 3);

function send_new_post($new_status, $old_status, $post) {
// 	if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'post') {
		send_post_by_email($post);
// 	}
}

function send_post_by_email($post) {
    $mail = new PHPMailer();

	$subject = $post->post_title;
	$content = $post->post_content;
	$mailingListId = get_option('mailing_list_id');
	$username = get_option('account-sid');
	$password = get_option('account-secret');

    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'in-v3.mailjet.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = $username;                     //SMTP username
    $mail->Password   = $password;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('carlos@dunkelheitdraper.com', 'Carlos Draper Giggs');
    $mail->addAddress($mailingListId);     //Add a recipient
    $mail->addReplyTo('carlos@dunkelheitdraper.com', 'Carlos Draper Giggs');

    //Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $content;

    $mail->send();
}