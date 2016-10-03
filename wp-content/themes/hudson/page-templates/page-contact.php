<?php
/**
 * Template Name: Contact Page Template
 *
 * Description: Contact page for your website.
 *
 * @package WordPress
 * @subpackage Hudson
 * @since Hudson 1.0
 */
get_header();
extract(_gall());
?>

<div id="primary" class="site-content">
    <div id="content" role="main">

        <?php while (have_posts()) : the_post(); ?>
            <!-- =====================================
    START CONTENT -->
            <div class="content">
                <div class="container">
                    <div class="path">
                        <a href="<?php echo site_url('/'); ?>"><?php _e('Home', 'hudson'); ?></a> / <?php _e('Contact', 'hudson'); ?>
                    </div>
                    <div class="row">
                        <div class="span9">
                            <?php tt_gmap('contact_map', 'map-canvas'); ?>
                            <?php
                            if (isset($contact_form)) {
                                $sent_contact_message = Tesla_Contact::checkNsend('_contact_form', _go('contact_email'), array('contact_name', 'contact_email', 'contact_website', 'contact_message'));
                                ?>
                                <h1 class = "h_title">
                                    <?php the_title() ?>
                                </h1>
                                <div class = "h_contact_form">
                                    <?php if (!empty($sent_contact_message)) { ?>
                                        <div class="alert alert-success">
                                            <?php _e('Message sent successfully.', 'hudson'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($contact_title)) { ?>
                                        <h2>
                                            <?php echo $contact_title; ?>
                                        </h2>
                                    <?php } ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="_contact_form" value="sent" />
                                        <input type = "text" name="contact_name" class = "h_line" placeholder = "<?php _e('Name', 'hudson'); ?>" required />
                                        <input type = "email" name="contact_email" class = "h_line" placeholder = "<?php _e('E-mail', 'hudson'); ?>" required />
                                        <input type = "text" name="contact_website" class="h_line" placeholder = "<?php _e('Web site', 'hudson'); ?>" />
                                        <textarea name="contact_message" class = "h_text" placeholder = "<?php _e('Message', 'hudson'); ?>"></textarea>
                                        <input type = "submit" value = "send" class = "h_button" />
                                    </form>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <div class="span3">
                            <?php get_sidebar('contact-page'); ?>
                        </div>                
                    </div>
                </div>
                <div class="clear"></div>
            </div>

        <?php endwhile; // end of the loop.     ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>