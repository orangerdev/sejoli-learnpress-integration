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

        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    
        $plugin_dir  = WP_PLUGIN_DIR . '/learnpress/learnpress.php';
        $plugin_data = get_plugin_data( $plugin_dir );

        if( version_compare($plugin_data['Version'], '4.2.2.2', '<=') ) :

            if(
                isset($order_data['meta_data']['learnpress']) &&
                !isset($order_data['meta_data']['learnpress_order'])
            ) :

                $this->buyer_id = $order_data['user_id'];
                $courses        = $order_data['meta_data']['learnpress'];

    			if(!is_object(LP()->cart) || !method_exists(LP()->cart, 'add_to_cart') || false === LP()->cart) :
    				LP()->cart = new \LP_Cart(); // Call the class directly
    			endif;

    			LP()->cart->empty_cart(); // empty cart
    			
    			do_action('sejoli/log/write', 'learnpress-create-order', $courses);

                foreach( (array) $courses as $course_id) :
                    LP()->cart->add_to_cart($course_id);
                endforeach;

                $order_data['meta_data']['learnpress_order'] = $learnpress_order_id = LP()->checkout()->create_order();

                sejolisa_update_order_meta_data($order_data['ID'], $order_data['meta_data']);

                if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                    $learnpress_order->update_status('completed');
                    $learnpress_order->save();
                endif;

    		elseif(isset($order_data['meta_data']['learnpress_order'])) :

    			$learnpress_order_id = intval($order_data['meta_data']['learnpress_order']);

    			if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                    $learnpress_order->update_status('completed');
                    $learnpress_order->save();
                endif;

            endif;

        else:

            global $wpdb;

            if(
                isset($order_data['meta_data']['learnpress']) &&
                !isset($order_data['meta_data']['learnpress_order'])
            ) :

                $this->buyer_id = $order_data['user_id'];
                $courses        = $order_data['meta_data']['learnpress'];

                if(!is_object(LP()->cart) || !method_exists(LP()->cart, 'add_to_cart') || false === LP()->cart) :
                    LP()->cart = new \LP_Cart(); // Call the class directly
                endif;
                
                do_action('sejoli/log/write', 'learnpress-create-order', $courses);

                $order_data['meta_data']['learnpress_order'] = $learnpress_order_id = LP()->checkout()->create_order();

                foreach( (array) $courses as $course_id ) :
                    LP()->cart->add_to_cart($course_id);

                    $cart     = LP()->cart;
                    $checkout = LP()->checkout();

                    if ( ! learn_press_enable_cart() ) :
                        $cart->empty_cart();
                    endif;

                    $item = array(
                        'item_id'         => absint( $course_id ),
                        'order_item_name' => get_the_title( $course_id ),
                    );
                    
                    $item_type = get_post_type( $item['item_id'] );
                    
                    $item = wp_parse_args(
                        $item,
                        array(
                            'item_id'         => 0,
                            'item_type'       => '',
                            'order_item_name' => '',
                            'quantity'        => 1,
                            'subtotal'        => 0,
                            'total'           => 0,
                            'meta'            => array(),
                        )
                    );

                    switch ( $item_type ) {
                        case LP_COURSE_CPT:
                            $course                     = learn_press_get_course( $item['item_id'] );
                            $item['subtotal']           = apply_filters( 'learnpress/order/item/subtotal', $course->get_price() * $item['quantity'], $course, $item );
                            $item['total']              = apply_filters( 'learnpress/order/item/total', $course->get_price() * $item['quantity'], $course, $item );
                            $item['order_item_name']    = apply_filters( 'learnpress/order/item/title', $course->get_title(), $course, $item );
                            $item['meta']['_course_id'] = $item['item_id'];
                            break;
                        default:
                            $item = apply_filters( 'learnpress/order/add-item/item_type_' . $item_type, $item );
                            break;
                    }
                    $wpdb->insert(
                        $wpdb->learnpress_order_items,
                        array(
                            'order_item_name' => get_the_title( $course_id ),
                            'order_id'        => $learnpress_order_id,
                            'item_id'         => absint( $course_id ),
                            'item_type'       => 'lp_course',
                        ),
                        array(
                            '%s',
                            '%d',
                            '%d',
                            '%s',
                        )
                    );
                    $order_item_id = absint( $wpdb->insert_id );

                    // Add learnpress_order_itemmeta
                    $item['meta']['_quantity'] = $item['quantity'];
                    $item['meta']['_subtotal'] = $item['subtotal'] ?? 0;
                    $item['meta']['_total']    = $item['total'] ?? 0;

                    if ( is_array( $item['meta'] ) ) :
                        foreach ( $item['meta'] as $k => $v ) :
                            learn_press_add_order_item_meta( $order_item_id, $k, $v );
                        endforeach;
                    endif;
                endforeach;

                sejolisa_update_order_meta_data($order_data['ID'], $order_data['meta_data']);

                if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                    $learnpress_order->update_status('completed');
                    $learnpress_order->save();
                endif;

            elseif(isset($order_data['meta_data']['learnpress_order'])) :

                $learnpress_order_id = intval($order_data['meta_data']['learnpress_order']);

                if ( $learnpress_order = learn_press_get_order( $learnpress_order_id ) ) :
                    $learnpress_order->update_status('completed');
                    $learnpress_order->save();
                endif;
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
