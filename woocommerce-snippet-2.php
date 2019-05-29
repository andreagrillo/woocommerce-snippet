<?php

/**
 * WordPress Meetup Catania - 30 Maggio 2019
 * @author Andrea Grillo <info@andreagrillo.it>
 * @version 1.0.0
 */
 
 /**
 * #1: Cambiare il numero di colonne della pagina shop
 */
add_filter('loop_shop_columns', 'ag_loop_shop_columns');

if( ! function_exists( 'ag_loop_shop_columns' ) ){
	function ag_loop_shop_columns( $columns ) {
		$columns = 5;

		// Product Taxonomies
		if ( is_product_taxonomy() ){
			$columns = 4;
		}

		//Related Products
		if ( is_product() ) {
			$columns = 3;
		}

		//Cross Sells
		if ( is_checkout() ) {
			$columns = 2;
		}

		return $columns;
	}
}

/**
 * #2: Aggiungere immagine di testata nella pagina shop
 */

add_action( 'woocommerce_archive_description', 'ag_add_store_header_image' );

if( ! function_exists( 'ag_add_store_header_image' ) ){
	function ag_add_store_header_image(){
		ob_start(); ?>
		<div class="store_header_wrapper">
			<img src="https://via.placeholder.com/1080x150/21759b/ffffff?text=WordPress Meetup Catania" alt="WordPress Meetup Catania" class="ag_header_image">
		</div>
		<?php
		echo ob_get_clean();
	}
}

/**
 * #3: Aggiungere la descrizione sotto il prodotto
 */

add_action('woocommerce_after_shop_loop_item_title','woocommerce_template_single_excerpt', 5);

/**
 * #4: Rimuovere la dropdown Order By nella pagina shop
 */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * #5: Rimuovere la dropdown Order By nella pagina shop per Storefront
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );

/**
 * #6: Rimuovere Order By --> Versione Multi Tema
 */

$theme = function_exists( 'wp_get_theme' ) ? wp_get_theme() : null;

if( $theme instanceof WP_Theme ){
    $template = $theme->get_template();
    //Convert template to lowercase
    $template = strtolower( $template );
    //Set priority to 30 (Default Value)
    $priority = 30;

    if( 'storefront' == $template){
        $priority = 10;
    }

	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', $priority );
}

/**
 * #7: Aggiungere un prodotto al carrello tramite query_string
 */


add_action( 'wp', 'ag_add_product_to_cart' );

if ( ! function_exists( 'ag_add_product_to_cart' ) ) {
	function ag_add_product_to_cart() {
		if ( ! is_admin() && ! is_ajax() ) {
			$product_id = ! empty( $_GET[ 'product_id' ] ) ? $_GET[ 'product_id' ] : 0;
			$found      = false;
            //check if product already in cart
            if( ! empty( $product_id ) ){
	            if ( sizeof( wc()->cart->get_cart() ) > 0 ) {
		            foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {
			            $_product = $values['data'];
			            if ( $_product->get_id() == $product_id ) {
				            $found = true;
			            }
		            }
		            // if product not found, add it
		            if ( ! $found ) {
			            wc()->cart->add_to_cart( $product_id );
		            }
	            }

	            else {
		            // if no products in cart, add it
		            wc()->cart->add_to_cart( $product_id );
	            }
            }
		}
	}
}
