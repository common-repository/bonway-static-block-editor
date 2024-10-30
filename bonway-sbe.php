<?php

include 'helpers/SbeCustomDataHelper.php';
include 'helpers/SbeMetaHelper.php';
include 'helpers/SbeRenderHelper.php';

/*
Plugin Name: Bonway Static Block Editor
Description: A simple Static Block Editor (SBE) that is both editor- and developer-friendly
Version: 1.1.0
Author: Bonway Services
Author URI: https://bonway-services.nl
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2018-2019 Bonway Services.
*/

/*
#==============================================================================#
[Basic hooks]
#==============================================================================#
 */

/**
 * Activation Hook for the module
 * @method bonwaysbe_activation
 */
function bonwaysbe_activation() {

}
register_activation_hook(__FILE__, 'bonwaysbe_activation');

/**
 * Deactivation Hook for the module
 * @method bonwaysbe_deactivation
 */
function bonwaysbe_deactivation() {

}
register_deactivation_hook(__FILE__, 'bonwaysbe_deactivation');

/**
 * Uninstall Hook for the module
 * @method bonwaysbe_uninstall
 */
function bonwaysbe_uninstall() {
    bonwaysbe_deactivation();
}
register_uninstall_hook(__FILE__, 'bonwaysbe_uninstall');

/*
#==============================================================================#
[General Functions]
#==============================================================================#
 */

/**
 * Returns meta-information based on the provided identifier-value
 * @method bonwaysbe_select_meta
 * @param  string     $value Identifier for the requested block
 */
function bonwaysbe_select_meta($value) {
    $metaArgs = array(
        'post_type'     => 'bonway-static-block',
        'meta_query'    => array(
            array(
                'key'   => 'bonwaysbe-identifier',
                'value' => $value
            )
        )
    );

   return new WP_Query($metaArgs);
}
