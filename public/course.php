<?php
namespace SejoliLP\Front;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/public
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Course {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param   string    $plugin_name       The name of the plugin.
	 * @param   string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Remove default learnpress hooks that related to checkout actions
     * Hooked via plugins_loaded, priority 1
     * @since   1.0.0
     * @return  void
     */
    public function remove_unneeded_hooks() {

        remove_action( 'learn-press/content-landing-summary', 'learn_press_course_price', 25 );
        remove_action( 'learn-press/content-landing-summary', 'learn_press_course_buttons', 30 );
        remove_action( 'learn-press/after-courses-loop-item', 'learn_press_courses_loop_item_price', 20 );
        remove_action( 'learn-press/after-courses-loop-item', 'learn_press_course_loop_item_buttons', 35 );

    }

    /**
     * Display sejoli product button
     * Hooked via learn-press/content-landing-summary, priority 25
     * @since   1.0.0
     * @return [type] [description]
     */
    public function display_product_button() {
        global $post;

        $products = sejolilp_get_products($post->ID);
        $file     = (false === $products) ? 'template/no-product-related.php' : 'template/products-related.php';

        require SEJOLP_DIR . $file;
    }
}
