<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'writebuddy_option_openai' );
delete_option( 'writebuddy_option_conversation' );

// drop a custom database table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}writebuddy_chat_messages" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}writebuddy_chat_recipients" );
