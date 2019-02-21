<?php
/*
 * Plugin Name: VidYen Lottery
 * Plugin URI: http://vidyen.com
 * Description: A simple lottery for VidYen Points
 * Author: VidYen, LLC
 * Version: 0.0.1
 * Author URI: http://VidYen.com
 * Text Domain: vidyen-video-poker
 * License: GPLv2
*/

/*
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/shortcodes/videopoker/poker_WP.php'); //SBFG_WP_get_poker_init
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/shortcodes/vidyen-video-poker-shortcode.php'); //Shortcode Init
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-poker-balance-func.php'); //Shortcode Init
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-ajax-deduct.php'); //Add ajax
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-ajax-add.php'); //deduct ajax
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-poker-pid-pull.php'); //PID vyps_poker_pid_pull
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-poker-max-bet-pull.php'); //Not actually used yet.
include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'includes/functions/vyps-poker-multi-pull.php'); //Not actually used yet.
*/

register_activation_hook(__FILE__, 'videyen_lottery_sql_install');

//Install the SQL tables for VYPS.
function videyen_lottery_sql_install()
{

    global $wpdb;

		$charset_collate = $wpdb->get_charset_collate(); //Still haven't figured out the reason i need thi sline

		//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

		//vyps_points table creation
    $table_name_points = $wpdb->prefix . 'vyps_points';

    $sql = "CREATE TABLE {$table_name_points} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		icon text NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

		//vyps_points_log. Notice how I loath th keep variable names the same in recycled code.
		//Visualization people. It's better for code to be ineffecient but readable than efficient and unreadable.
    $table_name_points_log = $wpdb->prefix . 'vyps_points_log';

    $sql .= "CREATE TABLE {$table_name_points_log} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
                reason varchar(128) NOT NULL,
                user_id mediumint(9) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		point_id varchar(11) NOT NULL,
                points_amount double(64, 0) NOT NULL,
                adjustment varchar(100) NOT NULL,
								vyps_meta_id varchar(64) NOT NULL,
								vyps_meta_data varchar(128) NOT NULL,
								vyps_meta_amount double(64,0) NOT NULL,
								vyps_meta_subid1 mediumint(9) NOT NULL,
								vyps_meta_subid2 mediumint(9) NOT NULL,
								vyps_meta_subid3 mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I am concerned that this used ABSPATH rather than the normie WP methods

    dbDelta($sql);
}

//adding menues
add_action('admin_menu', 'vidyen_video_poker_menu');

function vidyen_video_poker_menu()
{
    $parent_page_title = "VidYen Video Poker";
    $parent_menu_title = 'VY Video Poker';
    $capability = 'manage_options';
    $parent_menu_slug = 'vidyen_video_poker';
    $parent_function = 'vidyen_video_poker_menu_page';
    add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);
}

//The actual menu
function vidyen_video_poker_menu_page()
{
	global $wpdb;

	if (isset($_POST['point_id']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vyps-nonce' ) ) {
				// This nonce is not valid.
				die( 'Security check' );
		} else {
				// The nonce was valid.
				// Do stuff here.
		}

		//ID Text value
		$point_id = abs(intval($_POST['point_id'])); //Even though I am in the believe if an admin sql injects himself, we got bigger issues, but this has been sanitized.

		//The icon. I'm suprised this works so well
		$max_bet = abs(intval($_POST['max_bet']));

		//The icon. I'm suprised this works so well
		$win_multi = abs(floatval($_POST['win_multi']));

    $table_name_poker = $wpdb->prefix . 'videyen_video_poker';

	    $data = [
	        'point_id' => $point_id,
	        'maximum_bet' => $max_bet,
					'win_multi' => $win_multi,
	    ];

			$wpdb->update($table_name_poker, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_poker , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

	//the $wpdb stuff to find what the current name and icons are
	$table_name_poker = $wpdb->prefix . 'videyen_video_poker';

	$first_row = 1; //Note sure why I'm setting this.

	//Point_id pull
	$point_id_query = "SELECT point_id FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
	$point_id = $wpdb->get_var( $point_id_query_prepared );

	//max bet pull
	$max_bet_query = "SELECT maximum_bet FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$max_bet_query_prepared = $wpdb->prepare( $max_bet_query, $first_row );
	$max_bet = $wpdb->get_var( $max_bet_query_prepared );

	//multi pull
	$win_multi_query = "SELECT win_multi FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$win_multi_query_prepared = $wpdb->prepare( $win_multi_query, $first_row );
	$win_multi = $wpdb->get_var( $win_multi_query_prepared );


	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($point_id == '')
	{
		$point_id = 1;
	}

	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($max_bet == '')
	{
		$max_bet = 100;
	}

	//Just setting to 1 if nothing else I suppose, but should always be something
	if ($win_multi == '')
	{
		$win_multi = 1;
	}

	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	$vidyen_video_poker_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );


	//Static text for the base plugin
	echo
	'<br><br><img src="' . $vidyen_video_poker_logo_url . '">
	<h1>VidYen Video Poker Sub-Plugin</h1>
	<p>The Video poker!</p>
	<table>
		<form method="post">
			<tr>
				<th>Point ID</th>
				<th>Max Bet</th>
				<th>Win Multi</th>
				<th>Submit</th>
			</tr>
			<tr>
				<td><input type="number" name="point_id" type="number" id="point_id" min="1" step="1" value="' . $point_id .  '" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'. $vyps_nonce_check . '"/></td>
				<td><input type="number" name="max_bet" type="number" id="max_bet" min="1" max="1000000" step="1" value="' . $max_bet . '" required="true"></td>
				<td><input type="number" name="win_multi" type="number" id="win_multi" min="0.01" max="10" step=".01" value="' . $win_multi . '" required="true"></td>
				<td><input type="submit" value="Submit"></td>
			</tr>
		</form>
	</table>
	<h2>Shortcode</h2>
	<p><b>[vidyen-video-poker]</b></p>
	<p>Simply put the shortcode <b>[vidyen-video-poker]</b> on a page and let it run with the point id from the VidYen point system.</p>
	<p>Point ID is the point ID from the VidYen System. Found in Manage Points section of VYPS</p>
	<p>Max bet is how much you want to let them bet in a single hand. Requires session refresh.</p>
	<p>Win Multi is if you want to increase rewards with 2 for 2x the winnings.</p>
	<p>NOTE: If you change this settings while a game is in play, they must close browser or tab and reload page as is server session based.</p>
	<p>Requires the <a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank">VidYen Point System</a> for any point record keeping.</p>
	<br><br><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="' . $vyps_logo_url . '"></a>
	';
}
