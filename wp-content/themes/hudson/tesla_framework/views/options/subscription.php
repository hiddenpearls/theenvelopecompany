<div class="subscribers_container">
  <?php
  $subscriptions = tt_get_subscriptions();
  if ( !empty($subscriptions) ) :
    foreach ( $subscriptions as $key => $subscription ) : if($key > 20) break;//show first 20 subscribers?>
      <div class="subscriber">
          <p><i><?php echo esc_html ( $key ) ?></i></p>
      </div>
    <?php endforeach;?>
      <p>Total : <?php echo count($subscriptions); ?></p>
    <div>
      <a href="#" class="tt_btn clear"><span class="erase"><?php _e('Clear Subscription List','TeslaFramework') ?></span></a>
    </div>
    <div class="tt_option_title"><span><?php _e('Export subscription list','TeslaFramework') ?></span></div>
    <a href="<?php echo admin_url( 'admin.php?page=' . THEME_NAME . '_options&tt-export-subsc&nonce=' . wp_create_nonce( 'tt-export-subscr' ) ) ?>" class="tt_btn tt-export-subsc"><span class="tab_delimited"><?php _e('Tab Delimited TXT','TeslaFramework') ?></span></a> 
    <!-- <a href="<?php echo admin_url( 'admin.php?page=' . THEME_NAME . '_options&tt-export-subsc&nonce=' . wp_create_nonce( 'tt-export-subscr' ) ) ?>" class="tt_btn tt-export-subsc"><span class="csv"><?php _e('CSV','TeslaFramework') ?></span></a> -->
  <?php else: ?>
    <p><?php _e('No subscribers yet...','TeslaFramework') ?></p>
  <?php endif; ?>
</div>