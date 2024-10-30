<?php
/*
#==============================================================================#
[Post Types, Shortcodes, and Custom Columns]
#==============================================================================#
 */

/**
 * Registers a new posttype for the Bonway SBE
 * @method bonwaysbe_post_type
 */
function bonwaysbe_post_type()
{
    $labels = array(
        'name'          => __('Static Blocks'),
        'singular_name' => __('Static Block'),
  );

    $rewrite = array(
        'slug'  => 'bonway-sbe'
  );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'menu_icon'             => plugins_url('../images/bonway_logo_mini.png', __FILE__),
        'rewrite'               => $rewrite,
  );

    register_post_type('bonway-static-block', $args);
}
add_action('init', 'bonwaysbe_post_type');

/**
 * Registers the shortcode for the Static Blocks
 * @method bonway_static_block
 * @param  array               $atts    An array of attributes used for the shortcode
 * @param  string              $content Default NULL
 * @return Object                       Content of the selected block
 */
function bonway_static_block($atts, $content=NULL){
    $atts = shortcode_atts(array(
        'identifier' => ''
   ), $atts, 'bonway_static_block');
    $identifier = $atts['identifier'];

    return bonwaysbe_get_block_by_identifier($identifier);
}
add_shortcode('bonwaysbe','bonway_static_block');

/**
 * Initialize the custom columns for the module
 * @method bonwaysbe_custom_columns
 * @param  array                   $columns Default param
 */
function bonwaysbe_custom_columns($columns) {
    $columns['bonwaysbe_identifier'] = "Identifier";
    $columns['bonwaysbe_identifier_shortcode'] = "Identifier Shortcode";

    return $columns;
}
add_filter('manage_bonway-static-block_posts_columns', 'bonwaysbe_custom_columns');

/**
 * Insert data into custom columns
 * @method bonwaysbe_custom_column_data
 * @param  array                  $column  Array of custom columns
 * @param  integer                $post_id ID of the post
 */
function bonwaysbe_custom_column_data($column, $post_id) {
    switch ($column) {
        case 'bonwaysbe_identifier' :
            $identifier = get_post_meta(get_the_ID(), 'bonwaysbe-identifier', true);
            echo (!empty($identifier)) ? $identifier : 'No identifier set';
            break;
        case 'bonwaysbe_identifier_shortcode' :
            $identifier = get_post_meta(get_the_ID(), 'bonwaysbe-identifier', true);
            $printId = (!empty($identifier)) ? "[bonwaysbe identifier=&quot;" . $identifier . "&quot;]" : "No identifier set";
            $showCopy = (!empty($identifier)) ? "can-copy" : "";
            $copyMsg = "The shortcode <em>'" . $identifier . "'</em> has been copied succesfuly!";
            echo '<div class="' . $showCopy . ' bonwaysbe-inputcontainer"><div class="copy-btn js-bonwaysbe-copy-btn"></div><input class="js-bonwaysbe-shortcode readonly" value="' . $printId . '" readonly></input><div class="js-bonwaysbe-copy-msg copy-msg">' . $copyMsg . '</div></div>';
            break;
    }
}
add_action('manage_bonway-static-block_posts_custom_column' , 'bonwaysbe_custom_column_data', 10, 2);