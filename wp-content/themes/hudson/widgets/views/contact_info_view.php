<div class="contact_info span4">
    <?php if(!empty($widget_contact_heading)) : ?>
        <h1 class="contact_header tc1"><?php print $widget_contact_heading; ?></h1>
    <?php endif; ?>
    <ul class="items_wrapper">
        <?php if(_go('contact_email')) : ?>
            <li class="item email">
                <span class="icon dib"><i class="sp dib"></i></span>
                <span class="content bold tc8"><?php echo _go('contact_email') ?></span>
            </li>
        <?php endif; ?>
        <?php if(_go('contact_phone')) : ?>
            <li class="item phone">
                <span class="icon dib"><i class="sp dib"></i></span>
                <span class="content bold tc8"><?php echo _go('contact_phone') ?></span>
            </li>
        <?php endif; ?>
    </ul>
</div>