<?php
namespace BengalStudio;

final class WriteBuddy {
	private static $instance;
	private $ajax;
	private $settings;

	// Private constructor to prevent direct instantiation
	private function __construct() {
		// Initialize your class here
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/functions.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-database.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-settings.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-openai.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-thread.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-message.php';
		require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . '/includes/class-ajax.php';

		$database = new Database();
		register_activation_hook( WRITEBUDDY_AI_FILE, array( $database, 'create_chat_tables' ) );

		// Initialize Ajax class
		$this->ajax = new Ajax();

		if ( is_admin() ) {
			$this->settings = new Settings();

			// Enqueue JavaScript file
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}
	}

	// Method to get the singleton instance
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Enqueue JavaScript file
	public function enqueue_block_editor_assets() {
		$asset_file   = include( plugin_dir_path( WRITEBUDDY_AI_FILE ) . 'build/index.asset.php' );
		$current_user = wp_get_current_user();

		// Enqueue your plugin's JavaScript file
		wp_enqueue_script(
			'writebuddy',
			// Make sure to include React and React DOM as dependencies
			plugins_url( 'build/index.js', WRITEBUDDY_AI_FILE ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		// Pass Ajax URL to JavaScript file
		wp_localize_script(
			'writebuddy',
			'writebuddy',
			array(
				'i18n'         => [
					'ai'               => __( 'WriteBuddy AI', 'writebuddyai' ),
					'chat_now'         => __( 'Your AI Writing Assistant: Chat Now!', 'writebuddyai' ),
					'write_a_reply'    => __( 'Write a reply...', 'writebuddyai' ),
					'scroll_to_bottom' => __( 'Scroll to bottom', 'writebuddyai' ),
				],
				'admin_url'    => admin_url( 'admin-ajax.php' ),
				'thread_id'    => get_current_user_thread_id(),
				'current_time' => current_time( 'mysql' ),
				'security'     => wp_create_nonce( 'writebuddy_security' ),
				'current_user' => [
					'display_name' => $current_user->display_name,
					'avatar_url'   => get_avatar_url( $current_user->ID ),
				],
			)
		);

		wp_enqueue_style(
			'writebuddy',
			plugins_url( 'build/index.css', WRITEBUDDY_AI_FILE ),
			array(),
			$asset_file['version'],
		);
	}

	// Private clone method to prevent cloning of the instance
	private function __clone() {}

	// Private unserialize method to prevent unserialization of the instance
	private function __wakeup() {}
}
