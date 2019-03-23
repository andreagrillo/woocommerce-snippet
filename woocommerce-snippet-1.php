<?php

/**
 * WordPress Meetup Catania - 28 Marzo 2019
 * @author Andrea Grillo <info@andreagrillo.it>
 * @version 1.0.0
 */

/**
 * #1: Cambiare la stringa Out of Stock
 */
add_filter( 'woocommerce_get_availability_text', 'ag_woocommerce_get_availability_text', 10, 2 );

if( ! function_exists( 'ag_woocommerce_get_availability_text' ) ){
	function ag_woocommerce_get_availability_text( $label, $product ) {
		/** @var $product WC_Product */
		if( ! $product->is_in_stock() ){
			$label = __( 'Sold Out!!!', 'woocommerce' );
		}
		return $label;
	}
}

/**
 * #2: Prodotto già aggiunto al carrello - Shop Page
 */
add_filter( 'woocommerce_product_add_to_cart_text', 'ag_custom_add_to_cart_text', 10, 2 );

if( ! function_exists( 'ag_custom_add_to_cart_text' ) ){
	function ag_custom_add_to_cart_text( $add_to_cart_text, $product ) {
		/** @var $product WC_Product */
		foreach( wc()->cart->get_cart() as $cart_item_key => $values ) {
			if( $product->get_id() == $values['product_id'] ) {
				$add_to_cart_text =  __('Già nel carrello', 'woocommerce');
				break;
			}
		}

		return $add_to_cart_text;
	}
}

/**
 * #3: Prodotto già aggiunto al carrello - Single Product Page
 */
add_filter( 'woocommerce_product_single_add_to_cart_text', 'ag_custom_add_to_cart_text', 10, 2 );


/**
 * #4: Bloccare gli ordini inferiori a 50€
 */
add_action( 'woocommerce_checkout_process', 'ag_add_minimum_order_amount' );

if ( ! function_exists( 'ag_add_minimum_order_amount' ) ) {
	function ag_add_minimum_order_amount() {
		$minimum = 50;
		$cart_total = wc()->cart->get_total('edit' );

		if ( $cart_total < $minimum ) {
			$add_more = $minimum - $cart_total;
			$notice = sprintf( 'Ordine minimo %s. Mancano %s € per poter completare il tuo ordine',
				wc_price( $minimum ),
				wc_price( $add_more )
			);
			wc_add_notice( $notice, 'error' );
		}
	}
}

/**
 * #5: Aggiunge un messaggio al checkout
 */
add_action( 'woocommerce_before_checkout_form', 'ag_add_minimum_order_amount', 15 );

/**
 *#6: Disabilitare pulsante Effettua Ordine per importi inferiori a 50€
 */
add_action( 'woocommerce_order_button_html', 'ag_disable_place_order_button'  );

if( ! function_exists( 'ag_disable_place_order_button' ) ){
	function ag_disable_place_order_button( $button ){
		$minimum = 50;
		$cart_total = wc()->cart->get_total('edit' );

		if ( $cart_total < $minimum ) {
			$style = 'style="color:#fff;cursor:not-allowed;background-color:#999;"';
			$text = sprintf( '%s %s', __ ( 'Importo minimo', 'woocommerce' ), wc_price( $minimum ) );
			$button = '<a class="button" '.$style.'>' . $text . '</a>';
		}
		return $button;
	}
}

/**
 * #7: Cambiare il numero di colonne dello shop
 */
add_filter( 'loop_shop_columns', 'ag_loop_shop_columns' );

if( ! function_exists( 'ag_loop_shop_columns' ) ){
	function ag_loop_shop_columns( $cols ){
		$cols = 2;
		return $cols;
	}
}

/**
 * #8: Rimuovere le product tab
 */
add_filter( 'woocommerce_product_tabs', '__return_empty_array', 99 );

/**
 * #9: Mostrare le dimensioni di un prodotto nel loop
 */
add_action( 'woocommerce_after_shop_loop_item', 'ag_show_product_dimensions', 5 );

if( ! function_exists( 'ag_show_product_dimensions' ) ){
	function ag_show_product_dimensions() {
		global $product;

		if ( $product->has_dimensions() ) {
			$dimensions = wc_format_dimensions($product->get_dimensions(false));
			echo '<div class="product-meta"><span class="product-meta-label">Dimensioni: </span>' . $dimensions . '</div>';
		}
	}
}

/**
 * #10: Mostrare il peso di un prodotto nel loop
 */
add_action( 'woocommerce_after_shop_loop_item', 'ag_show_weights', 5 );

if( ! function_exists( 'ag_show_weights' ) ){
	function ag_show_weights() {
		global $product;

		if ( $product->has_weight() ) {
			$weight = $product->get_weight();

			echo '<div class="product-meta"><span class="product-meta-label">
			Peso: </span>' . $weight . get_option('woocommerce_weight_unit') . '</div></br>';
		}
	}
}
