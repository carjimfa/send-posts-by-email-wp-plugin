<?php
require 'mailjet-apiv3/vendor/autoload.php';

use \Mailjet\Resources;

/*
Plugin Name:	Send new post by email
Plugin URI:		https://drapergiggs.com
Description:	Send new posts by email to a mailing list in mailjet.
Version:		0.0.20
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

	 register_setting('posts-by-email', 'account-sid');
	 register_setting('posts-by-email', 'account-secret');
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


// SECTION: HOOK FOR NEW POSTS
add_action('transition_post_status', 'send_new_post', 10, 3);

function send_new_post($new_status, $old_status, $post) {
	if('publish' === $new_status && 'publish' !== $old_status && $post->post_type === 'post') {
		send_post_by_email($post);
	}
}
function send_post_by_email($post) {
	$mj = new \Mailjet\Client(get_option('account-sid' ), get_option('account-secret' ), true, ['version' => 'v3.1']);

	$subject = $post->post_title;
	$content = $post->post_content;

	// TODO: Send post to a Mailjet Mailing list

	$body = [
		'Messages' => [
			[
				'From' => [
					'Email' => "carlos@dunkelheitdraper.com",
					'Name' => "Me"
				],
				'To' => [
					[
						'Email' => "carjimfa@gmail.com",
						'Name' => "You"
					]
				],
				'Subject' => "$subject",
				'HTMLPart' => "$content"
			]
		]
	];

	$response = $mj->post(Resources::$Email, ['body' => $body]);
}