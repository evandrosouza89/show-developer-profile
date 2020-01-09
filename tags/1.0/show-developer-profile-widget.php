<?php
// Register and load the widget
add_action('widgets_init', 'evandrosouza89_sdvp_load_widget');
add_action('wp_enqueue_scripts', 'evandrosouza89_sdvp_styles_setup_cb');

function evandrosouza89_sdvp_styles_setup_cb() {
    wp_register_style('evandrosouza89_sdvp_widget', plugins_url('show-developer-profile-styles.css', __FILE__));
    wp_enqueue_style('evandrosouza89_sdvp_widget');
}

function evandrosouza89_sdvp_load_widget() {
    register_widget('evandrosouza89_sdvp_widget');
}

// Creating the widget
class evandrosouza89_sdvp_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            // Base ID of your widget
            'evandrosouza89_sdvp_widget',
            // Widget name will appear in UI
            'Show developer profile plugin',
            // Widget description
            array('description' => 'Exhibits your github profile details and repositories')
        );
    }

    // Creating widget front-end
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // This is where you run the code and display the output
        $options = get_option('evandrosouza89_sdvp_options');
        if ($options['github-user-details'] != null && $options['github-user-details'] !== '') {
            $avatar_url = $options['github-user-details']->avatar_url;
            $user_name = $options['github-user-details']->name;
            $repos_url = $options['github-user-details']->repos_url;
            $bio = $options['github-user-details']->bio;
            $blog = $options['github-user-details']->blog;
            ?>

            <div class="card_div">
                <img class="user_avatar_img" src="<?= $avatar_url ?>" alt="Avatar" style="width:100%">
                <div class="user_container_div">
                    <h1 class="user_name_header"><?= $user_name ?></h1>
                    <p class="bio_paragraph"><?= $bio ?></p>
                    <div class="bio_footer_div">
                        <a href=<?= $repos_url ?>>Github</a> |
                        <a href=<?= $blog ?>>About me</a>
                    </div>
                    <h1 class="projects_header">My github projects</h1>

                    <?php

                    $repositories = $options['github-repositories-list']; ?>

                    <?php
                    $repos_size = count($repositories);
                    foreach ($repositories as $value) { ?>
                        <h2 class="project_name_header"><?= $value->name ?></h2>
                        <p class="project_description_paragraph"><?= $value->description ?></p>
                        <div class="read_more_link">
                            <a href=<?= $value->html_url ?>>Read more</a>
                        </div>
                        <?php if ($repos_size-- > 1) { ?>
                            <hr class="project_divider_hr">
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php }
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpb_widget_domain');
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} ?>