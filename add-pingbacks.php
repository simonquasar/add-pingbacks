<?php
/*
 * Plugin Name:       Add Pingbacks
 * Plugin URI:        https://simonquasar.net/add-pingbacks
 * Description:       Manually add a Pingback to any post.
 * Version:           1.2
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            simonquasar
 * Author URI:        https://simonquasar.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/simonquasar/add-pingbacks
 * Text Domain:       add-pingbacks
*/

defined('ABSPATH') || exit;

function add_pingbacks_set_plugin_meta($links, $file) {
    $plugin_base = plugin_basename(__FILE__);
    if ($file === $plugin_base) {
        $new_links = [
            '<a href="options-general.php?page=addPingbacks">' . esc_html__('Add Pingback', 'addPingbacks') . '</a>'
        ];
        return array_merge($links, $new_links);
    }
    return $links;
}

function add_pingbacks_options_init() {
    register_setting('addPingbacks-group', 'addPingbacks-options', 'add_pingbacks_validate_input');
}

function add_pingbacks_validate_input($input) {
    return sanitize_text_field($input);
}

function add_pingbacks_options_link() {
    add_submenu_page('edit-comments.php', 'Add Pingbacks', 'Add Pingbacks', 'manage_options', 'addPingbacks', 'add_pingbacks_options_page', 'dashicons-admin-comments');
}

function add_pingbacks_enqueue_scripts($hook) {
    if ('comments_page_addPingbacks' !== $hook) {
        return;
    }
    
    wp_enqueue_script(
        'add-pingbacks-js',
        plugins_url('add-pingbacks.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script(
        'add-pingbacks-js',
        'wpApiSettings',
        array('ajaxUrl' => admin_url('admin-ajax.php'))
    );
}

function get_post_types_dropdown() {
    $post_types = get_post_types(array('public' => true), 'objects');
    $options = '';
    foreach ($post_types as $post_type) {
        $options .= '<option value="' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</option>';
    }
    return $options;
}

function add_post_select_box() {
    echo '<select name="post_type" id="post_type" onchange="fetchPosts(this.value)">';
    echo '<option value="">- select Post type -</option>';
    echo get_post_types_dropdown();
    echo '</select>';

    echo '<select name="post_list" id="post_list" style="display:none;"></select>';
}

function add_pingback_text_box($label, $name, $default = '') {
    ?>
    <tr>
        <td><label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label); ?></label></td>
        <td colspan="2"><input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($default); ?>"/></td>
    </tr>
    <?php
}

function add_pingbacks_add_comment($post_id, $author, $email, $url, $ip, $comment) {
    if (empty($comment)) {
        return new WP_Error('empty_comment', __('Comment cannot be empty', 'addPingbacks'));
    }

    return wp_insert_comment([
        'comment_post_ID' => $post_id,
        'comment_author' => $author,
        'comment_author_email' => $email,
        'comment_author_url' => $url,
        'comment_content' => $comment,
        'comment_type' => 'pingback',
        'comment_parent' => 0,
        'comment_author_IP' => $ip,
        'comment_agent' => 'Add Pingbacks Plugin',
        'comment_date' => current_time('mysql'),
        'comment_approved' => 1
    ]);
}

function add_pingbacks_options_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addpingback') {
        $post_id = intval($_POST['post_list']);
        $author = !empty($_POST['author_name']) ? sanitize_text_field($_POST['author_name']) : __('anonymous', 'addPingbacks');
        $email = !empty($_POST['author_email']) ? sanitize_email($_POST['author_email']) : get_bloginfo('admin_email');
        $url = !empty($_POST['author_url']) ? esc_url($_POST['author_url']) : '';
        $ip = !empty($_POST['author_ip']) ? sanitize_text_field($_POST['author_ip']) : '127.0.0.1';

        $result = add_pingbacks_add_comment($post_id, $author, $email, $url, $ip, sanitize_textarea_field($_POST['comment']));
        $message = is_wp_error($result) ? $result->get_error_message() : sprintf(__('Pingback added to %s', 'addPingbacks'), esc_html(get_the_title($post_id)));

        echo '<div class="updated settings-error"><p>' . esc_html($message) . '</p></div>';
    }
    ?>

    <div class="wrap">
        <h2><?php esc_html_e('Add Pingback URLs', 'addPingbacks'); ?></h2>
        <span class="description">
            <?php esc_html_e('Select a Post Type and a corresponding Post, then add the referral URL which points to your content. Play fair. ;)', 'addPingbacks'); ?><br/>
            <?php printf(__('Plugin by <a href="%s" target="_blank" title="%s">%s</a>', 'addPingbacks'), esc_url('http://simonquasar.net'), esc_attr__('simonquasar', 'addPingbacks'), esc_html__('simonquasar', 'addPingbacks')); ?>
        </span>

        <form method="post" action="">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th colspan="3" style="font-size:1.3em"><?php esc_html_e('Select Post Type', 'addPingbacks'); ?></th>
                    </tr>
                    
                    <tr>
                        <td><strong><?php esc_html_e('Post Type', 'addPingbacks'); ?></strong><br/><?php esc_html_e('Post Title:', 'addPingbacks'); ?></td>
                        <td><?php add_post_select_box(); ?></td>
                    </tr>

                    <tr>
                        <th colspan="3" style="font-size:1.3em"><?php esc_html_e('Referrer link', 'addPingbacks'); ?></th>
                    </tr>
                    
                    <?php 
                    $authors = [
                        ['name' => 'author_name', 'label' => __('Site Title / Page Name', 'addPingbacks'), 'default' => ''],
                        ['name' => 'author_url', 'label' => __('Link', 'addPingbacks'), 'default' => 'http://']
                    ];

                    foreach ($authors as $author) {
                        add_pingback_text_box($author['label'], $author['name'], $author['default']);
                    }
                    ?>

                    <tr>
                        <th colspan="2" style="font-size:1.3em"><?php esc_html_e('Excerpt / Content', 'addPingbacks'); ?></th>
                    </tr>
                    <tr>
                        <td colspan="3"><textarea name="comment" id="comment" cols="120" rows="5">[...] cit. [...]</textarea></td>
                    </tr>
                </tbody>
            </table>

            <p class="submit">
                <input type="hidden" name="action" value="addpingback" />
                <input type="submit" class="button-primary" value="<?php esc_attr_e('Add Link Reference', 'addPingbacks'); ?>" />
            </p>
        </form>
    </div>
    <?php
}

add_action('wp_ajax_fetch_posts', function() {
    $post_type = !empty($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
    $posts = get_posts([
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    $response = [];
    foreach ($posts as $post) {
        $response[] = [
            'ID' => $post->ID,
            'title' => get_the_title($post->ID),
        ];
    }
    
    wp_send_json($response);
});

add_filter('plugin_row_meta', 'add_pingbacks_set_plugin_meta', 10, 2);
add_action('admin_init', 'add_pingbacks_options_init');
add_action('admin_menu', 'add_pingbacks_options_link');
add_action('admin_enqueue_scripts', 'add_pingbacks_enqueue_scripts');
?>
