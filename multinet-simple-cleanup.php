<?php

/**
 * Plugin Name: Simple Cleanup
 * Description: Provides options to clean up your WordPress backend and the generated HTML source of your website.
 * Version: 0.3
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: multinet gmbh
 * Author URI: https://multinet.ch
 * License: GNU GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: multinet-simple-cleanup
 */

defined( 'ABSPATH' ) or die;

require_once __DIR__ . '/src/SimpleCleanup.php';
require_once __DIR__ . '/src/OptionsPageHandler.php';
require_once __DIR__ . '/src/ActionHandler.php';

$simple_cleanup_instance = new Multinet\SimpleCleanup\SimpleCleanup();
$simple_cleanup_instance->boot();