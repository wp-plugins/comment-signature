<?php
/*
Plugin Name: Comment Signature
Plugin URI: http://buffercode.com/
Description: Comment Signature provides easy way to add user's signature who already registered on that site.
Version: 1.0
Author: vinoth06
Author URI: http://buffercode.com/
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action( 'show_user_profile', 'buffecode_comment_sign_edit_show_profile' );
add_action( 'edit_user_profile', 'buffecode_comment_sign_edit_show_profile' );

function buffecode_comment_sign_edit_show_profile( $user ) { ?>
	<h3>Extra profile information</h3>
	<table class="form-table">
	<tr>
	<th><label for="buffercode_cmt_sign_label">User's Signature</label></th>
	<td>
	<textarea name="buffercode_cmt_sign_textarea" class="regular-text" maxlength="100"><?php echo esc_attr( get_the_author_meta( 'buffercode_cmt_sign_textarea', $user->ID ) ); ?> </textarea><br>
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
	$buffercode_cmt_sign_cmt_text.='<br><br><hr width="90%"><code>';
	$buffercode_cmt_sign_cmt_text1 = $comment->user_id;
	$buffercode_cmt_sign_cmt_text.= get_user_meta($buffercode_cmt_sign_cmt_text1, 'buffercode_cmt_sign_textarea', TRUE );
	$buffercode_cmt_sign_cmt_text.='</code>';
	return $buffercode_cmt_sign_cmt_text;
}
?>