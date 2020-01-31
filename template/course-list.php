<?php sejoli_header(); ?>
<h2 class="ui header"><?php _e('Kelas', 'sejoli'); ?></h2>
<?php
$course_ids   = learn_press_get_all_courses();
$user_courses = sejolilp_get_user_purchased_courses();

?><div class="ui three column doubling stackable cards item-holder masonry grid"><?php
foreach( (array) $course_ids as $course_id ) :
    $course          = learn_press_get_course( $course_id);
    $curd            = new LP_Course_CURD();
    $number_sections = $curd->count_sections( $course_id );

    ?>
    <div class="column">
        <div class="ui fluid card">
            <div class="image">
                <?php echo $course->get_image(); ?>
            </div>
            <div class="content">
                <span class='left floated'>
                    <a href='#' class='section'>
                        <i class='book icon'></i>
                        <?php
                        printf(
                            _n(
                                '%s bagian',
                                '%s bagian',
                                $number_sections,
                                'sejolilp'
                            ),
                            $number_sections
                        );
                        ?>
                    </a>
                </span>
                <span class='right floated'>
                    <a href='#' class='lesson'>
                        <i class='pencil icon'></i>
                        <?php
                        printf(
                            _n(
                                '%s ',
                                '%s materi',
                                $course->count_items(LP_LESSON_CPT),
                                'sejolilp'
                            ),
                            $course->count_items(LP_LESSON_CPT)
                        );
                        ?>
                    </a>
                </span>
            </div>
            <div class="content">
                <h3 class='header'><?php echo $course->get_title(); ?></h3>
                <div class="description">
                    <?php echo $course->get_content(''); ?>
                </div>
            </div>
            <div class="extra content">
                <a href='#'>
                    <i class="users icon"></i>
                    <?php
                    printf(
                        _n(
                            '%s peserta',
                            '%s peserta',
                            $course->get_users_enrolled(),
                            'sejolilp'
                        ),
                        $course->get_users_enrolled()
                    );
                    ?>
                </a>
            </div>
            <a href='<?php echo $course->get_permalink(); ?>' class="ui bottom attached button">
                <?php _e('Lihat Kelas', 'sejoli'); ?>
            </a>
        </div>
    </div><?php

endforeach;
?></div><?php

wp_reset_query();

sejoli_footer();
