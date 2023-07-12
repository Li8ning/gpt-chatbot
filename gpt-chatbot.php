<?php
/**
 * Plugin Name: GPT Chatbot
 * Description: Implements a secure chatbot functionality using OpenAI API.
 * Version: 1.0.0
 * Author: Dharmrajsinh Jadeja
 * Author URI: https://anandavak.com
 * Plugin URI: https://github.com/Li8ning/gpt-chatbot
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
 * @return void
 */
function gpt_chatbot_plugin_settings() {

	// Check if the user has the required capability to access the settings page.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// Save the gpt chatbot configurations if the form is submitted.
	if ( isset( $_POST['gpt_chatbot_plugin_nonce'] ) && wp_verify_nonce( $_POST['gpt_chatbot_plugin_nonce'], 'gpt_chatbot_plugin_settings' ) ) {
		$secret_key                     = isset( $_POST['gpt_chatbot_secret_key'] ) ? sanitize_text_field( wp_unslash( $_POST['gpt_chatbot_secret_key'] ) ) : '';
		$gpt_model                      = isset( $_POST['gpt_chatbot_model_name'] ) ? sanitize_text_field( wp_unslash( $_POST['gpt_chatbot_model_name'] ) ) : '';
		$gpt_temperature                = isset( $_POST['gpt_chatbot_temperature'] ) && '' !== $_POST['gpt_chatbot_temperature'] ? floatval( $_POST['gpt_chatbot_temperature'] ) : '';
		$gpt_max_tokens                 = isset( $_POST['gpt_chatbot_max_tokens'] ) && '' !== $_POST['gpt_chatbot_max_tokens'] ? intval( $_POST['gpt_chatbot_max_tokens'] ) : '';
		$gpt_chatbot_completions_config = array(
			'secret_key'  => $secret_key,
			'model'       => $gpt_model,
			'temperature' => $gpt_temperature,
			'max_tokens'  => $gpt_max_tokens,
		);

		// Convert the array to JSON.
		$gpt_chatbot_completions_config_json = wp_json_encode( $gpt_chatbot_completions_config );
		update_option( 'gpt_chatbot_completions_config', $gpt_chatbot_completions_config_json );
		echo '<div class="notice notice-success"><p>GPT Chatbot Configurations Saved.</p></div>';
	}

	// Retrieve the gpt chatbot completions configurations.
	$gpt_chatbot_completions_config = get_option( 'gpt_chatbot_completions_config', '' );

	$gpt_chatbot_completions_config_array = json_decode( $gpt_chatbot_completions_config );
	$secret_key                           = isset( $gpt_chatbot_completions_config_array->secret_key ) ? $gpt_chatbot_completions_config_array->secret_key : '';
	$gpt_model                            = isset( $gpt_chatbot_completions_config_array->model ) ? $gpt_chatbot_completions_config_array->model : '';
	$gpt_temperature                      = isset( $gpt_chatbot_completions_config_array->temperature ) ? $gpt_chatbot_completions_config_array->temperature : '';
	$gpt_max_tokens                       = isset( $gpt_chatbot_completions_config_array->max_tokens ) ? $gpt_chatbot_completions_config_array->max_tokens : '';

	// Render the settings page.
	$html  = '<div class="wrap">
			<h1>Chatbot Plugin Settings</h1>
			<form method="post">
				<label for="gpt_chatbot_secret_key">' . esc_html__( 'Secret Key:', 'gpt-chatbot' ) . '</label>
				<input type="text" id="gpt_chatbot_secret_key" name="gpt_chatbot_secret_key" required value="' . esc_attr( ! empty( $secret_key ) ? $secret_key : '' ) . '">
				<label for="gpt_chatbot_model_name">' . esc_html__( 'Model Name:', 'gpt-chatbot' ) . '</label>
				<input type="text" id="gpt_chatbot_model_name" name="gpt_chatbot_model_name" required value="' . esc_attr( ! empty( $gpt_model ) ? $gpt_model : '' ) . '">
				<label for="gpt_chatbot_temperature">' . esc_html__( 'Temperature:', 'gpt-chatbot' ) . '</label>
				<input type="number" step="0.01" min="0" max="2" id="gpt_chatbot_temperature" name="gpt_chatbot_temperature" value="' . esc_attr( ! empty( $gpt_temperature ) ? $gpt_temperature : '' ) . '">
				<label for="gpt_chatbot_max_tokens">' . esc_html__( 'Max Tokens:', 'gpt-chatbot' ) . '</label>
				<input type="number" id="gpt_chatbot_max_tokens" name="gpt_chatbot_max_tokens" value="' . esc_attr( ! empty( $gpt_max_tokens ) ? $gpt_max_tokens : '' ) . '">';
	$html .= wp_nonce_field( 'gpt_chatbot_plugin_settings', 'gpt_chatbot_plugin_nonce' );
	$html .= '<p class="submit">
			<input type="submit" class="button-primary" value="Save">
				</p>
			</form>
			</div>';

	// Restricting and filtering html tags and its attributes as passed in wp_kses.
	echo wp_kses(
		$html,
		array(
			'div'   => array(
				'class' => array(),
			),
			'h1'    => array(),
			'form'  => array(
				'method' => array(),
			),
			'label' => array(
				'for' => array(),
			),
			'input' => array(
				'type'     => array(),
				'id'       => array(),
				'class'    => array(),
				'name'     => array(),
				'value'    => array(),
				'step'     => array(),
				'min'      => array(),
				'max'      => array(),
				'required' => array(),
			),
			'p'     => array(
				'class' => array(),
			),
		)
	);
}
