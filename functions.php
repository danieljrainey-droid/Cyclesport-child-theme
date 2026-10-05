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
function cyclesport_asset_version( $relative_path ) {
	$file = get_stylesheet_directory() . $relative_path;
	return file_exists( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
}

function cyclesport_enqueue_assets() {
	$theme_uri = get_stylesheet_directory_uri();

	wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'cyclesport-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=IBM+Plex+Mono:wght@500&display=swap', array(), null );
	wp_enqueue_style( 'cyclesport-style', get_stylesheet_uri(), array( 'storefront-style' ), cyclesport_asset_version( '/style.css' ) );
	wp_enqueue_style( 'cyclesport-design-system', $theme_uri . '/assets/css/design-system.css', array( 'cyclesport-style' ), cyclesport_asset_version( '/assets/css/design-system.css' ) );
	wp_enqueue_style( 'cyclesport-layout', $theme_uri . '/assets/css/layout.css', array( 'cyclesport-design-system' ), cyclesport_asset_version( '/assets/css/layout.css' ) );
	wp_enqueue_style( 'cyclesport-woocommerce-overrides', $theme_uri . '/assets/css/woocommerce-overrides.css', array( 'cyclesport-layout' ), cyclesport_asset_version( '/assets/css/woocommerce-overrides.css' ) );

	wp_enqueue_script( 'cyclesport-theme', $theme_uri . '/assets/js/theme.js', array(), cyclesport_asset_version( '/assets/js/theme.js' ), true );
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

/**
 * Favicon, built from the Cycle Sport chain-ring logo mark.
 *
 * Falls back to these theme assets only when no Site Icon has been set
 * in the customizer, so an editor's own choice there still wins.
 */
function cyclesport_favicon_url( $size ) {
	return get_stylesheet_directory_uri() . '/assets/images/favicon-' . $size . 'x' . $size . '.png';
}

function cyclesport_site_icon_url( $url, $size, $blog_id ) {
	if ( $url || has_site_icon( $blog_id ) ) {
		return $url;
	}

	$available = array( 16, 32, 48, 192, 512 );
	$closest   = $available[0];
	foreach ( $available as $candidate ) {
		if ( $candidate >= $size ) {
			$closest = $candidate;
			break;
		}
		$closest = $candidate;
	}

	return cyclesport_favicon_url( $closest );
}
add_filter( 'get_site_icon_url', 'cyclesport_site_icon_url', 10, 3 );

function cyclesport_favicon_markup() {
	if ( has_site_icon() ) {
		return;
	}

	$theme_uri = get_stylesheet_directory_uri();
	?>
	<link rel="icon" href="<?php echo esc_url( cyclesport_favicon_url( 32 ) ); ?>" sizes="32x32">
	<link rel="icon" href="<?php echo esc_url( cyclesport_favicon_url( 192 ) ); ?>" sizes="192x192">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $theme_uri . '/assets/images/apple-touch-icon.png' ); ?>">
	<link rel="shortcut icon" href="<?php echo esc_url( $theme_uri . '/assets/images/favicon.ico' ); ?>">
	<?php
}
add_action( 'wp_head', 'cyclesport_favicon_markup', 1 );
add_action( 'admin_head', 'cyclesport_favicon_markup', 1 );
add_action( 'login_head', 'cyclesport_favicon_markup', 1 );

/**
 * English language option.
 *
 * The site's actual content (pages, products, menus) lives in the
 * database, not in this theme, so a theme alone can't translate it — that
 * needs a multilingual plugin. This renders an IT/EN switcher, styled to
 * match the design system, once Polylang or WPML is installed and has
 * languages configured; until then it shows site admins a one-time nudge
 * instead of a switcher with nothing to switch to.
 */
function cyclesport_has_multilingual_plugin() {
	return function_exists( 'pll_the_languages' ) || has_action( 'wpml_add_language_selector' ) || function_exists( 'icl_get_languages' );
}

function cyclesport_language_switcher_markup() {
	if ( function_exists( 'pll_the_languages' ) ) {
		$links = pll_the_languages( array(
			'raw'             => 1,
			'hide_if_empty'   => 0,
			'display_names_as' => 'slug',
		) );
		if ( empty( $links ) ) {
			return;
		}
		echo '<div class="cs-lang-switcher">';
		foreach ( $links as $lang ) {
			$classes = 'cs-lang-switcher__link' . ( ! empty( $lang['current_lang'] ) ? ' is-active' : '' );
			printf(
				'<a href="%1$s" class="%2$s">%3$s</a>',
				esc_url( $lang['url'] ),
				esc_attr( $classes ),
				esc_html( strtoupper( $lang['slug'] ) )
			);
		}
		echo '</div>';
		return;
	}

	if ( function_exists( 'icl_get_languages' ) ) {
		$languages = icl_get_languages( 'skip_missing=0' );
		if ( empty( $languages ) ) {
			return;
		}
		echo '<div class="cs-lang-switcher">';
		foreach ( $languages as $lang ) {
			$classes = 'cs-lang-switcher__link' . ( ! empty( $lang['active'] ) ? ' is-active' : '' );
			printf(
				'<a href="%1$s" class="%2$s">%3$s</a>',
				esc_url( $lang['url'] ),
				esc_attr( $classes ),
				esc_html( strtoupper( $lang['language_code'] ) )
			);
		}
		echo '</div>';
	}
}
add_action( 'storefront_header', 'cyclesport_language_switcher_markup', 60 );

function cyclesport_language_switcher_styles() {
	if ( ! cyclesport_has_multilingual_plugin() ) {
		return;
	}
	?>
	<style>
	.cs-lang-switcher { display: flex; gap: 4px; align-items: center; font-family: var(--font-mono); font-size: 12px; font-weight: 700; }
	.cs-lang-switcher__link { padding: 2px 5px; border-radius: var(--radius-sm); color: var(--ink-400); text-decoration: none; }
	.cs-lang-switcher__link.is-active { color: var(--accent-primary); background: var(--white); }
	</style>
	<?php
}
add_action( 'wp_head', 'cyclesport_language_switcher_styles', 5 );

function cyclesport_language_plugin_admin_notice() {
	if ( cyclesport_has_multilingual_plugin() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	$install_url = wp_nonce_url(
		self_admin_url( 'update.php?action=install-plugin&plugin=polylang' ),
		'install-plugin_polylang'
	);
	?>
	<div class="notice notice-info is-dismissible">
		<p>
			<?php esc_html_e( 'Cycle Sport 4.0: the theme is ready to show an IT/EN language switcher in the header, but no multilingual plugin is active yet, so there is nothing for it to switch between.', 'cyclesport-child' ); ?>
			<a href="<?php echo esc_url( $install_url ); ?>"><?php esc_html_e( 'Install Polylang (free) to add English translations.', 'cyclesport-child' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'cyclesport_language_plugin_admin_notice' );
