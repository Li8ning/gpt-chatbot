<?php
/**
 * File to render frontend chatbot window.
 *
 * @package gpt-chatbot
 */

?>

<div class="container">
	<div class="chat-container" id="chat-container"></div>
	<div class="input-container">
		<input type="text" id="user-input" placeholder="Type a message..." />
		<button onclick="sendMessage()" id="send-button">Send</button>
	</div>
	<p class="please-wait" id="please-wait-message" style="display: none;">Please wait...</p>
	<a href="#" id="download-link" download style="display: none;" class="download-link">Download Answer</a>
</div>
