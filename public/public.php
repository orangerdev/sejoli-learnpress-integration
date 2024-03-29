<?php
namespace SejoliLP;

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
class Front {

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
	 * Enable semantic theme
	 * @since 	1.1.7
	 * @var 	boolean
	 */
	protected $enable_semantic = true;

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		global $post;

		if(is_object($post) && property_exists($post, 'ID')) :
			$page_template = get_post_meta($post->ID, '_wp_page_template', true);

			if(
				learn_press_is_course() || learn_press_is_lesson() || learn_press_is_quiz() ||
				is_archive(LP_COURSE_CPT) ||
				is_category('course_category')
			):

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sejolilp-public.css', array(), $this->version, 'all' );

			endif;
		endif;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sejolilp-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Redirect if current page is learnpress page and user is not logged in
	 * Hooked via action template_redirect, priority 10
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function redirect_for_regular_pages() {

		global $post;

		// redirect from checkout page to course list
		if(learn_press_is_checkout()) :
			wp_redirect( get_post_type_archive_link(LP_COURSE_CPT) );
			exit;
		endif;

		if ( learn_press_is_profile() && is_object( $post ) ) :
			if ( has_shortcode( $post->post_content, 'learn_press_profile' ) ) :
				wp_redirect( site_url('member-area/profile'));
				exit;
			endif;
		endif;

	}

	/**
	 * Check if current page is using sejoli-member-page.php
	 * Hooked via filter template_include, priority 10
	 * @since 	1.1.7
	 * @since 	1.3.0 	Change priority from 1 to 10
	 * @param  	string	$template	Template file
	 * @return 	string
	 */
	public function view_member_template($template) {

		global $post, $wp_query;

		// Return template if post is empty
		if ( ! $post ) :
			return $template;
		endif;
		
		$get_page_template = get_page_template_slug();

		// Return default template if we don't have a custom one defined
		if(false !== $this->enable_semantic) :

			if($wp_query->query_vars['post_type'] == 'lp_course' && $get_page_template == 'sejoli-member-page.php') :
				$template = plugin_dir_path( dirname( __FILE__ ) ) . 'template/single-course-template.php';
			elseif($wp_query->query_vars['post_type'] == 'lp_course') :
				return $template;
			else:
				return $template;
			endif;

			return $template;

		endif;

		return $template;
	}

	/**
	 * Enable semantic
	 * Hooked via filter sejoli/enable, priority 100
	 * @since 	1.1.7
	 * @return 	boolean
	 */
	public function enable_semantic($enable_semantic) {
		return (true === $enable_semantic) ? true : $this->enable_semantic;
	}
}
