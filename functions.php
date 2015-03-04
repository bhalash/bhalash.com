<?php
    if (!isset($content_width)) {
        $content_width = 600;
    }

    /*
     * Header Social Meta Information
     * ------------------------------
     * We tried a few different existing plugins for this, but:
     * 
     * 1. They were overly-complex for lay users to configure.
     * 2. They worked in an inconsistent and buggy manner, at best.
     * 3. They chosen one occasionally inserted annoying upsell banners on admin
     *    pages.
     */

    function social_meta() {
        facebook_meta();
        twitter_meta();
    }

    $fallback = array(
        'publisher' => 'http://www.bhalash.com',
        'image' => get_template_directory_uri() . '/images/polaris.jpg',
        'description' => get_bloginfo('description'),
        'twitter' => '@bhalash'
    );

    function twitter_meta() {
        /* Social Meta Information for Twitter
         * ------------------------------------
         * This /should/ be all of the relevant information for Twitter. */
        global $fallback, $post;
        $the_post = get_post($post->ID);
        setup_postdata($the_post);

        $site_meta = array(
            'twitter:card' => 'summary',
            'twitter:site' => $fallback['twitter'],
            'twitter:title' => get_the_title(),
            'twitter:description' => (is_single()) ? get_the_excerpt() : $fallback['description'],
            'twitter:image:src' => content_first_image($post->ID),
            'twitter:url' => get_site_url() . $_SERVER['REQUEST_URI'],
        );

        foreach ($site_meta as $key => $value) {
            printf('<meta name="%s" content="%s">', $key, $value);
        }
    }

    function facebook_meta() {
        /* Social Meta Information for Facebook
         * ------------------------------------
         * This /should/ be all of the relevant information for Facebook. */
        global $fallback, $post;
        $the_post = get_post($post->ID);
        setup_postdata($the_post);

        $site_meta = array(
            'og:title' => get_the_title(),
            'og:site_name' => get_bloginfo('name'),
            'og:url' => get_site_url() . $_SERVER['REQUEST_URI'],
            'og:description' => (is_single()) ? get_the_excerpt() : $fallback['description'],
            'og:image' => content_first_image($post->ID),
            'og:type' => (is_single()) ? 'article' : 'website',
            'og:locale' => get_locale(),
        );

        if (is_single()) {
            $category = get_the_category($post->ID);

            $tags = get_the_tags();
            $taglist = '';
            $i = 0;

            foreach ($tags as $key => $value) {
                if ($i > 0) {
                    $taglist .= ', ';
                }

                $taglist .= $value->name;
                $i++;
            }

            $article_meta = array(
                'article:section' => $category[0]->cat_name,
                'article:tag' => $taglist,
                'article:publisher' => $fallback['publisher'],
            );

            $site_meta = array_merge($site_meta, $article_meta);
        }

        foreach ($site_meta as $key => $value) {
            printf('<meta property="%s" content="%s">', $key, $value);
        }
    }

    function clean_search_url() {
        // See: http://wpengineer.com/2258/change-the-search-url-of-wordpress/
        if (is_search() && ! empty($_GET['s'])) {
            wp_redirect(home_url('/search/') . urlencode(get_query_var('s')));
            exit();
        }
    }

    function rmwb_excerpt() {
        $excerpt = get_the_content(); 
        $excerpt = strip_shortcodes($excerpt); 
        $excerpt = strip_tags($excerpt); 
        $excerpt = explode('.', $excerpt);
        $excerpt = $excerpt[0]; 
        $length = strlen(preg_replace(array('/\s/', '/\n/'), '', $excerpt)); 
        return $excerpt;
    }

    function rmwb_scripts() {
        wp_enqueue_script('rmwb-functions', get_stylesheet_directory_uri() . '/js/functions.js', array('jquery'), '1.0', true);
    }

    function google_font_url() {
        $google_fonts = array(
            'Open Sans Condensed 300',
            'Merriweather',
            'Open Sans:400,700,800',
            'Bitter',
            'Roboto Condensed',
            'Source Code Pro'
        );

        $g_string = 'http://fonts.googleapis.com/css?family=';

        foreach ($google_fonts as $index => $value) {
            $g_string .= str_replace(' ', '+', $value);

            if ($index < sizeof($google_fonts) - 1) {
                $g_string .= '|';
            }
        }

        return $g_string;
    }

    function rmwb_styles() {
        wp_register_style('google-fonts',  google_font_url());
        wp_enqueue_style('google-fonts');
        // Main style.
        wp_enqueue_style('main-style', get_stylesheet_uri(), false, '1.4', 'all');
    }

    function rmwb_menu() {
        register_nav_menu('sidebar-menu',__('Sidebar Menu'));
    }

    function archive_page_count($page_num = null, $total_results = null) {
        if ($page_num == '') {
            $page_num = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        if ($total_results == '') {
            // if (is_home()) {
                $total_results = wp_count_posts('post', 'readable')->publish;
            // }
        }

        $posts_per_page = get_option('posts_per_page');
        $total_pages = ceil($total_results / $posts_per_page);
        echo 'Page ' . $page_num . ' of ' . $total_pages;
    }

    function content_first_image() {
        // See: http://css-tricks.com/snippets/wordpress/get-the-first-image-from-a-post/
        global $post, $posts, $fallback;
        ob_start();
        ob_end_clean();
        $first_image = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $first_image = $matches[1][0];
        return (!empty($first_image)) ? $first_image : $fallback['image'];
    }

    function rmwb_title($title, $sep) {
        // See: https://tommcfarlin.com/filter-wp-title/
        global $paged, $page;

        if (is_single() || is_page()) {
            return substr($title, 2);
        }

        if (!is_search()) {
            $title .= get_bloginfo('name');
        }

        if (is_home() || is_front_page()) {
            if (0 == $paged) {
                return $title;   
            } else {
                return $title .= " $sep " . $paged;
            }
        }

        return substr($title, 2);
    }

    add_filter('wp_title', 'rmwb_title', 10, 2);

    function rmwb_reading_seconds($post) {
        // Word count to seconds reading time, based on 300 WPM.
        $average_wpm = 300;
        $average_wps = round($average_wpm / 60);
        $time = str_word_count(strip_tags($post));
        return round($time / $average_wps);
    }

    function rmwb_reading_minutes($seconds) {
        // Seconds to minutes, rounded to nearest minute.
        if ($seconds % 60 <= 30) {
            $minutes = floor($seconds / 60);
        } else {
            $minutes = ceil($seconds / 60);
        }

        return $minutes;
    }

    function rmwb_minutes_to_words($minutes) {
        // Converts a given minutes time to words.
        // Only does up to ninety-nine minutes.
        // Honestly, if your article's reading time is above that, you've gone wrong somewhere.
        $time_word = '';

        $singles = array(
            'one','two','three','four','five',
            'six','seven','eight','nine'
        );

        $teens = array(
            'eleven', 'twelve','thirteen','fourteen','fifteen',
            'sixteen','seventeen','eighteen','nineteen'
        );

        $tens = array(
            'ten','twenty','thirty','forty','fifty',
            'sixty','seventy','eighty','ninety'
        );

        if ($minutes <= 0) {
            // <0 - 0
            $time_word = $singles[0];
        } elseif ($minutes < 10) {
            // 1 - 9
            $time_word = $singles[$minutes - 1];
        } elseif ($minutes > 10 && $minutes < 20) {
            // 11 - 19
            $time_word = $teens[$minutes - 11];
        } elseif ($minutes % 10 == 0) {
            // 10, 20, etc.
            $time_word = $tens[($minutes / 10) - 1];
        } elseif ($minutes > 99) {
             // > 99
            $a = $tens[8];   
            $b = $singles[8];
            $time_word = 'greater than' . $a . '-' . $b;
        } else {
            // 31, 56, 77, etc.
            $a = $tens[($minutes % 100) / 10 - 1];   
            $b = $singles[($minutes % 10) - 1];
            $time_word = $a . '-' . $b;
        }

        return $time_word;
    }

    function rmwb_reading_time($post) {
        // See http://www.bhalash.com/archives/13544802870
        $time = rmwb_reading_seconds($post);
        $time = rmwb_reading_minutes($time);
        $time_word = rmwb_minutes_to_words($time);
        $min_word = ($time <= 1) ? ' minute.' : ' minutes.';
        return ucfirst($time_word) . $min_word;
    }

    function sidebar_widgets_init() {
        // Wordpress dynamic sidebar.
        register_sidebar(array(
            'name' => 'Dynamic sidebar.',
            'id' => 'dynamicsidebar',
            'before_widget' => '<div class="sidebarwidget">',
            'after_widget' => '</div>',
            'before_title' => '<h6 class="sidebartitle">',
            'after_title' => '</h6>',
        ));
    }

    function rmwb_comments($comment, $args, $depth) {
        // Custom comment output.
        $GLOBALS['comment'] = $comment; ?>
        <li class="comment <?php comment_class(); ?>" id="li-comment-<?php comment_ID() ?>">
            <h4 class="author"><?php echo get_comment_author_link(); ?></h4>
            <p>
                <?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()); ?>
                <?php edit_comment_link(__('edit'),'  ',''); ?>
            </p>
            <?php if ($comment->comment_approved == '0') : ?>
                <p><?php _e('Your comment has been held for moderation.') ?></p>
            <?php endif;
            comment_text();
    }

    function rmwb_nav() {
        register_nav_menus(array(
            'sidebar' => 'Sidebar Menu'
        ));
    }

    add_action('init', 'rmwb_nav');
    add_action('template_redirect', 'clean_search_url');
    // Enqueue all scripts and stylesheets.
    add_action('wp_enqueue_scripts', 'rmwb_styles');
    add_action('wp_enqueue_scripts', 'rmwb_scripts');
    // Wordpress repeatedly inserted emoticons. No more, ever.
    remove_filter('the_content', 'convert_smilies');
    remove_filter('the_excerpt', 'convert_smilies');
    // Declares that the theme has HTML5 support.
    current_theme_supports('html5');
    current_theme_supports('menus');
    // Sidebar.
    add_action('widgets_init', 'sidebar_widgets_init');
    // Menu. 
    add_action('init', 'rmwb_menu');
?>