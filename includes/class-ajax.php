<?php
namespace BengalStudio;

class Ajax {
	// Constructor
	public function __construct() {
		// Register AJAX hook
		add_action( 'wp_ajax_writebuddy_ai_get_thread_messages', array( $this, 'get_thread_messages' ) );
		add_action( 'wp_ajax_writebuddy_ai_save_message', array( $this, 'save_message' ) );
	}

	// AJAX callback function
	public function get_thread_messages() {
		// Get thread ID from AJAX request
		$thread_id = isset( $_GET['thread_id'] ) ? absint( $_GET['thread_id'] ) : get_current_user_thread_id();

		// Get additional arguments from AJAX request
		$page       = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
		$per_page   = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 10;
		$order      = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : null;
		$end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : null;

		// Get thread messages by thread ID
		$thread_messages = Thread::get_thread_messages(
			$thread_id,
			array(
				'page'       => $page,
				'per_page'   => $per_page,
				'order'      => $order,
				'start_date' => $start_date,
				'end_date'   => $end_date,
			)
		);

		// Send response
		wp_send_json_success( $thread_messages ); // Use wp_send_json_success() for successful response
	}

	public function save_message() {
		// Get the raw request body as a string
		$request_body = file_get_contents( 'php://input' );

		// Parse the JSON string into an associative array
		$data = json_decode( $request_body, true );

		$thread_id      = isset( $data['thread_id'] ) ? intval( $data['thread_id'] ) : get_current_user_thread_id();
		$sender_id      = isset( $data['sender_id'] ) ? intval( $data['sender_id'] ) : get_current_user_id();
		$messages       = isset( $data['messages'] ) ? $data['messages'] : '';

		// Validate required parameters
		if ( empty( $thread_id ) || empty( $sender_id ) || empty( $messages ) ) {
			wp_send_json_error( array( 'content' => __( 'Required parameters are missing.', 'writebuddyai' ) ) );
		}

		// Check the nonce
		if ( ! wp_verify_nonce( $data['writebuddy_security'], 'writebuddy_security' ) ) {
			wp_send_json_error( array( 'content' => __( 'Nonce verification failed. Please refresh the page and try again.', 'writebuddyai' ) ) );
		}

		$option_conversation = get_option( 'writebuddy_option_conversation' );
		$enable_logging = isset( $option_conversation['enable_logging'] ) ? $option_conversation['enable_logging'] : 'on';

		if ( 'on' === $enable_logging ) {
			// Insert message into database
			$last_message = end( $messages );
			$message      = new Message( $sender_id, $last_message['message'], $thread_id );
			$message->send();

			reset( $messages );
		}

		$ai_reply = $this->generate_ai_reply( $thread_id, $messages );

		wp_send_json_success( $ai_reply );
	}

	public function generate_ai_reply( $thread_id = 0, $messages = [] ) {
		$option_openai       = get_option( 'writebuddy_option_openai' );
		$option_conversation = get_option( 'writebuddy_option_conversation' );

		$api_key           = isset( $option_openai['api_key'] ) ? $option_openai['api_key'] : false;
		$model             = isset( $option_openai['model'] ) ? $option_openai['model'] : 'gpt-4';
		$temperature       = isset( $option_openai['temperature'] ) ? $option_openai['temperature'] : 1;
		$max_tokens        = isset( $option_openai['max_tokens'] ) ? $option_openai['max_tokens'] : 0;
		$presence_penalty  = isset( $option_openai['presence_penalty'] ) ? $option_openai['presence_penalty'] : 0;
		$frequency_penalty = isset( $option_openai['frequency_penalty'] ) ? $option_openai['frequency_penalty'] : 0;

		$max_history    = isset( $option_conversation['max_history'] ) ? $option_conversation['max_history'] : 10;
		$enable_logging = isset( $option_conversation['enable_logging'] ) ? $option_conversation['enable_logging'] : 'on';

		$openai = new OpenAI( $api_key );

		$_messages = [];

		if ( 'on' === $enable_logging ) {
			$thread_messages = Thread::get_thread_messages(
				$thread_id,
				array(
					'per_page' => $max_history,
				)
			);

			foreach ( $thread_messages['messages'] as $message ) {
				$_messages[] = [
					'role'    => '-1' === $message->sender_id ? 'assistant' : 'user',
					'content' => $message->message,
				];
			}
		} else {
			$allowed_messages = array_slice( $messages, 0, $max_history );
			foreach ( $allowed_messages as $message ) {
				$_messages[] = [
					'role'    => '-1' === $message->sender_id ? 'assistant' : 'user',
					'content' => $message['message'],
				];
			}
		}

		$data = array(
			'model'             => $model,
			'messages'          => $_messages,
			'temperature'       => $temperature,
			'presence_penalty'  => $presence_penalty,
			'frequency_penalty' => $frequency_penalty,
		);

		if ( $max_tokens > 0 ) {
			$data['max_tokens'] = $max_tokens;
		}

		$result = $openai->send_request( 'chat/completions', $data );

		if ( is_wp_error( $result ) ) {
			return array_merge(
				$data,
				array(
					'max_history' => $max_history,
					'content'     => $result->get_error_message(),
				)
			);
		}

		$content = $result->choices[0]->message->content;

		if ( 'on' === $enable_logging ) {
			$message = new Message( -1, $content, $thread_id );
			$message->send();
		}

		return array_merge(
			$data,
			array(
				'max_history' => $max_history,
				'content'     => $content,
			)
		);
	}
}

