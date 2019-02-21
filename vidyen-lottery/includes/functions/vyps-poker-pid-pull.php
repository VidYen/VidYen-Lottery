<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This will pull from db, which should never change unless admin means to

function vyps_poker_pid_pull()
{
  global $wpdb; //Seeing if this needs to be moved up
  //the $wpdb stuff to find what the current name and icons are
  $table_name_poker = $wpdb->prefix . 'videyen_video_poker';

  $first_row = 1; //Note sure why I'm setting this.

  //Point_id pull
  $point_id_query = "SELECT point_id FROM ". $table_name_poker . " WHERE id= %d"; //I'm not sure if this is resource optimal but it works. -Felty
  $point_id_query_prepared = $wpdb->prepare( $point_id_query, $first_row );
  $point_id = $wpdb->get_var( $point_id_query_prepared );

  //should be an int anyways
  return intval($point_id);
}
