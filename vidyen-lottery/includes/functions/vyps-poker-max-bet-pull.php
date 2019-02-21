<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This will pull from db, which should never change unless admin means to

function vyps_poker_max_bet_pull()
{
  global $wpdb; //Seeing if this needs to be moved up
  //the $wpdb stuff to find what the current name and icons are
  $table_name_poker = $wpdb->prefix . 'videyen_video_poker';

  $first_row = 1; //Note sure why I'm setting this.

  //Point_id pull
	$max_bet_query = "SELECT maximum_bet FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
	$max_bet_query_prepared = $wpdb->prepare( $max_bet_query, $first_row );
	$max_bet = $wpdb->get_var( $max_bet_query_prepared );

  //should be an int anyways
  return intval($max_bet);
}
