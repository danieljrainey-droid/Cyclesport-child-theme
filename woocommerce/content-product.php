<?php
/**
 * Shop-loop product card, reskinned to the Cycle Sport 4.0 design system
 * (ProductCard.jsx in the original mockup). Overrides WooCommerce's
 * default woocommerce/templates/content-product.php.
 *
 * Real product data (price, sale status, stock, permalink) comes from
 * $product via WooCommerce as usual — only the markup/classes changed.
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'cs-product-card', $product ); ?>>
	<a href="<?php echo esc_url( get_permalink() ); ?>" class="cs-product-card__link">
		<div class="cs-product-card__media">
			<?php
			if ( $product->is_on_sale() ) :
				?>
				<span class="cs-product-card__badge cs-badge cs-badge--sale"><?php esc_html_e( 'Saldi', 'cyclesport-child' ); ?></span>
				<?php
			elseif ( $product->is_featured() ) :
				?>
				<span class="cs-product-card__badge cs-badge cs-badge--new"><?php esc_html_e( 'Novità', 'cyclesport-child' ); ?></span>
				<?php
			endif;

			echo wp_kses_post( woocommerce_get_product_thumbnail() );
			?>
		</div>
		<div class="cs-product-card__body">
			<?php
			if ( taxonomy_exists( 'product_brand' ) ) {
				$brands = wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );
				if ( ! empty( $brands ) ) {
					echo '<span class="cs-product-card__brand">' . esc_html( $brands[0] ) . '</span>';
				}
			}
			?>
			<span class="cs-product-card__name"><?php echo esc_html( $product->get_name() ); ?></span>
			<span class="cs-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
		</div>
	</a>
	<div class="cs-product-card__cta">
		<?php woocommerce_template_loop_add_to_cart(); ?>
	</div>
</li>
