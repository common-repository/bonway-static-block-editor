<?php
/*
#==============================================================================#
[Styling of the plugin]
#==============================================================================#
*/

/**
 * Enqueue all scripts and styling for the plugin
 * @method bonwaysbe_register_css_js
 */
add_action('admin_enqueue_scripts', 'bonwaysbe_register_css_js');
function bonwaysbe_register_css_js($hook)
{
    $current_screen = get_current_screen();
    $screenId = $current_screen->id;

    //I am aware this edit was made directly in the plugin, but I haven't updated this plugin since... forever. Might make this default at some point so that eventual updates don't screw this up.
    if ($screenId === 'bonway-static-block' || $screenId === 'edit-bonway-static-block' || $screenId === 'edit-awsm_job_openings') {
        wp_enqueue_style('bonwaysbe_admin_style', plugins_url('../style/admin.css',__FILE__ ));
        wp_enqueue_script("bonwaysbe_admin_js", plugins_url("../js/admin.js", __FILE__), array('jquery'));
    } else {
        return;
    }
}

/*
#==============================================================================#
[Rendering of the Blocks to the frontend]
#==============================================================================#
*/

/**
 * Get a Bonway Static Block using its Identifier
 * @method bonwaysbe_get_block_by_identifier
 * @param  string       $identifier Identifier of the block
 * @return string        Block content
 */
function bonwaysbe_get_block_by_identifier($identifier){
    if(bonwaysbe_meta_exists($identifier)) {
        $meta = bonwaysbe_select_meta($identifier);
        $block = "";

        //Get the post ID
        $post_id = $meta->post->ID;

        //Get post and meta content
        $post = get_post($post_id);
        $post_meta = get_post_meta($post->ID);

        //Wrap content in a div with the given class, or simply return the content
        $class = "bonwaysbe-block";
        $id = "";
        if(isset($post_meta['bonwaysbe-class'])) {
            $class = " " . $post_meta['bonwaysbe-class'][0];
            $id = $post_meta['bonwaysbe-class'][0];
        }

        $block = "<div id='" . $id . "' class='bonwaysbe-block" . $class . "'>";
        $direction = "direction-" . (isset($post_meta['bonwaysbe-content-direction'][0]) ? $post_meta['bonwaysbe-content-direction'][0] : "column");
        if(isset($post_meta['bonwaysbe-display-title']) && $post_meta['bonwaysbe-display-title'][0] === 'display') { $block .= "<h2>$post->post_title</h2>"; }
        $block .= "<div class='bonwaysbe-block__container'><div class='bonwaysbe-block__inner " . $direction . "'>" . bonwaysbe_get_block_content($post) . "</div></div>";
        $block .= "</div>";

        return do_shortcode($block);
    } else {
        return "";
    }
}

/**
 * Get the content of a requested post
 * @method bonwaysbe_get_block_content
 * @param  Object          $post Post to get data from
 * @return string                Content of the post
 */
function bonwaysbe_get_block_content($post) {
    ob_start();
    $content = wpautop(apply_filters('the_content', $post->post_content));
    ob_end_clean();

    return $content;
}

/**
 * Check if a certain meta-value exists
 * @method bonwaysbe_meta_exists
 * @param  string          $identifier Identifier of the block
 * @return boolean          If the value exists
 */
function bonwaysbe_meta_exists($identifier) {
    $args = array(
        'post_type' => 'bonway-static-block',
        'meta_query' => array(
            array(
                'key' => 'bonwaysbe-identifier',
                'value' => $identifier
            )
        ),
        'fields' => 'ids'
    );

    $postQ = new WP_Query( $args );

    return sizeof($postQ->posts);
}
