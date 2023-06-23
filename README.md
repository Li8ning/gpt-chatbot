# AI Chat Plugin for WordPress

The GPT Chat Plugin for WordPress allows users to interact with OpenAI's generative chat models. It provides a chat window that can be easily integrated into WordPress websites using a shortcode. The plugin utilizes the OpenAI API to generate responses based on user input.

## Features

- Easy integration using a shortcode
- Support for entering API secret key, model name, temperature, and max tokens
- Secure implementation to prioritize user privacy and data protection

## Installation

1. Download the plugin ZIP file from the [releases page](https://github.com/Li8ning/gpt-chatbot).
2. Log in to your WordPress admin dashboard.
3. Go to "Plugins" > "Add New" and click on the "Upload Plugin" button.
4. Choose the downloaded ZIP file and click "Install Now".
5. After installation, activate the plugin.

## Usage

To display the chat window, use the following shortcode:
[gpt_chatbot]

You can place this shortcode on any page or post where you want the chat window to appear.

## Configuration

1. In the WordPress admin dashboard, go to "Settings" > "GPT Chatbot Settings".
2. Enter your OpenAI API secret key, model name, temperature, and max tokens.
3. Click "Save" to save the configuration.

## Security Considerations

- The plugin follows WordPress security best practices.
- User input is properly sanitized and validated to prevent potential vulnerabilities.
- The API secret key is securely stored in the WordPress database and protected using appropriate access controls.
- The plugin uses secure HTTPS connections to communicate with the OpenAI API.
