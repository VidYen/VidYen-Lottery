<?php
/*
 * Removing Plugin data using uninstall.php
 * the below function clears the database table on uninstall
 * only loads this file when uninstalling a plugin.
 */

 //NOTE: The vidyen-video-poker plugin SQL table is so small its worth removing through uninstall

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * exit uninstall if not called by WP
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//call the global as always
global $wpdb;

//Clear table one by one
$table_vidyen_lottery_settings = $wpdb->prefix . 'vidyen_lottery_settings';
$wpdb->query( "DROP TABLE IF EXISTS $table_vidyen_lottery_settings" );

//Clear table one by one
$table_vidyen_lottery_games = $wpdb->prefix . 'vidyen_lottery_games';
$wpdb->query( "DROP TABLE IF EXISTS $table_vidyen_lottery_games" );

//Clear table one by one
$table_vidyen_lottery_tickets = $wpdb->prefix . 'vidyen_lottery_tickets';
$wpdb->query( "DROP TABLE IF EXISTS $table_vidyen_lottery_tickets" );
