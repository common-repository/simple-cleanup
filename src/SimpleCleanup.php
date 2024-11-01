<?php

namespace Multinet\SimpleCleanup;

defined( 'ABSPATH' ) or die;

class SimpleCleanup {

	/**
	 * This is the name of the option WordPress will create in the wp_options table.
	 * We store our options as array so only one option is needed.
	 */
	const option_name = 'multinet-simple-cleanup';

	/**
	 * Holds/caches the current options from the DB for quick and easy access
	 * @var
	 */
	public static $options;

	/**
	 * Does everything: Loads the textdomain, creates the settings page and executes the hooks.
	 */
	public function boot() {
		// Load text domain
		load_plugin_textdomain( 'multinet-simple-cleanup', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages' );

		// Create options page and add to "Settings" menu
		OptionsPageHandler::setupOptionsPage();

		// Add hooks based on options
		ActionHandler::addActions();
	}

	/**
	 * Get an option. Returns the full array when $name is null.
	 *
	 * @param null $name the name of our option to get from the array or null to return the full array
	 *
	 * @return mixed|void|null
	 */
	public static function getOption( $name = null ) {
		if ( is_null( self::$options ) ) {
			self::$options = get_option( self::option_name );
		}

		return is_null( $name ) ? self::$options : ( self::$options[ $name ] ?? null );
	}
}