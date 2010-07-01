<?
/*
Plugin Name: WP Notifo
Plugin URI: http://hg.errant.me.uk/wp-notifo
Description: Get Notifo messages pushed to your phone whenever someone posts a comment (requires a www.notifo.com account)
Version: 1.0.1
Author: Tom
Author URI: http://errant.me.uk
*/

function notifo_init() {

	add_action('admin_menu', 'notifo_config_page');
}
add_action('init', 'notifo_init');

function notifo_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('Notifo Configuration'), __('Notifo Configuration'), 'manage_options', 'notifo-config', 'notifo_conf');

}

function notifo_conf()
{
    if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
        if (array_key_exists('key',$_POST))
        {
            update_option('notifo_api_key', $_POST['key']);
            update_option('notifo_username', $_POST['username']);
            update_option('notifo_comments', ($_POST['notifo_comments'] == 'true') ? True : False);
            update_option('notifo_password_rest', ($_POST['notifo_password_reset'] == 'true') ? True : False);
        }
        else
        {
            // test
            notifo_message('Test Message','A Quick Test..');
        }
    }
    ?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php (array_key_exists('key',$_POST)) ? _e('Options saved.') : _e('Test Message Sent.')?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Notifo Configuration'); ?></h2>
<div class="narrow">
<form action="" method="post" id="notifo-conf" style="margin: auto; width: 400px; font-family: 'Courier New', Courier, mono;">

<h3><label for="key"><?php _e('Notifo API Key'); ?></label></h3>
<input id="key" name="key" type="text" size="45" maxlength="45" value="<?php echo get_option('notifo_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

<h3><label for="username"><?php _e('Notifo Username'); ?></label></h3>
<input id="username" name="username" type="text" size="15" maxlength="20" value="<?php echo get_option('notifo_username'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

<h3><label for="username"><?php _e('Notification Preferences'); ?></label></h3>
Choose which notifications you want to recieve:<br />
<label for="notifo_comments" style="display: inline-block; width: 200px;" >New Comments:</label>
<input type="checkbox" name="notifo_comments" value="true" <?=(get_option('notifo_comments')) ? 'checked="checked"' : '';?> ><br />
<label for="notifo_comments" style="display: inline-block; width: 200px;" >Password Reset:</label>
<input type="checkbox" name="notifo_password_reset" value="true" <?=(get_option('notifo_password_rest')) ? 'checked="checked"' : '';?> >

<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
</form>
<form action="" method="post" id="notifo-test" style="margin: auto; width: 400px; ">
<p class="submit"><input type="submit" name="submit" value="<?php _e('Test Configuration (sends a test notification)'); ?>" /></p>
</form>

</div>
<?php
}

function notifo_comments($comment,$status)
{
    if($status == 1 and get_option('notifo_comments'))
    {
        // send notifo
        $comment = get_comment($comment);
        $post = get_post($comment->comment_post_ID);
        notifo_message('New Comment','By '.$comment->comment_author.' on "'.$post->post_title.'"',urlencode(get_comment_link($comment)));
        
    }
}
add_action('comment_post', 'notifo_comments', 10, 2);


function notifo_password_reset()
{
    if($status == 1 and get_option('notifo_password_reset'))
    {
        // send notifo
        notifo_message('Password Reset','The wordpress password was reset!');
        
    }
}
add_action('password_reset', 'notifo_password_rest', 10, 0);

function notifo_message($title,$msg,$uri = false)
{
    //send notification to iPhone
    $notifo_pass = get_option('notifo_api_key');
    $notifo_user = get_option('notifo_username');

    echo "sending notifo";
    $data = array('label' => bloginfo('name'), 'title' => $title, 'msg' => $msg);
    if($uri)
    {
        $data['uri'] = $uri;
    }

    $ch = curl_init("https://api.notifo.com/v1/send_notification");

    curl_setopt($ch, CURLOPT_USERPWD, $notifo_user.":".$notifo_pass);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
}