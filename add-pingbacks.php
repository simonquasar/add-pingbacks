<?php
/*
 * Plugin Name:       Add Pingbacks
 * Plugin URI:        https://github.com/simonquasar/add-pingbacks
 * Description:       Manually add a Pingback to any post.
 * Version:           1.2.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
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
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['submit_pingback'])) {
        $post_id = intval($_POST['post_id']);
        $url = esc_url_raw($_POST['url']);
        $content = sanitize_textarea_field($_POST['content']);

        if ($post_id && $url && $content) {
            wp_insert_comment([
                'comment_post_ID' => $post_id,
                'comment_author' => parse_url($url, PHP_URL_HOST),
                'comment_author_url' => $url,
                'comment_content' => $content,
                'comment_type' => 'pingback',
                'comment_approved' => 1
            ]);
            echo '<div class="notice notice-success"><p>Pingback aggiunto!</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Add Pingback</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Post Type:</th>
                    <td>
                        <select id="post-type">
                            <?php 
                            $types = get_post_types(['public' => true], 'objects');
                            foreach ($types as $type) {
                                echo '<option value="' . $type->name . '">' . $type->label . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Post:</th>
                    <td>
                        <select name="post_id" id="posts" required>
                            <?php
                            $posts = get_posts(['post_type' => 'post', 'posts_per_page' => -1]);
                            foreach ($posts as $post) {
                                echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
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
            </table>
            <input type="submit" name="submit_pingback" class="button button-primary" value="Add Pingback">
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#post-type').on('change', function() {
            var type = $(this).val();
            $.post(ajaxurl, {
                action: 'get_posts',
                type: type
            }, function(response) {
                $('#posts').html(response);
            });
        });
    });
    </script>
    <?php
}

add_action('wp_ajax_get_posts', function() {
    $type = $_POST['type'];
    $posts = get_posts(['post_type' => $type, 'posts_per_page' => -1]);
    
    foreach ($posts as $post) {
        echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
    }
    wp_die();
});