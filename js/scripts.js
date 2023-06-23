const chatContainer = document.getElementById('gpt-chat-container');
const userInput = document.getElementById('gpt-user-input');
const sendButton = document.getElementById('gpt-send-button');
const pleaseWaitMessage = document.getElementById('gpt-please-wait-message');
const downloadLink = document.getElementById('gpt-download-link');

// Event listener for pressing Enter key
userInput.onkeydown = function (event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        sendButton.click();
    }
}

async function sendMessage() {
    const message = userInput.value;
    if (message.trim() === '') return;

    appendMessage(message, 'gpt-user-message');
    userInput.value = '';

    pleaseWaitMessage.style.display = 'block';

    // Send user message to the server-side API endpoint
    jQuery.ajax({
        type: 'POST',
        url: gpt_chatbot_ajax_object.ajax_url,
        data: {
            action: 'gpt_chatbot_send_message',
            message: message
        },
        success: function (response) {
            // Display AI response in chat window
            pleaseWaitMessage.style.display = 'none';
            displayBotReply(response.data);
            downloadLink.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(response.data);
            downloadLink.style.display = 'block';
        },
        error: function () {
            // Handle error response from the API
            console.log('An error occurred while communicating with the AI.', 'error');
        }
    });
}

function appendMessage(message, className) {
    const messageElement = document.createElement('div');
    messageElement.classList.add('gpt-message');
    messageElement.classList.add(className);
    messageElement.textContent = message;
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function displayBotReply(botReply) {
    appendMessage(botReply, 'gpt-bot-message');
}
