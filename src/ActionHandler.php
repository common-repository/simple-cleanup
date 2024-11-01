<?php

namespace Multinet\SimpleCleanup;

defined( 'ABSPATH' ) or die;

class ActionHandler {

	public static function addActions() {
		// Remove Emoji support
		if ( SimpleCleanup::getOption( 'remove_emoji' ) ) {
			add_action( 'init', function () {
				remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
				remove_action( 'wp_print_styles', 'print_emoji_styles' );
				remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
				remove_action( 'admin_print_styles', 'print_emoji_styles' );
				remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
				remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
				remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			} );
		}

		// Remove "Comments" link from admin menu and admin bar
		if ( SimpleCleanup::getOption( 'remove_comments' ) ) {
			add_action( 'admin_menu', function () {
				remove_menu_page( 'edit-comments.php' );
			} );
			add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
				$wp_admin_bar->remove_node( 'comments' );
			}, 999 );
		}

		// Remove "Posts" link from admin menu (+ remove from admin bar and remover "quick draft" dashboard box
		if ( SimpleCleanup::getOption( 'remove_posts' ) ) {
			add_action( 'admin_menu', function () {
				remove_menu_page( 'edit.php' );
			} );
			add_action( 'wp_dashboard_setup', function () {
				remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
			} );
			add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
				$wp_admin_bar->remove_node( 'new-post' );
			}, 999 );
		}

		// Remove WLW (Windows Live Writer) and RSD tags
		if ( SimpleCleanup::getOption( 'remove_blog_clients' ) ) {
			add_action( 'init', function () {
				remove_action( 'wp_head', 'wlwmanifest_link' );
				remove_action( 'wp_head', 'rsd_link' );
			} );
		}

		// Remove id="menu-item-ID" from <li> menu items
		if ( SimpleCleanup::getOption( 'remove_nav_menu_item_id' ) ) {
			add_action( 'init', function () {
				add_filter( 'nav_menu_item_id', '__return_null', 10, 3 );
			} );
		}

		// Remove menu css classes
		if ( SimpleCleanup::getOption( 'remove_nav_menu_css_classes' ) ) {
			add_action( 'init', function () {
				add_filter( 'nav_menu_css_class', function ( $classes ) {
					{
						if ( ! is_array( $classes ) ) {
							return '';
						}

						// Remove all classes starting with menu-item etc., meaning we only keep custom classes
						// or current-menu-.... classes which you probably need to style the navigation.
						foreach ( $classes as $index => $class ) {
							if ( strpos( $class, 'menu-item' ) === 0 ||
							     strpos( $class, 'current-page' ) === 0 ||
							     strpos( $class, 'current_page' ) === 0 ||
							     strpos( $class, 'page_item' ) === 0 ||
							     strpos( $class, 'page-item' ) === 0 ) {
								unset( $classes[ $index ] );
							}
						}

						return $classes;
					}
				}, 100, 1 );
			} );
		}

		// Remote "Generator" meta tag
		if ( SimpleCleanup::getOption( 'remove_generator' ) ) {
			add_action( 'init', function () {
				remove_action( 'wp_head', 'wp_generator' );
			} );
		}

		// Remote "wp-json" link tag
		if ( SimpleCleanup::getOption( 'remove_wp_json_tag' ) ) {
			add_action( 'init', function () {
				remove_action( 'wp_head', 'rest_output_link_wp_head' );
			} );
		}
	}
}