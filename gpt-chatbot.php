<?php
/**
 * Plugin Name: GPT Chatbot
 * Description: Implements a secure chatbot functionality using OpenAI API.
 * Version: 1.0.0
 * Author: Dharmrajsinh Jadeja
 * Author URI: https://anandavak.com
 * Text Domain: gpt-chatbot
 *
 * @package gpt-chatbot
 */

// Prevent direct access to the plugin file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue stylesheets and scripts.
 *
 * @return void
 */
function gpt_chatbot_enqueue_scripts() {
	wp_enqueue_style( 'gpt-chatbot-style', plugins_url( 'css/style.css', __FILE__ ), array(), '1.0.0' );
	wp_enqueue_script( 'gpt-chatbot-script', plugins_url( 'js/scripts.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );

	// Localize the script with AJAX URL and any other variables you want to pass.
	wp_localize_script(
		'gpt-chatbot-script',
		'gpt_chatbot_ajax_object',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gpt_chatbot_enqueue_scripts' );

// Include the AJAX handler file.
require_once plugin_dir_path( __FILE__ ) . 'gpt-ajax-handler.php';

/**
 * Register shortcode for chatbot display.
 *
 * @return html
 */
function gpt_chatbot_shortcode() {
	ob_start();
	include plugin_dir_path( __FILE__ ) . 'templates/render-gpt-chatbot.php';
	return ob_get_clean();
}
add_shortcode( 'gpt_chatbot', 'gpt_chatbot_shortcode' );


add_action( 'admin_menu', 'gpt_chatbot_plugin_register_menu' );

/**
 * Register GPT Chatbot plugin settings page
 *
 * @return void
 */
function gpt_chatbot_plugin_register_menu() {
	add_options_page(
		'GPT Chatbot Plugin Settings',    // Page title.
		'GPT Chatbot Settings',           // Menu title.
		'manage_options',             // Capability required to access the menu.
		'gpt-chatbot-settings',           // Menu slug.
		'gpt_chatbot_plugin_settings'  // Callback function to render the settings page.
	);
}

/**
 * Render Plugin settings page.
 *
 * @return html
 */
function gpt_chatbot_plugin_settings() {
	// Load WordPress admin files.
	require_once ABSPATH . 'wp-load.php';
	require_once ABSPATH . 'wp-admin/includes/admin.php';

	// Check if the user has the required capability to access the settings page.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// Save the secret key if the form is submitted.
	if ( isset( $_POST['gpt_chatbot_plugin_nonce'] ) && wp_verify_nonce( $_POST['gpt_chatbot_plugin_nonce'], 'gpt_chatbot_plugin_settings' ) && isset( $_POST['gpt_chatbot_secret_key'] ) ) {
		$secret_key = sanitize_text_field( wp_unslash( $_POST['gpt_chatbot_secret_key'] ) );
		update_option( 'gpt_chatbot_secret_key', $secret_key );
		echo '<div class="notice notice-success"><p>Secret key saved.</p></div>';
	}

	// Retrieve the secret key.
	$secret_key = get_option( 'gpt_chatbot_secret_key', '' );

	// Render the settings page.
	?>
<div class="wrap">
	<h1>Chatbot Plugin Settings</h1>

	<form method="post">
		<label for="gpt_chatbot_secret_key">Secret Key:</label>
		<input type="text" id="gpt_chatbot_secret_key" name="gpt_chatbot_secret_key" value="<?php echo esc_attr( $secret_key ); ?>">
		<?php
			wp_nonce_field( 'gpt_chatbot_plugin_settings', 'gpt_chatbot_plugin_nonce' );
		?>

		<p class="submit">
			<input type="submit" class="button-primary" value="Save">
		</p>
	</form>
</div>
	<?php
}
