<?php
/*
language functions, to simplify localization

*/
global $a_poker_lang;
$a_poker_lang = array(
//cm_deposit.php , cm_withdraw.php - not going to localize for now
//index.php
	'poker_deposit' => __('Deposit','vidyen-video-poker'),
	'poker_withdraw' => __('Withdraw','vidyen-video-poker'),
	'poker_bet' => __('Bet','vidyen-video-poker'),
	'poker_balance' => __('Balance','vidyen-video-poker'),
	'poker_deal' => __('Deal !','vidyen-video-poker'),
	'poker_trade' => __('Trade !','vidyen-video-poker'),
	'poker_time_to_play' => __("It's time to play! Click button to deal!",'vidyen-video-poker'),
//a_poker.php
	'poker_got_gonus' => __("You've got bonus %n points",'vidyen-video-poker'),
	'royal_flush' => __("Royal flush",'vidyen-video-poker'),
	'straight_flush' => __("Straight flush",'vidyen-video-poker'),
	'four_of_a_kind' => __("Four of a kind",'vidyen-video-poker'),
	'full_house' => __("Full house",'vidyen-video-poker'),
	'a_flush' => __("A flush",'vidyen-video-poker'),
	'a_straight' => __("A straight",'vidyen-video-poker'),
	'three_of_a_kind' => __("Three of a kind",'vidyen-video-poker'),
	'two_pair' => __("Two pair",'vidyen-video-poker'),
	'jacks_or_better' => __("Jacks or better",'vidyen-video-poker'),
	'almost_deal' => __("Almost! Deal to try again...",'vidyen-video-poker'),
	'you_won' => __("You win %n points",'vidyen-video-poker'),
//poker_util.js
	'points' => __("points",'vidyen-video-poker'),
	'and' => __("and",'vidyen-video-poker'),
	'cancel' => __("Cancel",'vidyen-video-poker'),
	'must_win_1' => __("While playing on the bonus you must win at least",'vidyen-video-poker'),
	'must_win_2' => __("times before withdraw.",'vidyen-video-poker'),
	'enter_address' => __("Enter bitcoin address to withdraw your points",'vidyen-video-poker'),
	'enter_amount' => __("Enter amount of points to deposit",'vidyen-video-poker'),
	'incorrect_amount' => __("is incorrect value for points amount",'vidyen-video-poker'),
	'must_be_between' => __("Must be between",'vidyen-video-poker'),
	'something_went_wrong' => __("Something went wrong, please reset",'vidyen-video-poker'),
	'you_still_have' => __("You still have",'vidyen-video-poker'),
	'disable_adblock' => __("Please disable AdBlock",'vidyen-video-poker'),
//poker.js - pain in the ass
	'must_bet_value' => __("You must enter bet value between",'vidyen-video-poker'),
	'you_have_zero' => __("You have 0 points",'vidyen-video-poker'),
	'deposit_some' => __("Please deposit some",'vidyen-video-poker'),
	'not_that_much' => __("You don't have that much money to bet",'vidyen-video-poker'),
	'bet_to_1' => __("Bet set to 1 points",'vidyen-video-poker'),
	'click_cards_to_reade' => __("Click the cards you want to trade",'vidyen-video-poker'),
	'no_points_reset' => __("You've run out of points! Click 'OK' to reset.",'vidyen-video-poker'),

);

function poker_text($key)
{
	global $a_poker_lang;
	return($a_poker_lang[$key]);
}

function poker_text_to_js()
{
	global $a_poker_lang;

	$ret = "\n<script>";
	foreach ($a_poker_lang as $k => $v)
	{
		$ret .= "\n var poker_text_" . $k . " = \"" . $v . "\";";
	}
	$ret .= "\n</script>\n";
	return($ret);
}
