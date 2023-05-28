<?php
namespace BengalStudio;

class Database {
	public function create_chat_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Table for chat messages
		$chat_messages_table_name = $wpdb->prefix . 'writebuddy_chat_messages';
		$chat_messages_sql        = "CREATE TABLE IF NOT EXISTS $chat_messages_table_name (
          id INT(11) NOT NULL AUTO_INCREMENT,
          sender_id INT(11) NOT NULL,
          thread_id INT(11) NOT NULL,
          message TEXT NOT NULL,
          created_at DATETIME NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $chat_messages_sql );

		// Table for chat recipients
		$chat_recipients_table_name = $wpdb->prefix . 'writebuddy_chat_recipients';
		$chat_recipients_sql        = "CREATE TABLE IF NOT EXISTS $chat_recipients_table_name (
          id INT(11) NOT NULL AUTO_INCREMENT,
          thread_id INT(11) NOT NULL,
          recipient_id INT(11) NOT NULL,
          first_message_sent TINYINT(1) NOT NULL DEFAULT 0,
          PRIMARY KEY (id)
        ) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $chat_recipients_sql );
	}
}
