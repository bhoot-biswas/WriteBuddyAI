<?php
namespace BengalStudio;

class Thread {
	private $thread_id;
	private $new_thread;

	public function __construct( $thread_id = null ) {
		if ( $thread_id ) {
			$this->thread_id  = $thread_id;
			$this->new_thread = false; // Thread is not new
		} else {
			$this->new_thread = true; // Thread is new
		}
	}

	// Add a chat message to the thread
	public function add_chat_message( $sender_id, $message, $recipient_ids = array() ) {
		global $wpdb;

		// If thread is new, create a new thread
		if ( $this->new_thread ) {
			$this->thread_id  = $wpdb->get_var(
				"SELECT MAX(thread_id) FROM {$wpdb->prefix}writebuddy_chat_messages"
			);
			$this->thread_id  = $this->thread_id ? $this->thread_id + 1 : 1;
			$this->new_thread = false; // Update $this->new_thread to false, as thread is not new

			// Insert sender as a recipient into writebuddy_chat_recipients table
			$this->insert_chat_recipient( $this->thread_id, $sender_id, true );

			// Insert chat recipients into writebuddy_chat_recipients table
			foreach ( $recipient_ids as $recipient_id ) {
				$this->insert_chat_recipient( $this->thread_id, $recipient_id );
			}
		}

		// Insert chat message into writebuddy_chat_messages table
		$wpdb->insert(
			"{$wpdb->prefix}writebuddy_chat_messages",
			array(
				'sender_id'  => $sender_id,
				'thread_id'  => $this->thread_id,
				'message'    => $message,
				'created_at' => current_time( 'mysql' ),
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
			)
		);

		return $this->thread_id;
	}

	// Insert a chat recipient into writebuddy_chat_recipients table
	private function insert_chat_recipient( $thread_id, $recipient_id, $first_message_sent = false ) {
		global $wpdb;

		$wpdb->insert(
			"{$wpdb->prefix}writebuddy_chat_recipients",
			array(
				'thread_id'          => $thread_id,
				'recipient_id'       => $recipient_id,
				'first_message_sent' => $first_message_sent ? 1 : 0,
			),
			array(
				'%d',
				'%d',
				'%d',
			)
		);
	}

	public static function get_thread_messages( $thread_id = 0, $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'writebuddy_chat_messages';

		$default_args = array(
			'page'     => 1,
			'per_page' => 10,
			'order'    => 'DESC',
		);

		$args = wp_parse_args( $args, $default_args );

		$start_date = isset( $args['start_date'] ) ? $args['start_date'] : null;
		$end_date   = isset( $args['end_date'] ) ? $args['end_date'] : null;

		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $table_name WHERE thread_id = %d";

		$args_array = array( $thread_id );

		if ( $start_date || $end_date ) {
			$query .= ' AND';

			if ( $start_date ) {
				$query       .= ' created_at >= %s';
				$args_array[] = $start_date;
			}

			if ( $end_date ) {
				$query       .= ' created_at <= %s';
				$args_array[] = $end_date;
			}
		}

		$query .= " ORDER BY id {$args['order']} LIMIT %d, %d";

		$args_array[] = absint( ( $args['page'] - 1 ) * $args['per_page'] );
		$args_array[] = $args['per_page'];

		$query = $wpdb->prepare( $query, $args_array );

		$messages       = $wpdb->get_results( $query );
		$found_messages = (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
		$max_num_pages  = ceil( $found_messages / $args['per_page'] );

		// If the order is DESC, we need to reverse the message array
		if ( $args['order'] === 'DESC' ) {
			$messages = array_reverse( $messages );
		}

		return [
			'messages'       => $messages,
			'found_messages' => $found_messages,
			'max_num_pages'  => $max_num_pages,
			'previous_page'  => $args['page'] == $max_num_pages ? false : $args['page'] + 1,
		];
	}
}




