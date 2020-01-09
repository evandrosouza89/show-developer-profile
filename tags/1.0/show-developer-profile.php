<?php

/*
Plugin Name: Show developer profile
Plugin URI: https://github.com/evandrosouza89/show-developer-profile
Description: A plugin to fetch and exhibit profile information and list repositories of a given github user.
Version: 1.0
Author: Evandro Souza
Author URI: https://www.linkedin.com/in/evandro-souza
License: GPL
*/

define( 'EVANDROSOUZA89_SDVP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( EVANDROSOUZA89_SDVP_PLUGIN_DIR . 'show-developer-profile-settings.php' );
require_once( EVANDROSOUZA89_SDVP_PLUGIN_DIR . 'show-developer-profile-widget.php' );

function evandrosouza89_sdvp_get_user_details($user_name) {
    $json = wp_remote_retrieve_body(wp_remote_get("https://api.github.com/users/$user_name"));
    return json_decode($json);
}

function evandrosouza89_sdvp_get_repositories_list($repos_url) {
    $json = wp_remote_retrieve_body(wp_remote_get($repos_url));
    return json_decode($json);
}