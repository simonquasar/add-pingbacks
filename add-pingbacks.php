<?php
/*
 * Plugin Name
 *
 * @package           AddPingbacks
 * @author            simonquasar
 * @copyright         2025 simonquasar
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Add Pingbacks
 * Plugin URI:        https://github.com/simonquasar/add-pingbacks
 * Description:       Manually add a Pingback to any post.
 * Version:           1.2.2
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * Author:            simonquasar
 * Author URI:        https://www.simonquasar.net/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       add-pingbacks
*/

defined('ABSPATH') || exit;

add_action('admin_menu', function() {
    add_submenu_page(
        'edit-comments.php',
        'Add Pingbacks',
        'Add Pingbacks', 
        'manage_options',
        'add-pingbacks',
        'render_pingbacks_page'
    );
});

function render_pingbacks_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    if (isset($_POST['submit_pingback']) && check_admin_referer('add_pingback_action', 'add_pingback_nonce')) {
            $post_id = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
            $url = esc_url_raw($_POST['url']);
            $content = sanitize_textarea_field($_POST['content']);
    

            if ($post_id && $url && $content) {
                $host = parse_url($url, PHP_URL_HOST);
                wp_insert_comment(array(
                    'comment_post_ID' => $post_id,
                    'comment_author' => $host ? $host : '',  
                    'comment_author_url' => $url,
                    'comment_content' => $content,
                    'comment_type' => 'pingback',
                    'comment_approved' => 1
                ));
                echo '<div class="notice notice-success"><p>' . esc_html__('Pingback added!', 'add-pingbacks') . '</p></div>';
            }
        }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post">
            <?php wp_nonce_field('add_pingback_action', 'add_pingback_nonce'); ?>
            <table class="form-table">
                <tr>
                    <td colspan="2">
                        <p class="description" style="text-align: right; font-style: italic; color: #666;">
                         made simple by <a href="https://www.simonquasar.net" target="_blank">simonquasar</a> since 2014
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for="post-type">Post Type:</label></th>
                    <td>
                        <select id="post-type">
                            <?php 
                            $types = get_post_types(array('public' => true), 'objects');
                            foreach ($types as $type) {
                                echo '<option value="' . esc_attr($type->name) . '">' . esc_html($type->label) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="posts">Post:</label></th>
                    <td>
                        <select name="post_id" id="posts" required>
                            <?php
                            $posts = get_posts(array('post_type' => 'post', 'posts_per_page' => -1));
                            foreach ($posts as $post) {
                                echo '<option value="' . esc_attr($post->ID) . '">' . esc_html($post->post_title) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>URL:</th>
                    <td><input type="url" name="url" class="regular-text" style="width: 95%;" placeholder="https://example.com/your-article" required></td>
                </tr>
                <tr>
                    <th>Content:</th>
                    <td><textarea name="content" style="width: 95%; height: 200px;" placeholder="Enter any referring content here..." required></textarea></td>
                </tr>
				<tr>
                    <th></th>
                    <td><input type="submit" name="submit_pingback" class="button button-primary" value="<?php echo esc_attr__('Add Pingback', 'text-domain'); ?>"></td>
                </tr>
            </table>
            
        </form>
    </div>

    <script>
jQuery(document).ready(function($) {
    $('#post-type').on('change', function() {
        var type = $(this).val();
        var data = {
            action: 'get_posts',
            type: type,
            security: '<?php echo wp_create_nonce("get_posts_nonce"); ?>'
        };
        $.post(ajaxurl, data, function(response) {
            $('#posts').html(response);
        });
    });
});
</script>
    <?php
}

add_action('wp_ajax_get_posts', function() {
    check_ajax_referer('get_posts_nonce', 'security');
    
    $type = sanitize_text_field(isset($_POST['type']) ? $_POST['type'] : '');  
    if (empty($type)) {
        wp_send_json_error('Invalid post type');
        return;
    }

    $posts = get_posts(array('post_type' => $type, 'posts_per_page' => -1));
    
    foreach ($posts as $post) {
        echo '<option value="' . esc_attr($post->ID) . '">' . esc_html($post->post_title) . '</option>';
    }
    wp_die();
});