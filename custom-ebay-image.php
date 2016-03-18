<?php
/**
 * Plugin Name: WooCommerce Custom eBay Image Add on
 * Plugin URI: https://www.wplab.com/
 * Description: An e-commerce toolkit that helps you 	
 * Version: 1.0
 * Author: Hamilton Nieri
 * Author URI: https://www.wplab.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * ----------------------------------------------------------------------
 * Copyright (C) 2016  Hamilton Nieri  (Email: hamiltonnieri8755@yahoo.com)
 * ----------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ----------------------------------------------------------------------
 */

// Including WP core file
if ( ! function_exists( 'get_plugins' ) )
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

// Including base class
if ( ! class_exists( 'WC_MetaBox_Custom_ebay_Images' ) )
    require_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-mb-ebay-images.php';

// Whether plugin active or not
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) :

	/**
	 * Display Image Gallery Metabox on edit product page
	 **/

	add_action( 'add_meta_boxes', 'wcei_add_meta_boxes' );

	function wcei_add_meta_boxes(){

	    add_meta_box(
	        'woocommerce-custom-ebay-image',
	        'Custom eBay Image',
	        'custom_ebay_image_meta',
	        'product',
	        'side',
	        'default'
	    );

	}

	$wcei = NULL;

	/**
	 * Outputs the content of the meta box
	 */
	function custom_ebay_image_meta( $post ) {
		
		global $wcei;
		$wcei = new WC_MetaBox_Custom_ebay_Images( $post );
		echo $wcei->output();

	}

	/**
	 * Save attachments to _ebay_image_gallery
	 */	

	add_action( 'save_post', 'wcei_save_ebay_gallery', 10, 3 );

	function wcei_save_ebay_gallery( $post_id, $post, $update ) {
 		
		$attachment_ids = isset( $_POST['ebay_image_gallery'] ) ? array_filter( explode( ',', wc_clean( $_POST['ebay_image_gallery'] ) ) ) : array();
		update_post_meta( $post_id, '_ebay_image_gallery', implode( ',', $attachment_ids ) );

	}

endif;