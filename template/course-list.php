<?php sejoli_header(); ?>
<h2 class="ui header"><?php _e('Kelas', 'sejoli'); ?></h2>
<?php
$course_ids   = learn_press_get_all_courses();
$user_courses = sejolilp_get_user_purchased_courses();

foreach( (array) $course_ids as $course_id ) :
    $course          = learn_press_get_course( $course_id);
    $curd            = new LP_Course_CURD();
    $number_sections = $curd->count_sections( $course_id );
    __print_debug(
        $course->get_title(),
        $course->get_image(),
        $course->get_permalink(),
        $course->get_users_enrolled(),
        $course->get_students_html(),
        $course->get_instructor(),
        $number_sections,
        $course->count_items(LP_LESSON_CPT),
        $course->count_items(LP_QUIZ_CPT)
    );
endforeach;

sejoli_footer();
