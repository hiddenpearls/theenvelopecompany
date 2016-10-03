<?php $columns = (isset($columns) && $columns != '12') ? 'span'.$columns : 'span12'; ?>
<?php if(isset($as_shortcode)): ?> <ul class="latest_posts_shortcode"> <?php endif; ?>
    <li class="item posts <?php echo $columns; ?> <?php echo $class; ?>">

        <ul id="tabs" class="nav nav-tabs">
            <li class="tab active">
                <a href="#latest" data-toggle="tab"><?php _e('Latest Posts', THEME_NAME); ?></a>
            </li>
            <li class="tab">
                <a href="#popular" data-toggle="tab"><?php _e('Popular', THEME_NAME); ?></a>
            </li>
        </ul>

        <div id="tabs_content" class="tab-content">
            <div class="tab-pane fade in active" id="latest">
                <ul class="posts_wr">
                    <?php
                    $rec_args = array(
                        'showposts' => $this->posts_number,
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                    );
                    $recent_posts = new WP_Query($rec_args);
                    ?>
                    <?php if ($recent_posts->have_posts()): while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                            <li class="item">
                                <div class="content row-fluid">
                                    <?php $meta_class = 'span10 offset1'; if (has_post_thumbnail()): $meta_class = 'span8'; ?>
                                        <figure class="span3 offset1">
                                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(100,100)); ?></a>
                                        </figure>
                                    <?php endif; ?>
                                    <div class="meta <?php echo $meta_class; ?>">
                                        <h4 class="title tc14 bold"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <h5 class="author">By <?php the_author(); ?></h5>
                                        <h5 class="date"><?php the_date('j F Y'); ?></h5>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; endif; ?>
                    <?php wp_reset_query(); ?>

                </ul>
            </div>
            <div class="tab-pane fade" id="popular">
                <ul class="posts_wr">

                    <?php
                    $pop_args = array(
                        'showposts' => $this->posts_number,
                        'post_status' => 'publish',
                        'orderby' => 'comment_count',
                        'order' => 'DESC',
                        'ignore_sticky_posts' => 1,
                    );
                    $popular_posts = new WP_Query($pop_args);
                    ?>
                    <?php if ($popular_posts->have_posts()): while ($popular_posts->have_posts()) : $popular_posts->the_post(); ?>
                    <li class="item">
                        <div class="content row-fluid">
                            <?php $meta_class = 'span10 offset1'; if (has_post_thumbnail()): $meta_class = 'span8'; ?>
                            <figure class="span3 offset1">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(100,100)); ?></a>
                            </figure>
                            <?php endif; ?>
                            <div class="meta <?php echo $meta_class; ?>">
                                <h4 class="title tc14 bold"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <h5 class="author">By <?php the_author(); ?></h5>
                                <h5 class="date"><?php the_date('j F Y'); ?></h5>
                            </div>
                        </div>
                    </li>
                    <?php endwhile; endif; ?>
                    <?php wp_reset_query(); ?>

                </ul>
            </div>
        </div>

    </li>
<?php if(isset($as_shortcode)): ?> </ul> <?php endif; ?>