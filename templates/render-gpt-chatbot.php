<?php
/**
 * File to render frontend chatbot window.
 *
 * @package gpt-chatbot
 */

?>

<div class="container">
	<div class="gpt-chat-container" id="gpt-chat-container"></div>
	<div class="gpt-input-container">
		<input type="text" id="gpt-user-input" placeholder="Type a message..." />
		<button onclick="sendMessage()" id="gpt-send-button">Send</button>
	</div>
	<p class="gpt-please-wait" id="gpt-please-wait-message" style="display: none;">Please wait...</p>
	<a href="#" id="gpt-download-link" download style="display: none;" class="gpt-download-link">Download Answer</a>
</div>
