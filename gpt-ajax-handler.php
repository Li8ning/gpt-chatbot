<?php
/**
 * Ajax handler for gpt-chatbot plugin.
 *
 * @package gtp-chatbot
 */

// Prevent direct access to the AJAX handler.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// AJAX action for sending a message.
add_action( 'wp_ajax_gpt_chatbot_send_message', 'gpt_chatbot_send_message' );
add_action( 'wp_ajax_nopriv_gpt_chatbot_send_message', 'gpt_chatbot_send_message' );

/**
 * Function to send a message to the OpenAI API.
 *
 * @return void
 */
function gpt_chatbot_send_message() {
	// Check if the required data is received.
	if ( ! isset( $_POST['message'] ) ) {
		wp_send_json_error( 'Invalid request.' );
	}

	// Retrieve the gpt chatbot completions configurations.
	$gpt_chatbot_completions_config = get_option( 'gpt_chatbot_completions_config', '' );

	$gpt_chatbot_completions_config_array = json_decode( $gpt_chatbot_completions_config );
	$secret_key                           = isset( $gpt_chatbot_completions_config_array->secret_key ) ? $gpt_chatbot_completions_config_array->secret_key : '';
	$gpt_model                            = ! empty( $gpt_chatbot_completions_config_array->model ) ? $gpt_chatbot_completions_config_array->model : 'gpt-3.5-turbo';
	$gpt_temperature                      = ! empty( $gpt_chatbot_completions_config_array->temperature ) ? $gpt_chatbot_completions_config_array->temperature : 0.2;
	$gpt_max_tokens                       = ! empty( $gpt_chatbot_completions_config_array->max_tokens ) ? $gpt_chatbot_completions_config_array->max_tokens : 250;

	// Check if the required data is received.
	if ( empty( $secret_key ) ) {
		wp_send_json_error( 'API Key not set' );
	}

	// Get the user message from the request.
	$message = sanitize_text_field( $_POST['message'] );

	// Define the API endpoint URL.
	$api_endpoint = 'https://api.openai.com/v1/chat/completions';

	// Set the request headers.
	$headers = array(
		'Content-Type'  => 'application/json',
		'Authorization' => 'Bearer ' . $secret_key,
	);

	// Set the request data.
	$data = array(
		'model'       => $gpt_model,
		'messages'    => array(
			array(
				'role'    => 'user',
				'content' => $message,
			),
		),
		'max_tokens'  => $gpt_max_tokens,
		'temperature' => $gpt_temperature,
	);

	// Send the request to the OpenAI API.
	$response = wp_remote_post(
		$api_endpoint,
		array(
			'headers' => $headers,
			'body'    => wp_json_encode( $data ),
		)
	);

	// Check for a successful response.
	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'An error occurred while communicating with the AI.' );
	}

	// Get the AI response from the API response body.
	$ai_response = json_decode( wp_remote_retrieve_body( $response ), true );

	// Extract the generated text from the AI response.
	$generated_text = $ai_response['choices'][0]['message']['content'] ?? '';

	// Send the AI response back to the client.
	wp_send_json_success( $generated_text );
}

