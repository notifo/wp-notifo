<?
/*
Plugin Name: WP Notifo
Plugin URI: http://hg.errant.me.uk/wp-notifo
Description: Get Notifo messages pushed to your phone whenever someone posts a comment (requires a www.notifo.com account)
Version: 1.1.1
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
        if (array_key_exists('service_key',$_POST))
        {
            update_option('notifo_service_api_key', $_POST['service_key']);
            update_option('notifo_service_username', $_POST['service_username']);
        }
        else
        {
            // test
            notifo_message('Test Message','A Quick Test..');
        }
    }
    ?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php (array_key_exists('key',$_POST)  or array_key_exists('service_key',$_POST)) ? _e('Options saved.') : _e('Test Message Sent.')?></strong></p></div>
<?php endif; ?>


<form action="" method="post" id="notifo-conf" >
<h2><?php _e('Notifo Configuration'); ?></h2>
This section lets you configure notifications for yourself; e.g. so you get updates about new comments.
Log into your Notifo <b>User</b> account and copy/paste your username and API secret

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
<form action="" method="post" id="notifo-test" >
<p class="submit"><input type="submit" name="submit" value="<?php _e('Test Configuration (sends a test notification)'); ?>" /></p>
</form>

<form action="" method="post" id="notifo-service-conf" >
<h2><?php _e('Notifo Service Provider'); ?></h2>
This section requires a Notifo Service account; it lets you to add a widget to your blog allowing people to subscribe for updates.
Log into your Notifo <b>Service</b> account and copy/paste your service username and API secret

<h3><label for="service_key"><?php _e('Notifo Service API Key'); ?></label></h3>
<input id="service_key" name="service_key" type="text" size="45" maxlength="45" value="<?php echo get_option('notifo_service_api_key'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

<h3><label for="service_username"><?php _e('Notifo Service Username'); ?></label></h3>
<input id="service_username" name="service_username" type="text" size="15" maxlength="20" value="<?php echo get_option('notifo_service_username'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" />

<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>

<h4>Subscribed Users</h4>
<? $subscribed = get_option('notifo_subscribed');
    if ($subscribed)
    {
        foreach($subscribed as $username)
        {
            echo "$username<br />";
        }
    }
    
    
?>
</form>

<?php
$id = 4;
notifo_post(get_post($id));
}

function notifo_comments($comment,$status)
{
    if($status == 1 and get_option('notifo_comments'))
    {
        // send notifo
        $comment = get_comment($comment);
        $post = get_post($comment->comment_post_ID);
        $author = get_userdata($post->author);
        notifo_message('New Comment','By '.$comment->comment_author.' on "'.$post->post_title.'"',urlencode(get_comment_link($comment)));
        /*if($author->user_email != $comment->comment_author_email)
        {
            notifo_message('New Comment','By '.$comment->comment_author.' on "'.$post->post_title.'"',urlencode(get_comment_link($comment)));
        }*/
        
    }
}
add_action('comment_post', 'notifo_comments', 10, 2);

function notifo_post($id)
{
    if(is_array(get_option('notifo_subscribed')))
    {
        $post = get_post($id);
        // send notifo
        if($post->post_modified==$post->post_date)
        {
            foreach(get_option('notifo_subscribed') as $user)
            {
            
            notifo_message('New Post',$post->post_title.' by '.get_userdata($post->post_author)->user_nicename,urlencode(get_bloginfo('url').'/'.$post->post_name),$user);
            }
        }
    }
}
add_action('publish_post', 'notifo_post', 10, 1);


function notifo_password_reset()
{
    if($status == 1 and get_option('notifo_password_reset'))
    {
        // send notifo
        notifo_message('Password Reset','The wordpress password was reset!');
        
    }
}
add_action('password_reset', 'notifo_password_rest', 10, 0);

function notifo_message($title,$msg,$uri = false,$to = false)
{
    //send notification to iPhone
    $notifo_pass = get_option('notifo_api_key');
    $notifo_user = get_option('notifo_username');

    $data = array('label' => get_bloginfo('name'), 'title' => $title, 'msg' => $msg);
    if($uri)
    {
        $data['uri'] = $uri;
    }
    if($to)
    {
        $data['to'] = $to;
        $notifo_pass = get_option('notifo_service_api_key');
        $notifo_user = get_option('notifo_service_username');
    }

    $ch = curl_init("https://api.notifo.com/v1/send_notification");

    curl_setopt($ch, CURLOPT_USERPWD, $notifo_user.":".$notifo_pass);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
}

function notifo_subscribe($user)
{
    //send notification to iPhone
    $notifo_pass = get_option('notifo_service_api_key');
    $notifo_user = get_option('notifo_service_username');

    $data = array('username' => $user);

    $ch = curl_init("https://api.notifo.com/v1/subscribe_user");

    curl_setopt($ch, CURLOPT_USERPWD, $notifo_user.":".$notifo_pass);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    
    return json_decode($response);
}

// a widget!
function widget_notifo_init()
{
    function widget_notifo_control()
	{
       ?>
       <p>You can configure this widget on the <a href="plugins.php?page=notifo-config">Notifo configuration page</a></p>
       <?
		

	}
	
    function widget_notifo ($args)
    {
        if(get_option('notifo_service_username') and get_option('notifo_service_api_key'))
        {
            if(array_key_exists('notifo_username',$_POST))
            {
            
                $r = notifo_subscribe($_POST['notifo_username']);
                if($r->status == 'success')
                {
                    $subscribed = get_option('notifo_subscribed');
                    if(!is_array($subscribed))
                    {
                        $subscribed = array();
                    }
                    $subscribed[$_POST['notifo_username']] = $_POST['notifo_username'];
                    update_option('notifo_subscribed', $subscribed);
                    ?>
                    <h3>Notifo Subscription</h3>
                    Subscription request sent - please check your phone to accept.
                    <?
                }
                else
                {
                    ?>
                    <h3>Notifo Subscription</h3>
                    Subscription error problem... did you use the right username?
                    <?
                }
            }
            else
            {
            ?>
            <form action="" method="post" id="notifo-service-conf" >
                <h3>Subscribe to this blog with Notifo</h3>
                Enter your username below to subscribe to notifications from this blog<br />
                <input type="text" name="notifo_username" />
                <p style="submit" >
                    <input type="submit" name="submit_subscribe" value="<?php _e('Subscribe &raquo;'); ?>" />
                </p>
            </form>
            <?
            }
        }
    }
    register_sidebar_widget( 'Notifo', 'widget_notifo' );
	register_widget_control( 'Notifo', 'widget_notifo_control', 440, 120 );
}

add_action( 'plugins_loaded', 'widget_notifo_init' );
