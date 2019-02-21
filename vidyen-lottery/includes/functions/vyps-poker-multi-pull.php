<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This will pull from db, which should never change unless admin means to

function vyps_poker_multi_pull()
{
  global $wpdb; //Seeing if this needs to be moved up
  //the $wpdb stuff to find what the current name and icons are
  $table_name_poker = $wpdb->prefix . 'videyen_video_poker';

  $first_row = 1; //Note sure why I'm setting this.

  //Point_id pull
	$win_multi_query = "SELECT win_multi FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$win_multi_query_prepared = $wpdb->prepare( $win_multi_query, $first_row );
	$win_multi = $wpdb->get_var( $win_multi_query_prepared );

  //should be an int anyways
  return floatval($win_multi);
}
