<?php
/**
 * Product Images
 *
 * Display the product images meta box.
 *
 * @author      Hamilton Nieri
 * @category    Developer
 * @package     
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_MetaBox_Custom_ebay_Images
 */
class WC_MetaBox_Custom_ebay_Images {

	/**
	 * Object
	 *
	 * @access private
	 * @var    WC_Product
	 */
	private $post;

	/**
     * Class constructor
     *
     * @access public
     * @param 
     */
    public function __construct( $post ) {
    	$this->post = $post;
    	$this->wcei_enqueue();
    }

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function output() {

		$post = $this->post;
?>
		<div id="ebay_images_container">
			<ul class="ebay_images">
				<?php

					// Init 
					$ebay_image_gallery = "";

					if ( (!metadata_exists('post', $post->ID , '_ebay_image_gallery')) && metadata_exists('post', $post->ID, '_product_image_gallery') ) {

						// _ebay_image_gallery meta field is not set but the post has _product_image_gallery

						$ebay_image_gallery = get_post_meta( $post->ID, '_product_image_gallery', true );

					} else if ( (!metadata_exists('post', $post->ID , '_ebay_image_gallery')) && (!metadata_exists('post', $post->ID, '_product_image_gallery')) ) {

						// Both _ebay_image_gallery and _product_image_gallery meta fields are not set

						$attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
						$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
						$ebay_image_gallery = implode( ',', $attachment_ids );

					} else if ( (metadata_exists('post', $post->ID, '_ebay_image_gallery')) ) {

						// The post has _ebay_image_gallery meta field

						$ebay_image_gallery = get_post_meta( $post->ID, '_ebay_image_gallery', true );

					}
 
					$attachments = array_filter( explode( ',', $ebay_image_gallery ) );

					$update_meta = false;

					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {

							$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

							// if attachment is empty, then skip
							if ( empty( $attachment ) ) {
								$update_meta = true;
								continue;
							}

							echo '<li class="ebay_image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . $attachment . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a></li>
								</ul>
							</li>';

							// rebuild ids to be saved
							$updated_gallery_ids[] = $attachment_id;
						}

						// need to update product meta to set new gallery ids
						if ( $update_meta ) {
							update_post_meta( $post->ID, '_ebay_image_gallery', implode( ',', $updated_gallery_ids ) );
						}
					}
				?>
			</ul>

			<input type="hidden" id="ebay_image_gallery" name="ebay_image_gallery" value="<?php echo esc_attr( $ebay_image_gallery ); ?>" />

		</div>
		<p class="add_ebay_images hide-if-no-js">
			<a href="#" data-choose="<?php esc_attr_e( 'Add Images to Product Gallery', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'woocommerce' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'woocommerce' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'woocommerce' ); ?>"><?php _e( 'Add product gallery images', 'woocommerce' ); ?></a>
		</p>
		<?php
	}

	/**
     * Class constructor
     *
     * @access public
     * @param 
     */
	public function wcei_enqueue() {
    	
    	wp_enqueue_media();

        wp_enqueue_style( 'wcei-style-custom', plugins_url( 'css/wcei_style.css', dirname(__FILE__) ) );
        wp_enqueue_script( 'wcei-script-main', plugins_url( 'js/wcei_script.js', dirname(__FILE__) ), array(), '1.0.0', true);
    }
}

