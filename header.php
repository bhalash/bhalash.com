<?php

/**
 * PHP Header File
 * -----------------------------------------------------------------------------
 * @category   PHP Script
 * @package    Sheepie
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU General Public License v3.0
 * @version    3.0
 * @link       https://github.com/bhalash/sheepie
 *
 * This file is part of Sheepie.
 * 
 * Sheepie is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software 
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * Sheepie is distributed in the hope that it will be useful, but WITHOUT ANY 
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with 
 * Sheepie. If not, see <http://www.gnu.org/licenses/>.
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- The mystery of life isn't a problem to solve, but a reality to experience. -->
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
    <?php // FIXME ?>
    <title><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <link rel="icon" type="image/png" href="<?php printf(get_stylesheet_directory_uri()); ?>/images/favicon.png" />
    <?php social_meta(); ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="side-by-side" id="site">
        <?php // Output a pretty background image, if it exists. ?>
        <div class="<?php header_class($post->ID); ?>" id="header" <?php header_background($post->ID); ?>>
            <h2 id="header-title"><?php page_title($post->ID); ?></h2>

            <p id="header-description">
                <?php // Either site information or post meta content. ?>
                <span>
                    <?php if (!is_single()) : ?>
                        <?php bloginfo('description'); ?>
                    <?php else: ?>
                        <small><time datetime="<?php the_time('Y-m-d H:i'); ?>"><?php the_time(get_option('date_format')) ?></time> in <?php the_category(', '); edit_post_link('edit post', ' / ', ''); ?></small> 
                    <?php endif; ?>
                </span>
            </p>

            <?php // TODO: Static menu isn't desireable! ?>
            <?php // wp_nav_menu(array('theme_location' => 'top-social')); ?>

            <ul id="header-social">
                <li><a class="facebook" href="//www.facebook.com/bhalash"></a></li>
                <li><a class="github" href="//www.github.com/bhalash"></a></li>
                <li><a class="twitter" href="//www.twitter.com/bhalash"></a></li>
            </ul>
        </div>
        <div id="content">
            <div id="interior">