<?php
//register our evandrosouza89_sdvp_init_cb to the admin_init action hook
add_action('admin_init', 'evandrosouza89_sdvp_init_cb');

function evandrosouza89_sdvp_init_cb() {
    // register a new setting for "evandrosouza89_sdvp_options_group" page
    register_setting('evandrosouza89_sdvp_options_group', 'evandrosouza89_sdvp_options', 'evandrosouza89_sdvp_options_validate_username_cb');

    // register a new section in the "evandrosouza89_sdvp_options_group" page
    add_settings_section('show_developer_profile_plugin', 'Main Settings', 'evandrosouza89_sdvp_section_text_cb', 'evandrosouza89_sdvp_options_group');

    // register a new field in the "show_developer_profile_plugin" section
    add_settings_field('show-developer-profile-options-user-name', 'Github user name', 'evandrosouza89_sdvp_setting_username_cb', 'evandrosouza89_sdvp_options_group', 'show_developer_profile_plugin');
}

add_action('admin_menu', 'evandrosouza89_sdvp_page_cb');

function evandrosouza89_sdvp_page_cb() {
    add_options_page('Show Developer Profile Plugin Configuration Page', 'Show Developer Profile Plugin', 'manage_options', 'show-developer-profile-plugin', 'evandrosouza89_sdvp_page_html_cb');
}

function evandrosouza89_sdvp_page_html_cb() {

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('evandrosouza89_sdvp_messages', 'show_developer_profile_message', 'Settings Saved', 'updated');
    }

    ?>

    <div class="wrap">
        <h2>Show Developer Profile Plugin</h2>
        Options relating to the Show Developer Profile Plugin.
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "evandrosouza89_sdvp_options_group"
            settings_fields('evandrosouza89_sdvp_options_group');
            // output setting sections and their fields
            do_settings_sections('evandrosouza89_sdvp_options_group');

            submit_button('Save Settings');

            ?>
        </form>
    </div>

    <?php
}


function evandrosouza89_sdvp_section_text_cb() {
    echo '<p>Instructions: just provide a valid and existing github username and click Save Settings.</p>';
}

function evandrosouza89_sdvp_setting_username_cb() {
    $options = get_option('evandrosouza89_sdvp_options');
    echo "<input id='github-user-options-user-name' name='evandrosouza89_sdvp_options[github-username]' size='40' type='text' value='{$options['github-username']}' />";
}

function evandrosouza89_sdvp_options_validate_username_cb($input) {
    $options = get_option('evandrosouza89_sdvp_options');

    $options['github-username'] = trim($input['github-username']);

    if (!preg_match('/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/i', $options['github-username'])) {
        $options['github-username'] = '';
        $options['github-user-details'] = '';
        $options['github-repositories-list'] = '';
        add_settings_error('evandrosouza89_sdvp_messages', 'show_developer_profile_message', 'Invalid github username provided', 'error');
    } else {
        $user_details = evandrosouza89_sdvp_get_user_details($options['github-username']);
        if ($user_details == null) {
            add_settings_error('evandrosouza89_sdvp_messages', 'show_developer_profile_message', 'Could not fetch user information from github. Does it exist?', 'error');
            $options['github-username'] = '';
            $options['github-user-details'] = '';
            $options['github-repositories-list'] = '';
        } else {
            $options['github-user-details'] = $user_details;
            $repositories_list = evandrosouza89_sdvp_get_repositories_list($user_details->repos_url);
            if ($repositories_list == null) {
                add_settings_error('evandrosouza89_sdvp_messages', 'show_developer_profile_message', 'Could not fetch repositories information from github. Does this user has any public repository?', 'error');
                $options['github-username'] = '';
                $options['github-user-details'] = '';
                $options['github-repositories-list'] = '';
            } else {
                $options['github-repositories-list'] = $repositories_list;
            }
        }
    }

    return $options;
} ?>