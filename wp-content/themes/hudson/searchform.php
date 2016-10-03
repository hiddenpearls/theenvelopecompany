<form role="search" method="get" id="searchform" action="<?php echo esc_url(home_url('/')) ?>" class="search_line_all_form">
    <input class="search_line_all_button" type="submit" value=""/>
    <input name="s" id="s"  class="search_line_all" type="text" placeholder="<?php esc_attr_e('Search','hudson'); ?>" value="<?php echo get_search_query(); ?>" />                                
</form>