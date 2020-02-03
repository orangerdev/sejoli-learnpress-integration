<?php

if(!isset($products)) :
    $course   = \LP_Global::course();
    $products = sejolilp_get_products($course->get_id());
endif;

?>
<div class='learn-press-content-protected-message'>
    <p><?php _e('Anda belum bisa untuk mengakses konten ini. Harus melakukan pembelian terlebih dahulu', 'sejoli'); ?>
</div>
<?php
require 'purchase-buttons.php';
