<?php
/*
Plugin Name: Comment Signature
Plugin URI: http://buffercode.com/wordpress-comment-signature-plugin/
Description: Comment Signature provides easy way to add user's signature who already registered on that site.
Version: 1.3
Author: vinoth06
Author URI: http://buffercode.com/
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
add_action( 'admin_init', 'buffercode_comment_signature_js',1 );

function buffercode_comment_signature_js() {
wp_enqueue_script( 'buffercode-comment-signature-script',plugins_url('js\jscolor.js',__FILE__) );
}

add_action( 'show_user_profile', 'buffecode_comment_sign_edit_show_profile' );
add_action( 'edit_user_profile', 'buffecode_comment_sign_edit_show_profile' );

function buffecode_comment_sign_edit_show_profile( $user ) { ?>
	<h3>Extra profile information</h3>
	<table class="form-table">
	<tr>
	<th><label for="buffercode_cmt_sign_label">User's Signature</label></th>
	<td>
	<textarea name="buffercode_cmt_sign_textarea" class="regular-text" maxlength="<?php echo get_option('comment_signature_setting_text_field'); ?>"><?php echo esc_attr( get_the_author_meta( 'buffercode_cmt_sign_textarea', $user->ID ) ); ?> </textarea><br>
	<span class="description">Please enter your Signature.</span><br>
		</td>
	</tr>

</table>

<?php
}
add_action( 'personal_options_update', 'buffecode_comment_sign_edit_save_profile' );
add_action( 'edit_user_profile_update', 'buffecode_comment_sign_edit_save_profile' );

function buffecode_comment_sign_edit_save_profile( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	update_user_meta($user_id, 'buffercode_cmt_sign_textarea', $_POST['buffercode_cmt_sign_textarea'] );
}

add_filter( 'comment_text', 'buffecode_comment_sign_cmt_text' );

function buffecode_comment_sign_cmt_text( $buffercode_cmt_sign_cmt_text) {
	global $comment;
	$buffercode_cmt_sign_cmt_text.='<!-- Buffercode.com Comment Signature --><br><br><hr width="90%"><code style="white-space: wrap;"><font color=#"'.get_option('comment_signature_setting_text_color').'">';
	$buffercode_cmt_sign_cmt_text1 = $comment->user_id;
	$buffercode_cmt_sign_cmt_text.= get_user_meta($buffercode_cmt_sign_cmt_text1, 'buffercode_cmt_sign_textarea', TRUE );
	$buffercode_cmt_sign_cmt_text.='</font></code><!-- Buffercode.com Comment Signature -->';
	return $buffercode_cmt_sign_cmt_text;
}

add_action('admin_menu', 'buffecode_comment_sign_menu');
function buffecode_comment_sign_menu() {

	add_options_page( 'Comment Signature', 'Comment Signature', 'manage_options', __FILE__, 'comment_signature_settings' );

	//call register settings function
	add_action( 'admin_init', 'comment_signature_register_settings' );
}

function comment_signature_register_settings(){
register_setting( 'comment_signature_setting_group', 'comment_signature_setting_text_field' );
register_setting( 'comment_signature_setting_group', 'comment_signature_setting_text_color' );
}

function comment_signature_settings(){
?>
<div class="wrap">
<h2>Comment Signature Options</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'comment_signature_setting_group' ); ?>
    <?php do_settings_sections( 'comment_signature_setting_group' );?>
	 
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Text Size</th>
        <td><input type="text" name="comment_signature_setting_text_field" value="<?php echo get_option('comment_signature_setting_text_field'); ?>" /></td>
        </tr>
		
			<tr valign="top">
        <th scope="row">Font Color</th>
        <td><input type="text" class="color {required:false,pickerClosable:true}"  name="comment_signature_setting_text_color"  value="<?php echo get_option('comment_signature_setting_text_color'); ?>" /></td>
        </tr>
        	
		 <tr valign="top">
        <th scope="row">Designed by - <a href="http://buffercode.com">Buffercode</a></th>
        </tr>
    </table>
        <?php submit_button();  ?>

</form>
</div>
<?php
}
?>