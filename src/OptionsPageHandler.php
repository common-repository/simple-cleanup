<?php

namespace Multinet\SimpleCleanup;

defined( 'ABSPATH' ) or die;

class OptionsPageHandler {

	/**
	 * Returns/echoes a checkbox using the name, description etc. from the given args.
	 *
	 * @param array $args Arguments passed from WordPress. Properties can be "name", "label" and "description"
	 * @param bool $output
	 *
	 * @return string|void
	 */
	public static function checkbox( $args, $output = true ) {
		$name  = $args['name'];
		$label = $args['label'] ?? __( 'Remove', 'multinet-simple-cleanup' );

		$html = '<label for="' . esc_attr( $name ) . '">
            <input type="checkbox" id="' . esc_attr( $name ) . '"
                   value="1"
				' . checked( 1, SimpleCleanup::getOption( $name ), false ) . '
                   name="' . esc_attr( SimpleCleanup::option_name ) . '[' . esc_attr( $name ) . ']">
			' . $label . '
        </label>' . ( ! empty( $args['description'] ) ? '<p class="description">' . esc_html( $args['description'] ) . '</p>' : '' );
		if ( ! $output ) {
			return $html;
		}
		echo $html;
	}

	/**
	 * Create the settings page with all our needed form fields.
	 */
	public static function setupOptionsPage() {
		add_action( 'admin_init', function () {
			register_setting( 'multinet-simple-cleanup', SimpleCleanup::option_name );

			self::addSectionsToOptionsPage();
			self::addFieldsToOptionsPage();
		} );
		add_action( 'admin_menu', function () {
			self::addOptionsPageToMenu();
		} );
	}

	/**
	 * The settings page consists of multiple sections, each holding a set of options.
	 */
	protected static function addSectionsToOptionsPage() {
		/* --------------------------------------------------------------------------
		Add sections
		-------------------------------------------------------------------------- */
		add_settings_section(
			'multinet-simple-cleanup-section-general',
			__( 'General', 'multinet-simple-cleanup' ),
			null,
			'multinet-simple-cleanup'
		);

		add_settings_section(
			'multinet-simple-cleanup-section-frontend',
			__( 'Backend/admin menu', 'multinet-simple-cleanup' ),
			null,
			'multinet-simple-cleanup'
		);

		add_settings_section(
			'multinet-simple-cleanup-section-backend',
			__( 'Backend/admin menu', 'multinet-simple-cleanup' ),
			null,
			'multinet-simple-cleanup'
		);
	}

	/**
	 * Add the form fields to the appropriate sections on the options page.
	 */
	protected static function addFieldsToOptionsPage() {
		// Emojis
		add_settings_field( "remove_emoji", __( "Remove Emoji script/style", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-general", [
			'name'        => 'remove_emoji',
			'description' => __( 'By default, WordPress outputs some scripts and styles for Emoji support on your website.', 'multinet-simple-cleanup' ),
		] );

		// Remove blog clients
		add_settings_field( "remove_blog_clients", __( "Remove blog clients", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-frontend", [
			'name'        => 'remove_blog_clients',
			'description' => __( 'Removes the meta tags for xmlrpc.php and wlwmanifest.xml', 'multinet-simple-cleanup' ),
		] );

		// Remove generator
		add_settings_field( "remove_generator", __( "Remove generator tag", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-frontend", [
			'name'        => 'remove_generator',
			'description' => __( 'Removes the generator meta tag', 'multinet-simple-cleanup' ),
		] );

		// Remove wp-api tag
		add_settings_field( "remove_wp_json_tag", __( "Remove wp-json tag", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-frontend", [
			'name'        => 'remove_wp_json_tag',
			'description' => __( 'Removes the wp-json &lt;link&gt; tag', 'multinet-simple-cleanup' ),
		] );

		// Remove id="item-id-x"
		add_settings_field( "remove_nav_menu_item_id", __( "Remove ID from menu items", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-frontend", [
			'name'        => 'remove_nav_menu_item_id',
			'description' => __( 'Removes the id="menu-item-x" attribute from &lt;li&gt; menu items', 'multinet-simple-cleanup' ),
		] );

		// Remove menu-item classes
		add_settings_field( "remove_nav_menu_css_classes", __( "Remove classes from menu items", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-frontend", [
			'name'        => 'remove_nav_menu_css_classes',
			'description' => __( 'Removes all classes from &lt;a&gt; menu items except custom classes or the .current-menu-* classes', 'multinet-simple-cleanup' ),
		] );

		// Remove comments
		add_settings_field( "remove_comments", __( "Remove «Comments» from menu", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-backend", [
			'name'        => 'remove_comments',
			'description' => __( 'When you have comments disabled on your website you probably don\'t need the «Comments» link in the menu.', 'multinet-simple-cleanup' ),
		] );

		// Remove posts
		add_settings_field( "remove_posts", __( "Remove «Posts» from menu", 'multinet-simple-cleanup' ), [
			OptionsPageHandler::class,
			'checkbox'
		], "multinet-simple-cleanup", "multinet-simple-cleanup-section-backend", [
			'name'        => 'remove_posts',
			'description' => __( 'When you don\'t run a blog, eg. on small corporate websites, you probably don\'t need the general «Posts» link. Custom post types won\'t be affected by this option.', 'multinet-simple-cleanup' ),
		] );
	}

	/**
	 * Add an options page to the existing "Settings" menu item.
	 */
	protected static function addOptionsPageToMenu() {
		add_options_page(
			'Simple Cleanup',
			'Simple Cleanup',
			'manage_options',
			'multinet-simple-cleanup',
			[ OptionsPageHandler::class, 'optionsPageHtml' ]
		);
	}

	/**
	 * Outputs the settings page
	 */
	public static function optionsPageHtml() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// show error/update messages
		settings_errors( 'multinet-simple-cleanup-messages' );

		?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wporg"
				settings_fields( 'multinet-simple-cleanup' );
				// output setting sections and their fields
				// (sections are registered for "wporg", each field is registered to a specific section)
				do_settings_sections( 'multinet-simple-cleanup' );
				// output save settings button
				submit_button( __( 'Save Changes' ) );
				?>
            </form>
        </div>
		<?php
	}
}