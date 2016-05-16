<?php
get_header();
?>
<header id="buy-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div id="betting">
                    <a href="<?php echo esc_url( home_url('/') )?>">
                    <img class="img-responsive center-block" src="<?php echo get_template_directory_uri()?>/images/esport-betting.png" />
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="text-center">
                    <div id="papyrus">
                        <div class="row">
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
                                    <a href="<?php echo get_permalink(7) . '?download_file=yes'?>" class="btn text-center download-button">
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
    </div>
</header>
<div id="buy-main" class="text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-centered">
                <div id="can-text" class="color-white">
                    You can register on one of the following betting services 
                        and start betting right now!
                </div>
            </div>
            <div class="col-centered hidden-sm hidden-xs">
                <table>
                    <tr class="odd-service row">
                        <th class="col-md-3 logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-15-.png" />
                        </th>
                        <th class="col-md-9 color-white service-text">
                            Pinnacle - the most popular betting office
                        </th>
                        <th class="col-md-2">
                            <a class="btn register-button color-white">Register Now</a>
                        </th>
                    </tr>
                    <tr class="row even-service">
                        <th class="col-md-3 logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-16.png" />
                        </th>
                        <th class="col-md-9 color-white service-text">
                            Betway - one of the biggest European betting offices 
    for eSports and poker
                        </th>
                        <th class="col-md-2">
                            <a class="btn register-button color-white">Register Now</a>
                        </th>
                    </tr>
                    <tr class="odd-service row">
                        <th class="col-md-3 logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-17.png" />
                        </th>
                        <th class="col-md-9 color-white service-text">
                            Egamingbets - specializes in eSports betting
                        </th>
                        <th class="col-md-2">
                            <a class="btn register-button color-white">Register Now</a>
                        </th>
                    </tr>
                    <tr class="row even-service">
                        <th class="col-md-3 logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-18.png" />
                        </th>
                        <th class="col-md-9 color-white service-text">
                            Bet365 - this UK service currently has more 
than 18 million registered users
                        </th>
                        <th class="col-md-2">
                            <a class="btn register-button color-white">Register Now</a>
                        </th>
                    </tr>
                    <tr class="odd-service row">
                        <th class="col-md-3 logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-19.png" />
                        </th>
                        <th class="col-md-9 color-white service-text">
                            William Hill - now it is one of the biggest betting offices in the UK
                        </th>
                        <th class="col-md-2">
                            <a class="btn register-button color-white">Register Now</a>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="visible-sm visible-xs col-xs-12">
                <div class="odd-service row">
                    <div class="col-xs-5 vcenter">
                        <div class="logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-15-.png" />
                        </div>
                        <div class="service-text-mobile color-white">
                            Pinnacle - the most popular betting office
                        </div>
                    </div>
                    <div class="btn-wrapper col-xs-5 vcenter">
                        <a class="btn register-button-mobile color-white">Register Now</a>
                    </div>
                </div>
                <div class="odd-service row">
                    <div class="col-xs-5 vcenter">
                        <div class="logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-16.png" />
                        </div>
                        <div class="service-text-mobile color-white">
                            Betway - one of the biggest European betting offices 
    for eSports and poker
                        </div>
                    </div>
                    <div class="btn-wrapper col-xs-5 vcenter">
                        <a class="btn register-button-mobile color-white">Register Now</a>
                    </div>
                </div>
                <div class="odd-service row">
                    <div class="col-xs-5 vcenter">
                        <div class="logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-17.png" />
                        </div>
                        <div class="service-text-mobile color-white">
                            Egamingbets - specializes in eSports betting
                        </div>
                    </div>
                    <div class="btn-wrapper col-xs-5 vcenter">
                        <a class="btn register-button-mobile color-white">Register Now</a>
                    </div>
                </div>
                <div class="odd-service row">
                    <div class="col-xs-5 vcenter">
                        <div class="logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-18.png" />
                        </div>
                        <div class="service-text-mobile color-white">
                            Bet365 - this UK service currently has more 
    than 18 million registered users
                        </div>
                    </div>
                    <div class="btn-wrapper col-xs-5 vcenter">
                        <a class="btn register-button-mobile color-white">Register Now</a>
                    </div>
                </div>
                <div class="odd-service row">
                    <div class="col-xs-5 vcenter">
                        <div class="logo">
                            <img class="img-responsive" src="<?php echo get_template_directory_uri()?>/images/-19.png" />
                        </div>
                        <div class="service-text-mobile color-white">
                            William Hill - now it is one of the biggest betting offices in the UK
                        </div>
                    </div>
                    <div class="btn-wrapper col-xs-5 vcenter">
                        <a class="btn register-button-mobile color-white">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer>
    <div id="footer-bottom">
        <div id="footer-text"class="text-center">
            <p>© 2015 eSports Betting Club</p>
        </div>
    </div>
</footer>
<?php get_footer(); ?>