// The "Upload" button
document.addEventListener("DOMContentLoaded", function() {
    jQuery(".js-bonwaysbe-shortcode").on("click", function() {
        copyValue(jQuery(this));
    });

    jQuery(".js-bonwaysbe-copy-btn").on("click", function() {
        var sibling = jQuery(this).next();
        copyValue(sibling);
    });
});

function copyValue(el) {
    jQuery(el).focus();
    jQuery(el).select();
    jQuery(el).next().css("display", "block");
    jQuery(el).next().fadeTo("slow", 1, function() {
        setTimeout(function(){
            jQuery(el).next().fadeTo("slow", 0, function(){
                jQuery(el).next().css("display", "none");
            });
        }, 4000);
    });
    document.execCommand("copy");
}