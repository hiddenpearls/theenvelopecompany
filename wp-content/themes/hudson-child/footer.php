</div>
<!-- =====================================
    START FOOTER -->
    
  <div class="clear"></div>  
<div class="footer">
    <div class="container">
        <div class="row">
            <?php
            $cols = array(2, 2, 4, 4);
            foreach (range(1, 4) as $i)
                if (is_active_sidebar('sidebar-footer-' . $i)) {
                    ?>
                    <div class="span<?php echo $cols[$i-1]; ?>">
                        <?php
                        dynamic_sidebar('sidebar-footer-' . $i);
                        ?>
                    </div>
                    <?php
                }
            ?>                       
        </div>
        <div class="row">
            <div class="span8">
                &nbsp;
            </div>
            <div class="span4">
                <div class="copyright"><span>Designed by <a href="http://YouAreHereMedia.com" target="_blank">YouAreHereMedia.com</a></span> <?php echo _go('copyright_message'); ?> 
                               
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

<!-- =====================================
START SCRIPTS -->
<?php wp_footer(); ?>
</body>
</html>