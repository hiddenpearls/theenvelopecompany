</div>
<!-- =====================================
    START FOOTER -->
<div class="footer">
    <div class="container">
        <div class="row">
            <?php
            $cols = array(2, 2, 4, 4);
            foreach (range(1, 4) as $i)
                if (is_active_sidebar('sidebar-footer-' . $i)) {
                    ?>
                    <div class="span<?php echo esc_attr($cols[$i-1]); ?>">
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
                <div class="copyright">
                    <span>
                        <?php _e( 'Designed by ', 'hudson' ) ?>
                        <a href="http://teslathemes.com" target="_blank">TeslaThemes</a>,

                        Supported by <a href="http://wpmatic.io">WPmatic</a>
                    </span>
                    <?php echo _go('copyright_message'); ?>
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