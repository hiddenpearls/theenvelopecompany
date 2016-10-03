<?php
if (!defined('ABSPATH'))
    return;
global $woocommerce;
?>
<div class="login_form sing_in_form">
    <h4><?php _e('sign in', 'hudson'); ?></h4>

    <form action="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" method="post" class="login">
        <input type="text" class="sing_in_line" name="username" id="username" placeholder="<?php _e('Username', 'hudson'); ?>" />
        <input type="password" class="sing_in_line" name="password" id="password" placeholder="<?php _e('Password', 'hudson'); ?>" />
        <?php wp_nonce_field( 'woocommerce-login' ); ?>
        <p><a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e('Lost Password?', 'woocommerce'); ?></a></p>
        <div class="sing_in_form_footer">
            <input type="submit" class="sing_in_button" name="login" value="<?php _e('Login', 'woocommerce'); ?>">
        </div>
    </form>

</div>

<div class="register_form sing_in_form">
    <h4><?php _e('register now', 'hudson'); ?></h4>

    <form action="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" method="post" class="register">

        <?php if (get_option('woocommerce_registration_email_for_username') == 'no') : ?>
            <input type="text" class="sing_in_line" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" placeholder="<?php _e('Username', 'woocommerce'); ?>" />
        <?php endif; ?>

        <input type="email" class="sing_in_line" placeholder="<?php _e('Email', 'woocommerce'); ?>" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />

        <input type="password" class="sing_in_line" placeholder="<?php _e('Password', 'woocommerce'); ?>" name="password" id="reg_password" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />

        <input type="password" placeholder="<?php _e('Re-enter password', 'woocommerce'); ?>" class="sing_in_line" name="password2" id="reg_password2" value="<?php if (isset($_POST['password2'])) echo esc_attr($_POST['password2']); ?>" />

        <!-- Spam Trap -->
        <div style="left:-999em; position:absolute;"><label for="trap"><?php _e('Anti-spam','woocommerce') ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

        <?php do_action('register_form'); ?>

        <div class="sing_in_form_footer">
            <?php wp_nonce_field( 'woocommerce-register' ); ?>
            <input type="submit" class="sing_in_button" name="register" value="<?php _e('Register', 'woocommerce'); ?>" />
        </div>

    </form>

</div>