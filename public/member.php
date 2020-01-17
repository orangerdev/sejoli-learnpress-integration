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
class Member {

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
     * Course menu position
     * @since   1.0.0
     * @var     integer
     */
    protected $menu_position = 3;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Add course menu to default member-area menu
     * Hooked via filter sejoli/member-area/menu, priority 999
     * @since   1.0.0
     * @param   array   $menu   Member area men
     * @return  array   Modified member area menu with course menu
     */
    public function add_course_menu(array $menu) {

        $course_menu = array(
            'link'    => site_url('member-area/course-list'),
            'label'   => __('Kelas','sejoli'),
            'icon'    => 'graduation cap icon',
            'class'   => 'item',
            'submenu' => []
        );

        // Add course menu in selected position
        $menu   =   array_slice($menu, 0, $this->menu_position, true) +
                    array('learnpress-all-course' => $course_menu) +
                    array_slice($menu, $this->menu_position, count($menu) - 1, true);

        return $menu;
    }

    /**
     * Add course menu to menu backend area
     * Hooked via filter sejoli/member-area/backend/menu, priority 999
     * @since   1.0.0
     * @param   array   $menu   Sejoli member area menu
     * @return  array   Modified member area menu
     */
    public function add_course_menu_in_backend(array $menu) {

        $course_menu = array(
            'title'  => __('Kelas (LearnPress)', 'sejoli'),
            'object' => 'sejoli-learnpress-course-list',
            'url'    => site_url('member-area/course-list')
        );

        // Add course menu in selected position
        $menu   =   array_slice($menu, 0, $this->menu_position, true) +
                    array('all-course' => $course_menu) +
                    array_slice($menu, $this->menu_position, count($menu) - 1, true);

        return $menu;
    }

}
