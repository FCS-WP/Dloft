<?php

/**
 * Palladio child theme functions and definitions
 */

function palladio_child_scripts()
{
    wp_enqueue_style('palladio-parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'palladio_child_scripts');

/*
 * Define Variables
 */
if (!defined('THEME_DIR'))
    define('THEME_DIR', get_template_directory());
if (!defined('THEME_URL'))
    define('THEME_URL', get_template_directory_uri());


/*
 * Include framework files
 */
foreach (glob(THEME_DIR . '-child' . "/includes/*.php") as $file_name) {
    require_once($file_name);
}


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
