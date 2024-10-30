<?php
/*
#==============================================================================#
[Meta box(es)]
#==============================================================================#
 */

 /**
  * Adds a meta box to the post editing screen
  * @method bonwaysbe_meta_box
  */
function bonwaysbe_meta_box() {
    add_meta_box(
		'bonway_static-block_meta',
		'General',
		'bonwaysbe_meta_fields',
		'bonway-static-block',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'bonwaysbe_meta_box');

/**
* Outputs the content of the meta box
* @method bonwaysbe_meta_fields
* @param  Object                $post The post being used
*/
function bonwaysbe_meta_fields($post) {
   wp_nonce_field(basename(__FILE__), 'bonwaysbe_nonce');
   $bonwaysbe_meta = get_post_meta($post->ID);
   ?>

   <div class="sbe-section sbe-section__general">
      <div class="sbe-section__inner">
            <?php if(isset($bonwaysbe_meta['bonwaysbe-identifier'])) {
                $identifier = $bonwaysbe_meta['bonwaysbe-identifier'][0];
                $printId = "[bonwaysbe identifier=&quot;" . $identifier . "&quot;]";
                $copyMsg = "The shortcode <em>'" . $identifier . "'</em> has been copied succesfuly!";
                echo '<div class="can-copy bonwaysbe-inputcontainer"><div class="copy-btn js-bonwaysbe-copy-btn"></div><input class="js-bonwaysbe-shortcode readonly" value="' . $printId . '" readonly></input><div class="js-bonwaysbe-copy-msg copy-msg">' . $copyMsg . '</div></div>';
            } ?>
          <div class="sbe-section__container">
              <span>Identifier <span class="required">*</span></span>
              <input type="text" name="bonwaysbe-identifier" id="bonwaysbe-identifier" required value="<?php if (isset($bonwaysbe_meta['bonwaysbe-identifier'])) echo $bonwaysbe_meta['bonwaysbe-identifier'][0]; ?>" />
          </div>
          <div class="sbe-section__container">
              <span>Block Class</span>
              <input type="text" name="bonwaysbe-class" id="bonwaysbe-class" value="<?php if (isset($bonwaysbe_meta['bonwaysbe-class'])) echo $bonwaysbe_meta['bonwaysbe-class'][0]; ?>" />
          </div>
          <div class="sbe-section__container">
              <span>Content Direction</span>
              <select name="bonwaysbe-content-direction" id="bonwaysbe-content-direction" value="<?php if (isset($bonwaysbe_meta['bonwaysbe-content-direction'])) echo $bonwaysbe_meta['bonwaysbe-content-direction'][0]; ?>">
                  <option value="column" <?php if($bonwaysbe_meta['bonwaysbe-content-direction'][0] === "column") { echo "selected"; } ?>>Column</option>
                  <option value="row" <?php if($bonwaysbe_meta['bonwaysbe-content-direction'][0] === "row") { echo "selected"; } ?>>Row</option>
              </select>
          </div>
          <div class="sbe-section__container">
              <span>Display title</span>
              <input type="checkbox" name="bonwaysbe-display-title" id="bonwaysbe-display-title" value="display" <?php if($bonwaysbe_meta['bonwaysbe-display-title'][0] === "display"){ echo "checked"; }; ?> />
          </div>
        </div>
   </div>

   <?php
}

/**
* Saves the custom meta input
* @method bonwaysbe_meta_save
* @param  int              $post_id ID of the saved post
*/
function bonwaysbe_meta_save($post_id) {
   // Checks save status
   $is_autosave = wp_is_post_autosave($post_id);
   $is_revision = wp_is_post_revision($post_id);
   $is_valid_nonce = (isset($_POST['bonwaysbe_nonce']) && wp_verify_nonce($_POST['bonwaysbe_nonce'], basename(__FILE__))) ? 'true' : 'false';

   // Exits script depending on save status
   if ($is_autosave || $is_revision || !$is_valid_nonce) {
       return;
   }

   if(isset($_POST['bonwaysbe-class'])) {
       update_post_meta($post_id, 'bonwaysbe-class', sanitize_text_field($_POST['bonwaysbe-class']));
   }

    if(isset($_POST['bonwaysbe-content-direction'])) {
        update_post_meta($post_id, 'bonwaysbe-content-direction', sanitize_text_field($_POST['bonwaysbe-content-direction']));
    }

   $displayTitle = "hide";
   if($_POST['bonwaysbe-display-title'] === 'display') {
      $displayTitle = "display";
   }

    update_post_meta($post_id, 'bonwaysbe-display-title', $displayTitle);

   // Checks for input and sanitizes/saves if needed
   if(isset($_POST['bonwaysbe-identifier'])) {
       $query =  bonwaysbe_select_meta($_POST['bonwaysbe-identifier']);
       $identifierId = $query->post->ID;

       /*
           Check if the identifier is unique, return an error if it's not.
           Post data is still saved, because it's annoying if you lost a
           bunch of work simply because you did not enter a unique identifier.
       */
       if($query->have_posts() == false || $identifierId == $post_id) {
           update_post_meta($post_id, 'bonwaysbe-identifier', sanitize_text_field($_POST['bonwaysbe-identifier']));
       } else {
           $bonwaysbe_error = new WP_Error(
               "noUniqueSbeIdentifierError",
               "Static Block data has been saved, but the provided Identifier is not unique. Please use another."
           );

           if ($bonwaysbe_error) {
               $_SESSION['bonwaysbe-error'] = $bonwaysbe_error->get_error_message();
           }

           return;
       }
   }

}
add_action('save_post', 'bonwaysbe_meta_save');

/**
* Returns an error based on session-data after a save
* @method bonwaysbe_custom_errors
*/
function bonwaysbe_not_unique_identifier_error() {
   if(isset($_SESSION) && array_key_exists('bonwaysbe-error', $_SESSION)) {?>
       <div class="error notice notice-error is-dismissible">
           <p><?= $_SESSION['bonwaysbe-error']; ?></p>
       </div><?php

       unset( $_SESSION['bonwaysbe-error'] );
   }
}
add_action( 'admin_notices', 'bonwaysbe_not_unique_identifier_error' );
