<section class="sand-bgd contact-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4 contact-information">
                <h2><?php the_field('company');?></h2>
                <p class="address"><?php the_field('address_1');?><br/><?php echo get_field('city').', '.get_field('state_province_region')." ".get_field('zip');?></p>
                <p class="heading-text italic">Customer Service</p>
                <ul>
                    <?php 
                        $phone = get_field('toll_free_phone', 'options'); 
                        $cleaned_phone = $phone;
                        $cleaned_phone = str_replace(array('(', ')', '-', ' '), "", $cleaned_phone);
                    ?>
                    <li><a href="tel:<?php echo $cleaned_phone; ?>"><i class="fa fa-phone"></i><strong>Toll Free: </strong><?php echo $phone; ?></a></li>
                    <?php 
                        $phone = get_field('regular_phone_number', 'options');; 
                        $cleaned_phone = $phone;
                        $cleaned_phone = str_replace(array('(', ')', '-', ' '), "", $cleaned_phone);
                    ?>
                    <li><a href="tel:<?php echo $cleaned_phone; ?>"><i class="fa fa-phone-square"></i><strong>Phone: </strong><?php echo $phone; ?></a></li>
                    <?php 
                        $phone = get_field('fax_number', 'options'); 
                        $cleaned_phone = $phone;        
                        $cleaned_phone = str_replace(array('(', ')', '-', ' '), "", $cleaned_phone);
                    ?>
                    <li><a href="tel:<?php echo $cleaned_phone; ?>"><i class="fa fa-fax"></i><strong>Fax: </strong><?php echo $phone; ?></a></li>
                </ul>
            </div>
            <div class="col-md-8 contact-form">
                <p><?php the_field('contact_us_form_title'); ?></p>
                <p class="orange heading-text"><?php the_field('contact_us_form_subtitle'); ?></p>
                <?php
                //gravity_form( 10, false, false, false, '', false );
                echo do_shortcode('[gravityform id=10 title=false description=true ajax=true tabindex=1]');
                //echo do_shortcode(get_field('contact_form_shortcode'));
                ?>
            </div>
        </div>
    </div>
</section>