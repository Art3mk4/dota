<?php
get_header(); ?>
<header id="buy-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div id="betting">
                    <img class="img-responsive center-block" src="<?php echo get_template_directory_uri()?>/images/esport-betting.png" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="text-center">
                <div id="papyrus" class="col-lg-8 col-centered">
                    <div class="col-md-6">
                        <div id="papyrus-text-thanks">
                            <?php if (have_posts()):?>
                                <?php
                                    while(have_posts()): the_post();
                                        the_content();
                                    endwhile;
                                ?>
                            <?php endif;?>
                        </div>
                        <div id="download-3">
                            <a href="<?php echo get_permalink(7)?>" class="btn text-center download-button">
                                Download now
                            </a>
                        </div>
                        <div id="social-buttons">
                            <div id="social-buttons-text">
                                Join us:
                            </div>
                            <ul id="social-links">
                                <li class="facebook"><a class="fa fa-facebook"></a></li>
                                <li class="twitter"><a class="fa fa-twitter"></a></li>
                                <li class="instagram"><a class="fa fa-instagram"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 hidden-sm hidden-xs">
                        <div id="esports-guide">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-8.png" />
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div id="buy-main" class="text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-centered">
                
            </div>
        </div>
    </div>
</div>
<footer>
    <div id="buy-footer"></div>
</footer>
<?php get_footer(); ?>