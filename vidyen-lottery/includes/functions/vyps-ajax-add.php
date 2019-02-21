<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** PHP Functions to handle AJAX request***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_run_add_action', 'vyps_run_add_action');

// handle the ajax request
function vyps_run_add_action()
{
  global $wpdb; // this is how you get access to the database

  //$win_amount = intval( $_POST['win_amount'] ); //NOTE: Probaly not a good idea to pass the win through ajax.

  $win_amount = intval($_SESSION["vidyen_add_win"]); //intval because the gods only know why the server would mess up

  $outgoing_pointid_get = vyps_poker_pid_pull(); //Now with no hardcoding

  // Shortcode additions.
  $atts = shortcode_atts(
    array(
        'outputid' => $outgoing_pointid_get,
        'outputamount' => $win_amount,
        'refer' => 0,
        'to_user_id' => get_current_user_id(),
        'comment' => '',
        'reason' => 'Video Poker Win',
        'btn_name' => 'videowin',
        'raw' => TRUE,
        'cost' => 1,
        'pid' => 1,
        'firstid' => 1,
        'firstamount' => 0,
    ), $atts, 'vyps-pe' );

  //Deduct. I figure there is a check when need to run.
  $add_results = vyps_add_func( $atts );

  echo json_encode($add_results);

  wp_die(); // this is required to terminate immediately and return a proper response
}
