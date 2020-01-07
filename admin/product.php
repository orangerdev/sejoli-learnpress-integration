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
class Product {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Add learnpress course data to sejoli product fields
     * Hooked via filter sejoli/product/fields, priority 5
     * @since   1.0.0
     * @param   array   $fields Array of fields
     * @return  array
     */
    public function set_product_fields($fields) {

        $fields[]   = array(
            'title'     => __('LearnPress', 'sejolilp'),
            'fields'    => array(
                Field::make('association', 'learnpress_course', __('Course', 'sejolilp'))
                    ->set_types(array(
                        array(
                            'type'      => 'post',
                            'post_type' => LP_COURSE_CPT
                        )
                    ))
                    ->set_help_text(__('Course yang akan digunakan pada pembelian produk ini', 'sejoli'))
            )
        );

        return $fields;
    }

}
