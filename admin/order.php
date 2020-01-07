<?php
namespace SejoliLP\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Order {

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
     * Buyer ID
     *
     * @since   1.0.0
     * @access  protected
     * @var     integer
     */
    protected $buyer_id = 0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Set learnpress metadata to order
     * Hooked via filter sejoli/order/meta-data, priority 100
     * @since   1.0.0
     * @param   array   $metadata   [description]
     * @param   array   $order_data [description]
     * @return  array
     */
    public function set_order_metadata(array $metadata, array $order_data) {

        $product = sejolisa_get_product($order_data['product_id']);

        if(property_exists($product, 'learnpress') && is_array($product->learnpress)) :
            $metadata['learnpress'] = $product->learnpress;
        endif;

        return $metadata;
    }

    /**
     * Set learnpress buyer order ID
     * Hooked via filter learn-press/checkcout/default-user, priority
     * @since   1.0.0
     * @param   integer $buyer_id
     */
    public function set_buyer_id( $buyer_id ) {

        if(0 !== $this->buyer_id) :
            return $this->buyer_id;
        endif;

        return $buyer_id;
    }

    /**
     * Create learnpress order when sejoli order completed
     * Hooked via sejoli/order/set-status/completed, prioirty 200
     * @since   1.0.0
     * @param   array  $order_data
     * @return  void
     */
    public function create_learnpress_order(array $order_data) {

        if(
            isset($order_data['meta_data']['learnpress']) &&
            !isset($order_data['meta_data']['learnpress_order'])
        ) :

            $this->buyer_id = $order_data['user_id'];
            $courses        = $order_data['meta_data']['learnpress'];

            foreach( (array) $courses as $course_id) :
                LP()->cart->add_to_cart($course_id);
            endforeach;

            $order_data['meta_data']['learnpress_order'] = $learnpress_order_id = LP()->checkout()->create_order();

            sejolisa_update_order_meta_data($order_data['ID'], $order_data['meta_data']);

            if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                $learnpress_order->update_status('completed');
                $learnpress_order->save();
            endif;
        endif;
    }

    /**
     * Cancel learnpress order
     * @since   1.0.0
     * @param   array  $order_data [description]
     * @return  void
     */
    public function cancel_learnpress_order(array $order_data) {

        if(isset($order_data['meta_data']['learnpress_order'])) :

            $learnpress_order_id = intval( $order_data['meta_data']['learnpress_order'] );

            if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                $learnpress_order->update_status('on-hold');
                $learnpress_order->save();
            endif;
        endif;

    }

}
