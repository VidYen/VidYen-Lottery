<?php

/*** Shortcode goes here like civilized nations do ***/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function videopoker_shortcode_top()
{
  $ret = '';

  $ret .= "\n<link rel='stylesheet' href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css'>";
  $ret .= "\n<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'></script>";
  $ret .= "\n<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>";

  //do it once, do it here so can be defined in standalone version, poker_util.php
  if(!function_exists ( 'poker_get_main_url' ))
  {
    function poker_get_main_url() //
    {
      return(plugin_dir_url( __FILE__ ).'videopoker/');
    }
  }

  include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'videopoker/poker_get_settings.php');
  include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'videopoker/poker_util.php');
  $ret .= settings_to_js();
  if($stop_if_adblock)
    $ret .= "\n<script src='" . plugin_dir_url( __FILE__ ) . "videopoker/lib/advertisment.js'></script>\n";
  include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'videopoker/poker_lang_WP.php');
  $ret .= poker_text_to_js();

  $ret .= "\n<link href='" . plugin_dir_url( __FILE__ ) . "videopoker/lib/messagebox.css' rel='stylesheet'>";
  $ret .= "\n<script src='" . plugin_dir_url( __FILE__ ) . "videopoker/lib/messagebox.js'></script>";
  $ret .= "\n<link href='" . plugin_dir_url( __FILE__ ) . "videopoker/poker.css' rel='stylesheet'>";
  $ret .= "\n<script src='" . plugin_dir_url( __FILE__ ) . "videopoker/poker.js'></script>	";
  $ret .= "\n<script src='" . plugin_dir_url( __FILE__ ) . "videopoker/poker_util.js'></script>";
  return($ret);
}

function videopoker_shortcode_body()
{
  $ret = '';
  $ret .= "\n";
  include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'videopoker/poker_WP.php');
  SBFG_WP_poker_settings_to_session();
  $ret .= SBFG_WP_get_poker_body();
  return($ret);
}

function videopoker_shortcode_localize()
{
  $ret = '';
  return($ret);
}

function videopoker_shortcode( $atts )
{
  //Login check.
  if ( ! is_user_logged_in() )
  {
      return 'Please log in to play VidYen Video Poker!';
  }
  vidyen_poker_balance_func(); //First load to create variables
  $ret = '';
  $ret .= videopoker_shortcode_localize();
  $ret .= videopoker_shortcode_top();
  $ret .= videopoker_shortcode_body();
  vidyen_poker_balance_func(); //Second load to overwrite variables
  return($ret);
}

add_shortcode('vidyen-video-poker', 'videopoker_shortcode');
