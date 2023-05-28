<?php
/**
 * Plugin Name:     WriteBuddy AI – ChatGPT-Powered Writing Assistant
 * Plugin URI:      https://writebuddyai.com/
 * Description:     WriteBuddy AI is a WordPress plugin that generates high-quality content and images using ChatGPT and OpenAI technology. It's a chat-based writing assistant that saves you time and boosts your productivity.
 * Author:          Bengal Studio
 * Author URI:      https://bengal-studio.com/
 * Text Domain:     writebuddy
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WriteBuddy
 */

namespace BengalStudio;

define('WRITEBUDDY_AI_FILE', __FILE__);

require_once plugin_dir_path( WRITEBUDDY_AI_FILE ) . 'includes/class-writebuddy.php';

// Function to initialize WriteBuddy and return the singleton instance
function writebuddy() {
    // Get the singleton instance of WriteBuddy
    $instance = WriteBuddy::get_instance();

    // Return the singleton instance
    return $instance;
}

writebuddy();