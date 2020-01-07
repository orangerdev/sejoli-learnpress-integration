<?php

namespace SejoliLP\CLI;

class Simulation
{
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
        __debug($user_id, $course_id);
    }
}
