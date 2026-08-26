<?php
/**
 * Cycle Sport 4.0 child theme functions.
 *
 * Storefront (the parent theme) already declares WooCommerce support and
 * renders the header/footer/shop-loop markup and hooks; this file only
 * layers the ported design system on top of it and adjusts the pieces
 * that need real markup changes rather than a CSS reskin.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue parent + child styles/scripts.
 */
function cyclesport_enqueue_assets() {
	$theme_uri     = get_stylesheet_directory_uri();
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'cyclesport-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=IBM+Plex+Mono:wght@500&display=swap', array(), null );
	wp_enqueue_style( 'cyclesport-style', get_stylesheet_uri(), array( 'storefront-style' ), $theme_version );
	wp_enqueue_style( 'cyclesport-design-system', $theme_uri . '/assets/css/design-system.css', array( 'cyclesport-style' ), $theme_version );
	wp_enqueue_style( 'cyclesport-layout', $theme_uri . '/assets/css/layout.css', array( 'cyclesport-design-system' ), $theme_version );
	wp_enqueue_style( 'cyclesport-woocommerce-overrides', $theme_uri . '/assets/css/woocommerce-overrides.css', array( 'cyclesport-layout' ), $theme_version );

	wp_enqueue_script( 'cyclesport-theme', $theme_uri . '/assets/js/theme.js', array(), $theme_version, true );
}
add_action( 'wp_enqueue_scripts', 'cyclesport_enqueue_assets', 20 );

/**
 * Append design-system button classes to WooCommerce's add-to-cart
 * buttons without touching the classes WooCommerce itself needs for the
 * AJAX add-to-cart behaviour (add_to_cart_button, ajax_add_to_cart, …).
 */
function cyclesport_add_to_cart_button_class( $args ) {
	$args['class'] = trim( ( isset( $args['class'] ) ? $args['class'] : '' ) . ' cs-btn cs-btn--dark cs-btn--sm' );
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'cyclesport_add_to_cart_button_class' );
