<?php
/*
  Plugin Name: Comment Signature
  Plugin URI: http://buffercode.com/project/comment-signature/
  Description: Comment Signature provides easy way to add user's signature who already registered on that site.
  Version: 2.1
  Author: vinoth06
  Author URI: http://buffercode.com/
  License: GPLv2
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
add_action('admin_init', 'buffercode_comment_signature_js', 1);

function buffercode_comment_signature_js() {
    wp_enqueue_script('buffercode-comment-signature-script', plugins_url('js\jscolor.js', __FILE__));
}

add_action('show_user_profile', 'buffecode_comment_sign_edit_show_profile');
add_action('edit_user_profile', 'buffecode_comment_sign_edit_show_profile');

function buffecode_comment_sign_edit_show_profile($user) {
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="buffercode_cmt_sign_label">User's Signature</label></th>
            <td>
                <textarea name="buffercode_cmt_sign_textarea" class="regular-text" maxlength="<?php echo get_option('comment_signature_setting_text_field'); ?>"><?php echo esc_attr(get_the_author_meta('buffercode_cmt_sign_textarea', $user->ID)); ?> </textarea><br>
                <span class="description">Please enter your Signature.</span><br>
            </td>
        </tr>

    </table>

    <?php
}

add_action('personal_options_update', 'buffecode_comment_sign_edit_save_profile');
add_action('edit_user_profile_update', 'buffecode_comment_sign_edit_save_profile');

function buffecode_comment_sign_edit_save_profile($user_id) {
    if (!current_user_can('edit_user', $user_id))
        return false;
    update_user_meta($user_id, 'buffercode_cmt_sign_textarea', $_POST['buffercode_cmt_sign_textarea']);
}

add_filter('comment_text', 'buffecode_comment_sign_cmt_text');

function buffecode_comment_sign_cmt_text($buffercode_cmt_sign_cmt_text) {
    global $comment;

    $buffercode_cmt_sign_cmt_text1 = $comment->user_id;
    $buffercode_cmt_sign_cmt_text2 = get_user_meta($buffercode_cmt_sign_cmt_text1, 'buffercode_cmt_sign_textarea', TRUE);
    if ($buffercode_cmt_sign_cmt_text2 == '') {
        return $buffercode_cmt_sign_cmt_text;
    } else {
        $margin = get_option('comment_signature_margin');
        $hr_value = get_option('comment_signature_hr');
        $margin['top'] = !empty($margin['top']) ? $margin['top'] : 10;
        $margin['right'] = !empty($margin['right']) ? $margin['right'] : 0;
        $margin['bottom'] = !empty($margin['right']) ? $margin['bottom'] : 0;
        $margin['left'] = !empty($margin['right']) ? $margin['left'] : 0;
        $hr = !empty($hr_value) ? $hr_value : 100;
        
        $buffercode_cmt_sign_cmt_text.='<hr style="margin: '.$margin['top'].'px '.$margin['right'].'px '.$margin['bottom'].'px '.$margin['right'].'px; width:'.$hr.'%"><code style="white-space: wrap;"><span style="color:#' . get_option('comment_signature_setting_text_color') . '; font-size:' . get_option('comment_signature_setting_text_field') . 'px;">';
        $buffercode_cmt_sign_cmt_text.= get_user_meta($buffercode_cmt_sign_cmt_text1, 'buffercode_cmt_sign_textarea', TRUE);
        $buffercode_cmt_sign_cmt_text.='</span></code>';
        return $buffercode_cmt_sign_cmt_text;
    }
}

add_action('admin_menu', 'buffecode_comment_sign_menu');

function buffecode_comment_sign_menu() {

    add_options_page('Comment Signature', 'Comment Signature', 'manage_options', __FILE__, 'comment_signature_settings');

    //call register settings function
    add_action('admin_init', 'comment_signature_register_settings');
}

function comment_signature_register_settings() {
    register_setting('comment_signature_setting_group', 'comment_signature_setting_text_field');
    register_setting('comment_signature_setting_group', 'comment_signature_setting_text_color');
    register_setting('comment_signature_setting_group', 'comment_signature_margin');
    register_setting('comment_signature_setting_group', 'comment_signature_hr');
    
    
}

function comment_signature_settings() {
    ?>
    <div class="wrap">
        <h2>Comment Signature Options</h2>

        <form method="post" action="options.php">
            <?php settings_fields('comment_signature_setting_group'); ?>
            <?php do_settings_sections('comment_signature_setting_group'); ?>
            <?php $margin = get_option('comment_signature_margin'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Text Size</th>
                    <td><input type="number" name="comment_signature_setting_text_field" value="<?php echo get_option('comment_signature_setting_text_field'); ?>" />px</td>
                </tr>

                <tr valign="top">
                    <th scope="row">Font Color</th>
                    <td><input type="text" class="color {required:false,pickerClosable:true}"  name="comment_signature_setting_text_color"  value="<?php echo get_option('comment_signature_setting_text_color'); ?>" /></td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Horizontal Line Width</th>
                    <td><input type="number" name="comment_signature_hr"  value="<?php echo get_option('comment_signature_hr'); ?>" /> %</td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Margin</th>
                    <td><input type="number"  name="comment_signature_margin[top]"  value="<?php echo $margin['top']; ?>" />px [Top]</td>
                    <td><input type="number"  name="comment_signature_margin[right]"  value="<?php echo $margin['right']; ?>" />px [Right]</td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"></th>
                    <td><input type="number"  name="comment_signature_margin[bottom]"  value="<?php echo $margin['bottom']; ?>" />px [Bottom]</td>
                    <td><input type="number"  name="comment_signature_margin[left]"  value="<?php echo $margin['left']; ?>" />px [Left]</td>
                </tr>


            </table>
            <?php submit_button(); ?>

        </form>
        
        <!-- Buffercode.com Feeds Starts -->

        <div class="wrap">
            <h2>Our Other Works</h2>
            <?php
            // Get RSS Feed(s)
            include_once( ABSPATH . WPINC . '/feed.php' );
            $rss = fetch_feed('http://buffercode.com/cat-portifolio/our-works/feed/');

            $maxitems = 0;

            if (!is_wp_error($rss)){
                $maxitems = $rss->get_item_quantity(20);
                $rss_items = $rss->get_items(0, $maxitems);
            }
            ?>

            <ul>
                <?php if ($maxitems == 0) : ?>
                    <li><?php _e('Something Went Wrong', 'bc_comment_signature'); ?></li>
                <?php else : ?>
                    <table class="form-table"  style="background-color:#d3d3d3; border-radius: 10px;">
                        <tr valign="top">
                            <?php foreach ($rss_items as $item) : ?>
                            <td>
                                    <a href="<?php echo esc_url($item->get_permalink()); ?>"
                                       title="<?php printf(__('Posted %s', 'bc_comment_signature'), $item->get_date('j F Y | g:i a')); ?>">
                                           <?php echo esc_html($item->get_title()); ?>
                                    </a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Buffercode.com Feeds Ends -->
    </div>
    <?php
}
