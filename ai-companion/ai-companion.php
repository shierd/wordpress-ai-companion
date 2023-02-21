<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.shierd.com
 * @since             1.0.0
 * @package           Ai_Companion
 *
 * @wordpress-plugin
 * Plugin Name:       AI Companion
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       With AI Companion plugin, you can easily add an AI-powered chat interface to your website, allowing visitors to have a more interactive and engaging experience.
 * Version:           1.0.0
 * Author:            shier
 * Author URI:        https://www.shierd.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Ai_Companion_VERSION', '1.0.0' );

/**
 * 插件设置key
 */
define( 'Ai_Companion_OPTION_KEY', 'ai_companion_setting' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ai-companion-activator.php
 */
function activate_ai_companion() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ai-companion-activator.php';
	Ai_Companion_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ai-companion-deactivator.php
 */
function deactivate_ai_companion() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ai-companion-deactivator.php';
	Ai_Companion_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ai_companion' );
register_deactivation_hook( __FILE__, 'deactivate_ai_companion' );

require plugin_dir_path( __FILE__ ) . 'wp-openai-client/__autoload.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ai-companion.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ai_companion() {

	$plugin = new Ai_Companion();
	$plugin->run();

}
run_ai_companion();
