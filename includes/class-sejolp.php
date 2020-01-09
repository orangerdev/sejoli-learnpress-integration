<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    SejoliLP
 * @subpackage SejoliLP/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SejoliLP
 * @subpackage SejoliLP/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class SejoliLP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SejoliLP_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SEJOLP_VERSION' ) ) {
			$this->version = SEJOLP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sejolp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_cli();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SejoliLP_Loader. Orchestrates the hooks of the plugin.
	 * - SejoliLP_i18n. Defines internationalization functionality.
	 * - SejoliLP_Admin. Defines all hooks for the admin area.
	 * - SejoliLP_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejolp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejolp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/product.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/order.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.php';

		/**
		 * The class responsible for defining CLI command and function
		 * side of the site.
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cli/simulation.php';

		$this->loader = new SejoliLP_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SejoliLP_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SejoliLP_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin = new SejoliLP\Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );

		$product = new SejoliLP\Admin\Product( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/product/fields', 	$product, 'set_product_fields', 11);
		$this->loader->add_filter( 'sejoli/product/meta-data',	$product, 'set_product_metadata', 100, 2);

		$order 	= new SejoliLP\Admin\Order( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'learn-press/checkout/default-user', 		$order, 'set_buyer_id',				1);
		$this->loader->add_filter( 'sejoli/order/meta-data', 					$order, 'set_order_metadata', 		100, 2);
		$this->loader->add_filter( 'sejoli/order/set-status/completed',			$order, 'create_learnpress_order',  200);
		$this->loader->add_filter( 'sejoli/order/set-status/on-hold',			$order, 'cancel_learnpress_order',  200);
		$this->loader->add_filter( 'sejoli/order/set-status/cancelled',			$order, 'cancel_learnpress_order',	200);
		$this->loader->add_filter( 'sejoli/order/set-status/refunded',			$order, 'cancel_learnpress_order',	200);
		$this->loader->add_filter( 'sejoli/order/set-status/in-progress',		$order, 'cancel_learnpress_order',	200);
		$this->loader->add_filter( 'sejoli/order/set-status/shipped',			$order, 'cancel_learnpress_order',	200);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$public = new SejoliLP\Front( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', 	$public, 	'enqueue_styles' 	, 1000);
		$this->loader->add_action( 'wp_enqueue_scripts', 	$public, 	'enqueue_scripts' 	, 1000);
		$this->loader->add_action( 'template_redirect',		$public,	'redirect_for_regular_pages', 10);
	}

	/**
	 * Register CLI commands
	 * @since  1.0.0
	 * @return void
	 */
	private function register_cli() {

		if ( !class_exists( 'WP_CLI' ) ) :
			return;
		endif;

		$simulation       = new SejoliLP\CLI\Simulation();

		WP_CLI::add_command('sejolilp simulation'	, $simulation);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    SejoliLP_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
