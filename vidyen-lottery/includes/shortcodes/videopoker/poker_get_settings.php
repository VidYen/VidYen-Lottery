<?php
/*
 for plugins we just replace this file

*/
global $api_key;
global $maximum_bet;
global $minimum_initial_bonus;
global $maximum_initial_bonus;
global $bonuses_before_deposit;
global $bonus_wins_before_withdraw;
global $maximum_deposit;
global $minimum_deposit;
global $balance_page_leave_confirm;
global $stop_if_adblock;

//error on frist try
if (isset($_SESSION['vidyen_max_bet']))
{
  $vidyen_maximum_bet = $_SESSION['vidyen_max_bet']; //I belew this should work to put our own settings in. Or not. -Felty
}
else
{
  $vidyen_maximum_bet = 100;
}

$api_key = get_option('sfbg_sf_videopoker_api_key','');
$maximum_bet = get_option('sfbg_sf_videopoker_maximum_bet', $vidyen_maximum_bet);
$minimum_initial_bonus = get_option('sfbg_sf_videopoker_minimum_initial_bonus',3);
$maximum_initial_bonus = get_option('sfbg_sf_videopoker_maximum_initial_bonus',11);
$bonuses_before_deposit = get_option('sfbg_sf_videopoker_bonuses_before_deposit',3);
$bonus_wins_before_withdraw = get_option('sfbg_sf_videopoker_wins_before_withdraw',3);
$maximum_deposit = get_option('sfbg_sf_videopoker_maximum_deposit',10000);
$minimum_deposit = get_option('sfbg_sf_videopoker_minimum_deposit',5);
$balance_page_leave_confirm = get_option('sfbg_sf_videopoker_balance_page_leave_confirm',1);
$stop_if_adblock = get_option('sfbg_sf_videopoker_stop_if_adblock',0);
