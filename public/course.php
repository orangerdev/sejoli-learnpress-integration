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
		remove_action( 'learn-press/after-courses-loop-item', 'learn_press_courses_loop_item_instructor', 25 );
        remove_action( 'learn-press/course-buttons', 'learn_press_course_purchase_button', 10 );
        remove_action( 'learn-press/course-buttons', 'learn_press_course_enroll_button', 15 );
        remove_action( 'learn-press/course-buttons', 'learn_press_course_retake_button', 20 );

    }

    /**
     * Display sejoli product button
     * Hooked via learn-press/content-landing-summary, priority 25
     * @since   1.0.0
     * @return  void
     */
    public function display_product_button() {
        global $post;

        $products = sejolilp_get_products($post->ID);
        $file     = (false === $products) ? 'template/no-product-related.php' : 'template/products-related.php';

        require SEJOLP_DIR . $file;
    }

    /**
     * Display purchase sejoli product button
     * Hooked via action learn-press/course-buttons, priority 10
     * @since   1.0.0
     * @return  void
     */
    public function display_purchase_button() {

		$course           = \LP_Global::course();
		$user             = \LP_Global::user();
		$button_available = false;

		if ( $course->get_external_link() ) {
			return;
		}

		if ( ! $course->is_publish() ) {
			return;
		}

		if ( $user->has_enrolled_course( $course->get_id() ) ) {
			return;
		}

		if ( ! $user->can_purchase_course( $course->get_id() ) ) {
			return;
		}

		if ( $user->has_purchased_course( $course->get_id() ) && 'finished' !== $user->get_course_status( $course->get_id() ) ) {
			return;
		}

		$products = sejolilp_get_products($course->get_id());

		require SEJOLP_DIR . 'template/purchase-buttons.php';
    }

	/**
	 * Change block button to list product
	 * Hooked via filter learn_press_content_item_protected_message, priority 999
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function change_block_button($content) {

		global $post;

		ob_start();
		$products = sejolilp_get_products($post->ID);
		require SEJOLP_DIR . 'template/block-content.php';
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Modify basic learnpress template
	 * @uses  		learn_press_get_template (action)
	 * @priority	999
	 * @since 		1.0.0
	 * @since 		1.0.5	Prevent display course price
	 * @param 		string $located  [description]
	 * @param 		string $template [description]
	 */
	public function set_template_for_block_part($located, $template) {

		if('single-course/content-protected.php' === $template) :

			$course   = \LP_Global::course();
			$products = sejolilp_get_products($course->get_id());
			$located  = SEJOLP_DIR . 'template/block-content.php';

		elseif(
			'single-course/buttons/enroll.php' === $template ||
			'loop/course/price.php' === $template ||
			'single-course/price.php' === $template
		) :

			$located  = SEJOLP_DIR . 'template/empty.php';

		endif;

		return $located;
	}

	/**
	 * Check if current user is really registered to the course, prevent any issue
	 * @uses 	learn-press/user-course-status (filter), 999
	 * @since 	1.0.6
	 * @param   string 		$status
	 * @param  	integer 	$course_id
	 * @param  	integer		$the_id
	 * @return 	string
	 */
	public function check_course_status( $status, $course_id, $the_id ) {

		if('LP_COURSE_PURCHASED' === $status) :
			return 'LP_COURSE_PURCHASED';
		endif;

		return $status;
	}
}
