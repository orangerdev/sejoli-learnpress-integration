<?php
/**
 * Get user purchased courses
 * @since   1.0.0
 * @param   integer $user_id
 * @return  array   Purchased courses detail
 */
function sejolilp_get_user_purchased_courses($user_id = 0) {

    $courses = array();
    $profile = learn_press_get_profile($user_id);
    $query   = $profile->query_courses( 'purchased' );

    if( $query['items'] ) :

        foreach($query['items'] as $user_course) :

            $id           = $user_course->get_id();
            $courses[$id] = array(
                'date'          => $user_course->get_start_time(),
                'result'        => $user_course->get_percent_result(),
                'status'        => $user_course->get_results( 'status' )
            );

        endforeach;

    endif;

    return $courses;
}
