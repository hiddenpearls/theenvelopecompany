<section class="products-section" id="products-section">
    <div class="container">
        <div class="row">
          <h2 class="text-center heading-text">Our Products</h2>
            <?php

              $taxonomy     = 'product_cat';
              //$orderby      = 'name';  
              $show_count   = 0;      // 1 for yes, 0 for no
              $pad_counts   = 0;      // 1 for yes, 0 for no
              $hierarchical = 0;      // 1 for yes, 0 for no  
              $title        = '';  
              $empty        = 0;

              $args = array(
                     'taxonomy'     => $taxonomy,
                     //'orderby'      => $orderby,
                     //'order'        => 'ASC',
                     'show_count'   => $show_count,
                     'pad_counts'   => $pad_counts,
                     'hierarchical' => $hierarchical,
                     'title_li'     => $title,
                     'hide_empty'   => $empty
              );
             $all_categories = get_categories( $args );
             foreach ($all_categories as $cat) {
                if($cat->category_parent == 0) {
                    $category_id = $cat->term_id;   
                    echo '<div class="col-md-4 product-category-extract button-down home">';    
                      $thumbnail_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
                      // get the image URL for child category
                      $image = wp_get_attachment_url($thumbnail_id);
                      // print the IMG HTML for child category
                      echo "<img src='".$image."' alt='' />";
                      echo  '<span>'.$cat->name.'</span>';
                      echo '<a class="orange-btn btn small" href="'. get_term_link($cat->slug, 'product_cat') .'">Shop';
                      echo '</a></div>';
                }   
            }
            ?>
        </div>
    </div>
</section>
<?php if( get_field('panel_title') ): ?>
<section class="panel-wbgd submit-file-section" style="background-image: url('<?php the_field('panel_background_image'); ?>');">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-4">
                <h2><?php the_field('panel_title'); ?></h2>
                <p><?php the_field('panel_text'); ?></p>
                <a href="<?php the_field('panel_button_url'); ?>" class="white-btn btn big">
                    <?php the_field('panel_button_label'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<section class="sand-bgd contact-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4 contact-information">
                <h2><?php the_field('contact_us_title'); ?></h2>
                <h3><?php the_field('contact_us_subtitle'); ?></h3>
                <p class="address"><?php the_field('contact_address_information') ?></p>
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
                //gravity_form( 3, false, false, false, '', false );
                echo do_shortcode(get_field('contact_form_shortcode'));
                ?>
            </div>
        </div>
    </div>
</section>
<?php  
  //Get sale banner if enabled
  if( get_field('sale_banner_enabled') ) {
?>
    <section class="panel-wbgd sale-banner-section" style="background-image: url('<?php the_field('sale_banner_background_image'); ?>');">
      <div class="container">
          <div class="row">
              <div class="col-md-6 col-md-offset-4">
                  <h2 class="heading-text"><?php the_field('sale_banner_title'); ?></h2>
                  <p><?php the_field('sale_banner_text'); ?></p>
                  <?php if( have_rows('sale_banner_calls_to_action') ) : ?>
                    <?php while( have_rows('sale_banner_calls_to_action') ) : the_row(); ?>
                      <a href="<?php the_sub_field('button_url'); ?>" class="white-btn btn big" title="<?php the_sub_field('button_label'); ?>">
                        <?php the_sub_field('button_label'); ?>
                      </a>
                    <?php endwhile; ?>
                  <?php endif; ?>
              </div>
          </div>
      </div>
      
    </section>
<?php
  } else {
    //Banner disabled. Do nothing
  }
?>

<?php 
    //show popup modal if enabled from the backend
    if( get_field('enable_popup', 'option') ){
        get_template_part('templates/partials/sale', 'popup');
    }
?>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
<script>
  jQuery(document).ready(function( $ ) {
    // Sale Popup show after time interval
    var modalPresent = $('#salePopup').length;
    if( modalPresent >= 1){
      var openModal = function(){
        $('#salePopup').modal('toggle');
      }
      setTimeout(function(){
        openModal();
      }, 3000);
    }
  });
</script>