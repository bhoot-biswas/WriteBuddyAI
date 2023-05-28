<?php
namespace BengalStudio;

function get_writebuddy_icon() {
	return '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
		<path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
		<path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
	</svg>';
}

function get_setting_tabs() {
	return array(
		'openai'       => array(
			'title'       => __( 'OpenAI Settings', 'writebuddyai' ),
			'description' => '',
			'fields'      => array(
				'api_key'           => array(
					'title'             => __( 'API Key', 'writebuddyai' ),
					'description'       => __( 'Enter your ChatGPT API key.', 'writebuddyai' ),
					'type'              => 'text',
					'default'           => '',
					'required'          => true, // This field is required
					'sanitize_callback' => 'sanitize_text_field',
				),
				'model'             => array(
					'title'             => __( 'Default Model', 'writebuddyai' ),
					'description'       => __( 'Select the default model to use for ChatGPT.', 'writebuddyai' ),
					'type'              => 'select',
					'default'           => 'gpt-3.5-turbo',
					'options'           => get_chatgpt_model_options(),
					'required'          => true, // This field is required,
					'sanitize_callback' => 'sanitize_text_field',
				),
				'temperature'       => array(
					'title'             => __( 'Temperature', 'writebuddyai' ),
					'description'       => __( 'What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'writebuddyai' ),
					'type'              => 'number',
					'default'           => '1.0',
					'required'          => false, // This field is required
					'sanitize_callback' => 'intval',
				),
				'max_tokens'        => array(
					'title'             => __( 'Max Tokens', 'writebuddyai' ),
					'description'       => __( 'The maximum number of tokens to generate in the chat completion.', 'writebuddyai' ),
					'type'              => 'number',
					'default'           => '',
					'required'          => false, // This field is required
					'sanitize_callback' => 'intval',
				),
				'frequency_penalty' => array(
					'title'             => __( 'Frequency Penalty', 'writebuddyai' ),
					'description'       => __( 'Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model\'s likelihood to talk about new topics.', 'writebuddyai' ),
					'type'              => 'number',
					'default'           => '0.0',
					'required'          => false, // This field is required
					'sanitize_callback' => 'intval',
				),
				'presence_penalty'  => array(
					'title'             => __( 'Presence Penalty', 'writebuddyai' ),
					'description'       => __( 'Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model\'s likelihood to repeat the same line verbatim.', 'writebuddyai' ),
					'type'              => 'number',
					'default'           => '0.0',
					'required'          => false, // This field is required
					'sanitize_callback' => 'intval',
				),
			),
		),
		'conversation' => array(
			'title'       => __( 'Conversation Settings', 'writebuddyai' ),
			'description' => '',
			'fields'      => array(
				'max_history'    => array(
					'title'             => __( 'Max History', 'writebuddyai' ),
					'description'       => __( 'Enter the maximum number of previous messages to use for ChatGPT input.', 'writebuddyai' ),
					'type'              => 'number',
					'default'           => '10',
					'required'          => false, // This field is required
					'sanitize_callback' => 'intval',
				),
				'enable_logging' => array(
					'title'             => __( 'Enable Logging', 'writebuddyai' ),
					'description'       => __( 'Enable or disable logging of ChatGPT conversations.', 'writebuddyai' ),
					'type'              => 'checkbox',
					'default'           => 'on',
					'required'          => false, // This field is not required
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		),
	);
}

function get_chatgpt_model_options() {
	return array(
		'gpt-4'              => __( 'gpt-4', 'writebuddyai' ),
		'gpt-4-0314'         => __( 'gpt-4-0314', 'writebuddyai' ),
		'gpt-4-32k'          => __( 'gpt-4-32k', 'writebuddyai' ),
		'gpt-4-32k-0314'     => __( 'gpt-4-32k-0314', 'writebuddyai' ),
		'gpt-3.5-turbo'      => __( 'gpt-3.5-turbo', 'writebuddyai' ),
		'gpt-3.5-turbo-0301' => __( 'gpt-3.5-turbo-0301', 'writebuddyai' ),
	);
}

function get_current_user_thread_id() {
	// Load WordPress database connector
	global $wpdb;

	// Get the user ID
	$user_id = get_current_user_id();

	$table_name_chat_recipients = $wpdb->prefix . 'writebuddy_chat_recipients';

	// delete_option( 'writebuddy_option_openai' );
	// delete_option( 'writebuddy_option_conversation' );

	// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}writebuddy_chat_messages" );
	// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}writebuddy_chat_recipients" );

	// Set the table name and select query
	$chat_recipients_query = $wpdb->prepare( "SELECT * FROM $table_name_chat_recipients WHERE recipient_id = %d LIMIT 1", $user_id );

	// Run the query and get the first result
	$result = $wpdb->get_row( $chat_recipients_query );

	// Check if a record was returned
	if ( $result ) {
		return $result->thread_id;
	}

	// $wpdb->delete( $table_name_writebuddy_chat_messages, array( 'sender_id' => "-1" ) );
	// $wpdb->delete( $table_name_chat_recipients, array( 'recipient_id' => "-1" ) );

	// No records exist for this user
	$message = new Message( -1, 'Hello! How can I assist you today?', null, array( $user_id ) );
	return $message->send();
}
