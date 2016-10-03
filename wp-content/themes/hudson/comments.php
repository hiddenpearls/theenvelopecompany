<?php
/**
 * The template for displaying Comments.
 *
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required())
    return;

/* If there are no comments and comments are closed, let's leave a note.
 * But we only want the note on posts and pages that had comments in the first place.
 */
if (comments_open() || get_comments_number()) :
    ?>
    <div id="comments" class="comment-box comments comments-area">
        <?php
        $commenter = wp_get_current_commenter();
        comment_form(array(
            'fields' => array(
                'author' => '<input id="author" name="author" type="text" class="h_line" placeholder="Name *" value="' . esc_attr($commenter['comment_author']) . '" size="30">',
                'email' => '<input id="email" name="email" type="email" class="h_line" placeholder="E-mail *" value="' . esc_attr($commenter['comment_author_email']) . '" size="30">',
                'url' => '<input id="url" name="url" type="text" class="h_line" placeholder="Website" value="' . esc_attr($commenter['comment_author_url']) . '" size="30">'
            ),
            'comment_field' => '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="h_text" placeholder="Comment *"></textarea>',
            'label_submit' => 'Add Comment',
            'comment_notes_after' => ''
        ));
        ?>

        <div class="comments_all">


            <h2>existing reviews (<?php echo get_comments_number(); ?>)</h2>

            <ul class="commentlist comment-items">
                <?php wp_list_comments(array('callback' => 'tesla_comment_cb', 'style' => 'ul')); ?>
            </ul><!-- .commentlist -->

            <nav id="comment-nav-below" class="navigation" role="navigation">
                <div class="nav-previous"><?php previous_comments_link(__('&larr; Older Comments', 'hudson')); ?></div>
                <div class="nav-next"><?php next_comments_link(__('Newer Comments &rarr;', 'hudson')); ?></div>
                <div class="clear"></div>
            </nav>

        </div>
    </div>
<?php endif; ?>