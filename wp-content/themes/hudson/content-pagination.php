<div class="h_pagination woocommerce_pagination">
    <?php
    $big = 999999999;
    global $wp_query;
    $paged = $wp_query->query_vars['paged'];
    $paged = !$paged ? 1 : $paged;
    $args = array(
        'show_all' => FALSE,
        'end_size' => 1,
        'total' => $wp_query->max_num_pages,
        'current' => $paged,
        'mid_size' => 2,
        'prev_next' => True,
        'prev_text' => __('Previous', 'hudson'),
        'next_text' => __('Next', 'hudson'),
        'type' => 'list',
        'add_args' => False,
        'add_fragment' => '',
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '/page/%#%',
    );
    echo paginate_links($args);
    ?>                                              
</div>