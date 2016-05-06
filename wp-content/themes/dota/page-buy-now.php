<?php
get_header(); ?>
<?php if (have_posts()):?>
    <?php
        while(have_posts()): the_post();
            //the_title();
            the_content();
        endwhile;
    ?>
<?php endif;?>
<?php get_footer(); ?>