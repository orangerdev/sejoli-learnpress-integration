<?php
/**
 * Get products that related to learnpress course
 * @since   1.0.0
 * @param   integer         $check_course_id    (Optional) ID of course
 * @return  array|false     Will return false if there is no product for given course id or no related product
 */
function sejolilp_get_products($check_course_id = 0) {
    global $wpdb;

    $data    = array();
    $results = $wpdb->get_results(
                "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key LIKE '_learnpress_course|||_|id'"
               );

    foreach((array) $results as $row) :

        $product_id = (int) $row->post_id;
        $course_id  = (int) $row->meta_value;

        if(!isset($data[$course_id])) :
            $data[$course_id] = array();
        endif;

        $data[$course_id][] = $product_id;

    endforeach;

    // check if there is related product to giver course ID
    if(0 < $check_course_id) :
        return (!isset($data[$check_course_id])) ? false : $data[$check_course_id];
    endif;

    // return false if there is no related product
    if(0 === count($data)) :
        return false;
    endif;

    return $data;
}
