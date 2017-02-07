<div class="modal fade" id="salePopup" tabindex="-1" role="dialog" aria-labelledby="salePopupLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h2 class="modal-title heading-text id="salePopupLabel"><?php the_field('popup_title', 'option'); ?></h2>
      </div>
      <div class="modal-body">
        <p><?php the_field('popup_text', 'option'); ?></p>
      </div>
      <div class="modal-footer">
        <?php if( have_rows('popup_call_to_action', 'option') ) : ?>
          <?php while( have_rows('popup_call_to_action', 'option') ) : the_row(); ?>
            <a href="<?php the_sub_field('button_url', 'ooption'); ?>" class="btn orange-btn big">
              <?php the_sub_field('button_label', 'option'); ?>
            </a>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>