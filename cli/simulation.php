<?php

namespace SejoliLP\CLI;

class Simulation
{
    protected $user_id = 0;

    public function set_user_id() {
        return $this->user_id;
    }

    /**
     * Get all active subscription and product
     *
     * <user_id>
     * The User ID
     *
     * <course_id>
     * The Course ID
     *
     * ## EXAMPLES
     *
     *  wp sejolilp simulation order 3 14
     *
     * @when after_wp_load
     */
    public function order(array $args) {

        list( $user_id, $course_id ) = $args;
        $this->user_id = $user_id;

        LP()->cart->add_to_cart($course_id);

        add_filter('learn-press/checkout/default-user', array($this, 'set_user_id'));

        __debug(LP()->checkout()->create_order());
    }
}
