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

	// Set up the OpenAI API credentials.
	// Retrieve the secret key.
	$openai_api_key = get_option( 'gpt_chatbot_secret_key', '' );

	// Check if the required data is received.
	if ( empty( $openai_api_key ) ) {
		wp_send_json_error( 'API Key not set' );
	}

	// Get the user message from the request.
	$message = sanitize_text_field( $_POST['message'] );

	// Define the API endpoint URL.
	$api_endpoint = 'https://api.openai.com/v1/chat/completions';

	// Set the request headers.
	$headers = array(
		'Content-Type'  => 'application/json',
		'Authorization' => 'Bearer ' . $openai_api_key,
	);

	// Set the request data.
	$data = array(
		'model'      => 'gpt-3.5-turbo',
		'messages'   => array(
			array(
				'role'    => 'system',
				'content' => $message,
			),
		),
		'max_tokens' => 250,
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

