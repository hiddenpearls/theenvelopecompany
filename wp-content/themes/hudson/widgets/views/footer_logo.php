<?php $src = (wp_get_attachment_url($footer_logo_img)) ? wp_get_attachment_url($footer_logo_img) : ''; ?>
<div class="logo span2">
    <a href="<?php echo home_url() ?>"><img src="<?php echo esc_attr($src); ?>" alt="Logo"></a>
</div>