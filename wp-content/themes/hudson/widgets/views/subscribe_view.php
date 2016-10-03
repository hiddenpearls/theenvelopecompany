<div class="subscribe span5">
    <h1 id="subscribe_title" class="subscribe_header tc1"><?php echo $widget_subscribe_heading; ?></h1>
    <div class="inputs bg1 clearfix">
        <form id="subscribe" method="post">
            <input class="email tc9 left" name="email" 
                   data-tt-subscription-required data-tt-subscription-type="email" placeholder="<?php echo $widget_email_placeholder; ?>"/>
            <div class="button bg8 right" onclick="jQuery('#subscribe').submit();">
                <i class="send_icon sp dib"></i>
            </div>
        </form>
    </div>
</div>