<?php


/**
 * Remove and deactivate all styling included with EDD
 *
 * @since 1.0
 */
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

/**
 * Remove the purchase link at the bottom of the download page
 *
 * @since 1.0
 */
remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since 1.0
 */
function trustedd_edd_body_classes( $classes ) {
	global $post;

	// add a shop class if we're on a page where the [downloads] shortcode is used
	if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'downloads' ) ) {
		$classes[] = 'edd-shop';
	}

    // if ( is_active_sidebar( 'sidebar-downloads' ) && is_singular( 'download' ) ) {
	// 	$classes[] = 'has-sidebar-downloads';
	// }

	return $classes;
}
add_filter( 'body_class', 'trustedd_edd_body_classes' );

/**
 * Is EDD Software Licensing active
 */
function trustedd_is_edd_sl_active() {
	return class_exists( 'EDD_Software_Licensing' );
}

/**
 * EDD purchase link defaults
 *
 * @since 1.0
 */
function trustedd_purchase_link_defaults( $defaults ) {
	// add a class of "small" to the add to cart button
	$defaults['class'] .= ' wide';

	$defaults['price'] = (bool) false;

	return $defaults;
}
add_filter( 'edd_purchase_link_defaults', 'trustedd_purchase_link_defaults' );

// get EDD currency
$currency = function_exists( 'edd_get_currency' ) ? edd_get_currency() : '';

/**
 * Wrap currency symbol in span
 *
 * @since 1.0
 */
function trustedd_currency_before( $formatted, $currency, $price ) {

	$symbol = edd_currency_symbol( $currency );

	if ( $symbol ) {
		$formatted = '<span class="currency">' . $symbol . '</span>' . $price;
	}

	return $formatted;
}
add_filter( 'edd_' . strtolower( $currency ) . '_currency_filter_before', 'trustedd_currency_before', 10, 3 );

/**
 * Wrap currency symbol in span
 *
 * @since 1.0
 */
function trustedd_currency_after( $formatted, $currency, $price ) {

	$symbol = edd_currency_symbol( $currency );

	if ( $symbol ) {
		$formatted = $price . '<span class="currency">' . $symbol . '</span>';
	}

	return $formatted;
}

// remove decimal places when not needed
add_filter( 'edd_' . strtolower( $currency ) . '_currency_filter_after', 'trustedd_currency_after', 10, 3 );

/**
 * Download price
 *
 * @since 1.0.0
 */
function trustedd_edd_price() {

	if ( edd_is_free_download( get_the_ID() ) ) {
		 $price = '<span class="edd_price">Free</span>';
	} elseif (  edd_has_variable_prices( get_the_ID() ) ) {
		$price =  '<div itemprop="price" class="edd_price">' . __( 'From', 'trustedd' ) . ' ' . edd_price( get_the_ID(), false ) . '</div>';
	} else {
		$price = edd_price( get_the_ID() );
	}

	return $price;

}

/**
 * Download purchase link
 *
 * @since 1.0.0
 */
function trustedd_edd_purchase_link() {

	$external_download_url = trustedd_get_download_meta( '_trustedd_download_url' );

//	var_dump( $external_download_url );

	$text = edd_is_free_download( get_the_ID() ) ? __( 'Download Now', 'trustedd' ) : __( 'Purchase', 'trustedd' );

	if ( $external_download_url ) {
		?>
		<div class="edd_download_purchase_form">
			<a href="<?php echo esc_url( $external_download_url ); ?>" class="button wide external" target="_blank">
				<span><?php echo $text; ?></span>

				<?php /*
				<svg class="external" width="16px" height="16px">
					<use xlink:href="<?php echo get_stylesheet_directory_uri() . '/images/svg-defs.svg#icon-external'; ?>"></use>
				</svg>
				*/ ?>

				<img src="<?php echo get_template_directory_uri() . '/images/external.svg'; ?>" width="16" height="16">
			</a>
		</div>

		<?php
	} else {
		echo edd_get_purchase_link();
	}

}

/**
 * The combined price and purchase button shown on the single download page
 *
 * @since 1.0.0
 */
function trustedd_edd_pricing() {
	?>
	<aside>
		<?php echo trustedd_edd_price(); ?>
		<?php trustedd_edd_purchase_link(); ?>
	</aside>
<?php
}
add_action( 'trustedd_sidebar_download', 'trustedd_edd_pricing' );

/**
 * Filter the purchase link defaults
 *
 * @since 1.0.0
 */
function trustedd_edd_purchase_link_defaults( $args ) {

	// free downloads
	if ( edd_is_free_download( get_the_ID() ) ) {
		$args['text'] = __( 'Add to cart', 'trustedd' );
	}

	return $args;
}
add_filter( 'edd_purchase_link_defaults', 'trustedd_edd_purchase_link_defaults' );


/**
 * Changelog
 */
function trustedd_product_changelog() {

	$changelog = get_post_meta( get_the_ID(), '_edd_sl_changelog', true );

	if ( ! ( is_singular( 'download' ) || $changelog || ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) || is_front_page() ) ) {
		return;
	}

	?>
	<script type="text/javascript">

		jQuery(document).ready(function($) {

		//inline
		$('.popup-content').magnificPopup({
			type: 'inline',
			fixedContentPos: true,
			fixedBgPos: true,
			overflowY: 'scroll',
			closeBtnInside: true,
			preloader: false,
			callbacks: {
				beforeOpen: function() {
				this.st.mainClass = this.st.el.attr('data-effect');
				}
			},
			midClick: true,
			removalDelay: 300
        });

		});
	</script>

	<?php
}
add_action( 'wp_footer', 'trustedd_product_changelog', 100 );
