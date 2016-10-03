            <div class="clear"></div>
      </div>
    </div>   
<div style="clear:both;"></div>
	<div style="width:1040px; margin:0 auto;">
	<div class="page-bottom">
    	<div class="page-bottom-box" id="box1">
            	<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-4' ); ?>
				<?php endif; ?>
        </div>

    	<div class="page-bottom-box" id="box2">
            	<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-5' ); ?>
				<?php endif; ?>
        </div>

    	<div class="page-bottom-box" id="box3">
            	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-6' ); ?>
				<?php endif; ?>
        </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <div style="width:1040px; margin:0 auto;">        
	<div class="page-footer">
            	<?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>
	   	  <div class="page-footer-text">
      					<?php dynamic_sidebar( 'sidebar-7' ); ?>
	      </div>
				<?php endif; ?>
	  <div class="page-footer-top"></div>
      <div class="page-footer-copyright">
            	<?php if ( is_active_sidebar( 'sidebar-8' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-8' ); ?>
				<?php endif; ?>
      </div>
    </div>    
    </div>


</div><!-- page wrapper -->
<?php wp_footer(); ?>
</body>
</html>
