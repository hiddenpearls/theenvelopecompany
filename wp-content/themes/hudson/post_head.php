<?php
global $post;
$post_meta = get_post_custom();
?>
<div <?php post_class(); ?>>
    <div class="post_img_vid">
        <?php if ((empty($post_meta[THEME_NAME . '_post_head_type']) || @$post_meta[THEME_NAME . '_post_head_type'][0] == 'image') && has_post_thumbnail()) { ?>
            <?php if (!is_singular()) { ?>    
                <a href="<?php the_permalink(); ?>">
                <?php } ?>
                <?php the_post_thumbnail('large'); ?>
                <?php if (!is_singular()) { ?>    
                </a>
            <?php } ?>
        <?php } elseif (@$post_meta[THEME_NAME . '_post_head_type'][0] == 'embed_code') { ?>
            <?php print @$post_meta[THEME_NAME . '_embed_code'][0]; ?> 
        <?php } ?>
    </div>
    <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
    <ul class="post_info">
        <li class="post_info_1"><?php the_author_link(); ?></li>
        <li class="post_info_2"><a href="<?php the_permalink(); ?>"><?php echo get_the_date(); ?></a></li>
        <li class="post_info_3"><?php _e('Categories:', 'hudson'); ?> <?php the_category(); ?></li>
        <li class="post_info_4"><a href="<?php comments_link(); ?>"><?php _e('Comments', 'hudson'); ?> (<?php comments_number('0', '1', '%'); ?>)</a></li>
    </ul>
    <div class="entry-content">
        <?php
        if (is_search())
            the_excerpt();
        else {
            if (strpos($post->post_content, '<!--more') === FALSE && !is_singular())
                the_excerpt();
            else
                the_content(__('Continue reading <span class="meta-nav">&raquo;</span>', 'hudson'));
        }
        ?>

        <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'hudson'), 'after' => '</div>')); ?>
        <?php if (has_tag()) { ?>
            <div class="post_tags">
                <?php the_tags(); ?>
            </div>
        <?php } ?>
    </div>                            
</div>