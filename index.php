<?php


/*
  Plugin Name: Clean up csv EventOn export 
  Plugin URI: http://#
  description: Clean up csv EventOn export 
  Integration   Version: 0.1
  Author: Nick Mullen
  Author URI: http://#
 */

//Load template from specific page
add_filter( 'page_template', 'wpa3396_page_template' );
function wpa3396_page_template( $page_template ){

    if ( get_page_template_slug() == 'toCSV.php' ) {
        $page_template = dirname( __FILE__ ) . '/toCSV.php';
    }
    return $page_template;
}

/**
 * Add "Custom" template to page attirbute template section.
 */
add_filter( 'theme_page_templates', 'wpse_288589_add_template_to_select', 10, 4 );
function wpse_288589_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

    // Add custom template named template-custom.php to select dropdown 
    $post_templates['toCSV.php'] = __('Export');

    return $post_templates;
}
?>

