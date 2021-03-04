<?php
/**
 * Plugin Name: Juice Blocks
 * Plugin URI: https://companyjuice.com/
 * Description: A Gutenberg blocks plugin with Post Type Query Builder.
 * Author: Marty McGee
 * Author URI: https://companyjuice.com/
 * Version: 1.0.1
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package wcn
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
