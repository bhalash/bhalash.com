<?php

/**
 * Theme Comment Functions
 * -----------------------------------------------------------------------------
 * @category   PHP Script
 * @package    Sheepie
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU GPL v3.0
 * @version    1.0
 * @link       https://github.com/bhalash/sheepie
 */

function sheepie_theme_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>

    <li <?php comment_class(['comment', 'vspace--double', 'comments__comment']); ?> id="comment--<?php comment_ID() ?>">
        <?php sheepie_avatar_background_html($comment, 130, 'comment__avatar'); ?>
        <div class="comment__body">
            <header class="comment__header">
                <?php

                printf('<span class="%s comment__meta">%s %s </span>',
                    'comment-author-link font-size--small',
                    get_comment_author_link(),
                    __('on', 'sheepie')
                );

                printf('<span class="%s comment__meta"><time datetime="%s">%s</time></span>',
                    'post-date',
                    get_comment_date('Y-M-d H:i'),
                    get_comment_date(get_option('date_format'))
                );

                ?>
                <?php if (is_user_logged_in()) : ?>
                    <span class="meta">
                        <?php edit_comment_link(__('edit', 'sheepie'), ' / ', ''); ?>
                    </span>
                <?php endif; ?>
            </header>

            <div class="comment__content meta">
                <?php if (!$comment->comment_approved) : ?>
                    <p class="comment__unapproved">
                        <?php _e('Your comment is awaiting approval.', 'sheepie'); ?>
                    </p>
                <?php else : ?>
                    <?php comment_text(); ?>
                <?php endif; ?>
            </div>
        </div>
    </li>

    <?php
}

/**
 * Generate Custom Commentform Input HTML
 * -------------------------------------------------------------------------
 * @param   array       $input_fields   Labels for input fields.
 * @param   string      $input_html     Raw HTML for input fields.
 * @param   array       $input_fields   Raw HTML joined with labels.
 */

function sheepie_commentform_fields($input_fields = null, $input_html = null) {
    // Template input for name, email and URL.
    $input_html = $input_html ?: '<input class="comments__input %s-name font-size--small" id="%s" name="%s" placeholder="%s" type="text" required="required">';

    $input_fields = $input_fields ?: [
        'author' => __('Name*', 'sheepie'),
        'email' => __('Email*', 'sheepie'),
        'url' => __('Website', 'sheepie')
    ];

    foreach ($input_fields as $field => $label) {
        $input_fields[$field] = sprintf($input_html, $field, $field, $field, $label);
    }

    return $input_fields;
}

/**
 * Wrap Comment Fields in Elements
 * -------------------------------------------------------------------------
 * Wrap comment form fields in <div></div> tags.
 * @link http://wordpress.stackexchange.com/a/172055
 */

function sheepie_wrap_comment_fields_before() {
    printf('<fieldset class="comments__inputs vspace--full">');
}

function sheepie_wrap_comment_fields_after() {
    printf('</fieldset>');
}

add_action('comment_form_before_fields', 'sheepie_wrap_comment_fields_before');
add_action('comment_form_after_fields', 'sheepie_wrap_comment_fields_after');

?>
