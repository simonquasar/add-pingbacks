<?php
/*
Plugin Name: Add Pingbacks
Plugin URI: http://simonquasar.net/add-pingbacks
Description: Manually add Pingbacks to a Post or a Page
Version: 1.1
Author: simonquasar
Author URI: http://simonquasar.net
License: GPLv2 
Copyright 2014 Simon Pilati (http://simonquasar.net)
*/

function addPingbacks_set_plugin_meta( $links, $file ) { 
	$plugin_base = plugin_basename(__FILE__);
    if ( $file == $plugin_base ) {
		$newlinks = array( '<a href="options-general.php?page=addPingbacks">Add Pingback</a>' ); 
		return array_merge( $links, $newlinks );
	}
	return $links;
}

function addPingbacks_options_init() { 
	register_setting( 'addPingbacks-group', 'addPingbacks-options', 'addPingbacks_validate_input' );
}

function addPingbacks_validate_input( $input ) {
	return $input;
}

function addPingbacks_options_link() { 
	add_comments_page( 'Add Pingbacks', 'Add Pingbacks', 'manage_options', 'addPingbacks', 'addPingbacks_options_page' );
}
 
function addPingback_select_box( $posttype ) {
	
	if( $posttype == 'page' )
		$args = array( 
            'numberposts'	=>	'9000',
			'post_type'		=>	'page',
			'post_status'	=>	'all' );
	
    else
        $args = array( 
            'numberposts'	=>	'9000',
            'post_type'		=>	'post',
            'post_status'	=>	'all' );
	
	$items = get_posts( $args );
	
	echo '<select name="' . $posttype . 's_list" id="' . $posttype . '">';
	
	foreach( $items as $item )
		echo '<option value="' . $item->ID . '">' . apply_filters('the_title',$item->post_title) . '</option>';
        
}

function addPingback_text_box( $label, $name, $default=NULL ) { ?>
					<tr>
						<td><label for="<?php echo $name ?>"><?php echo $label ?></label></th>
						<td colspan="2"><input type="text" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo $default; ?>"/></td>
					</tr>
<?php 
}

function addPingbacks_add_comments( $id, $author, $email, $url, $ip, $comment ) {
						
	if( empty( $comment ) ) { return false;} 
    else {
    wp_insert_comment( array(
    'comment_post_ID' => $id,
    'comment_author' => $author,
    'comment_author_email' => $email,
    'comment_author_url' => $url,
    'comment_content' => $comment,
    'comment_type' => 'pingback',
    'comment_parent' => 0,
    'user_id' => '',
    'comment_author_IP' => $ip,
    'comment_agent' => 'Add Pingbacks Plugin',
    'comment_date' => current_time( 'mysql' ),
    'comment_approved' => 1 ) );
	return true;
    }
}

function addPingbacks_options_page() { 
	
	if( $_POST['action'] == 'addpingback' ) {
		echo '<div class="updated settings-error">';
		$id = ( $_POST['post_or_page'] == 'page' ? $_POST['pages_list'] : $_POST['posts_list'] );
		$author = ( isset( $_POST['author_name'] ) ? $_POST['author_name'] : 'anonymous' );
		$email = ( isset( $_POST['author_email'] ) ? $_POST['author_email'] : get_bloginfo('admin_email' ) );
		$url = ( isset( $_POST['author_url'] ) ? $_POST['author_url'] : '' );
		$ip = ( isset( $_POST['author_ip'] ) ? $_POST['author_ip'] : '127.0.0.1' );
		echo addPingbacks_add_comments( $id, $author, $email, $url, $ip, $_POST['comment'] ) 
		. ' Pingback added to ' . get_the_title( $id ) . '</div>';
	} ?>

	<div class="wrap">
    	<div class="icon32" id="icon-options-general"><br /></div>
		<h2>Add Pingback URLs</h2>
		<span class="description">Select a Post or a Page, then add the referral URL which points to your content. Play fair. ;)<br/>
        Plugin by <a href="http://simonquasar.net" target="_blank" title="simonquasar">simonquasar</a></span>
		<form method="post" action="">
			
			<table class="form-table">
				<tbody>
					<tr>
                    <th colspan="3" style="font-size:1.3em">Where to add?</th></tr>
                    
					<tr>
						<td style="width: 150px;"><strong>Post</strong></td>
						<td><input type="radio" name="post_or_page" value="post" id="post" checked="checked" /><?php addPingback_select_box( 'post' ); ?></td>
					</tr>
                    
					<tr>
						<td><strong>Page</strong></td>
						<td><input type="radio" name="post_or_page" value="page" id="page" /><?php addPingback_select_box( 'page' ); ?></td>
					</tr>
                    
					<tr>
                    <th colspan="3" style="font-size:1.3em">Referrer link</th></tr>
					
					<?php 
					$authors = array(
						array( 'name' => 'author_name', 'label' => 'Site Title / Page Name', 'default' => '' ),
						array( 'name' => 'author_url', 'label' => 'Link', 'default' => 'http://' ) ); 
						
					foreach( $authors as $author )
						addPingback_text_box( $author['label'], $author['name'], $author['default'] );
					?>
					
					<tr>
						<th colspan="2" style="font-size:1.3em">Excerpt / Content</th>
					</tr>
					<tr>
						<td colspan="3"><textarea name="comment" id="comment" cols="120" rows="5">[...] cit. [...]</textarea></td>
					</tr>
                    
				</tbody>
			</table>
            
			<p class="submit">
				<input type="hidden" name="action" value="addpingback" />
				<input type="submit" class="button-primary" value="Add Link Reference" />
			</p>
            
		</form>
	</div>
<?php } 

add_filter( 'plugin_row_meta', 'addPingbacks_set_plugin_meta', 10, 2 );
add_action( 'admin_init', 'addPingbacks_options_init' );
add_action( 'admin_menu', 'addPingbacks_options_link' );
?>