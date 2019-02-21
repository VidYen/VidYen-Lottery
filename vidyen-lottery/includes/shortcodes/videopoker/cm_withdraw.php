<?php
//relies on session! don't forget tto do session_start(); in the main page!
session_start();
//include_once( dirname(__FILE__) . DIRECTORY_SEPARATOR .  'poker_get_settings.php');

	$api_key = $_SESSION['vp_s_api_key'];
	$maximum_bet = $_SESSION['vp_s_maximum_bet'];
	$minimum_initial_bonus = $_SESSION['vp_s_minimum_initial_bonus'];
	$maximum_initial_bonus = $_SESSION['vp_s_maximum_initial_bonus'];
	$bonuses_before_deposit = $_SESSION['vp_s_bonuses_before_deposit'];
	$bonus_wins_before_withdraw = $_SESSION['vp_s_wins_before_withdraw'];
	$maximum_deposit = $_SESSION['vp_s_maximum_deposit'];
	$minimum_deposit = $_SESSION['vp_s_minimum_deposit'];
	$balance_page_leave_confirm =$_SESSION['vp_s_balance_page_leave_confirm'];
	$stop_if_adblock = $_SESSION['vp_s_stop_if_adblock'];


if(strlen($api_key) < 40)
{
	die('Please provide correct cryptoo.me API Key');
}

//GET flags:
//	?address=1GHrzqB6Ngab1gvZDd2tyTXxigziy26L6s - adress to withdraw.



if(isset($_GET['address']))
{
	if(strlen($_GET['address']) > 0) //correct?
	{
		do_withdraw($_GET['address']);//start transaction
	}
	else
	{
		die('Address must me set');//go away
	}
}


function do_withdraw($address)
{
//print_r($_SESSION);
	$balance = intval($_SESSION["cm_balance"]);
	if($balance <= 0)
	{
		$msg = "Your balance is $balance points. Nothing to withdraw yet!";
		die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
	}

	global $bonus_wins_before_withdraw;
	if($_SESSION["cm_wins_after_bonus"] < $bonus_wins_before_withdraw)
	{
		$js = "<script>window.opener.postMessage(\"vp_withdraw\",\"*\" );</script>";
		$msg = "When playing on the bonus you must win at least ".$bonus_wins_before_withdraw." times before withdraw.";
		die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
	}

	global $api_key;
	$fields = array(
	'api_key'=> $api_key,
	'to'=>$address,
	'amount'=>$balance
	);
//print_r($fields);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://cryptoo.me/api/v1/send');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
	$res = curl_exec($curl);
//print_r($res); die(' 0');
	if($errno = curl_errno($curl))
	{
		$error_message = curl_strerror($errno);
		die("cURL error ({$errno}):\n {$error_message},\nMake sure cURL is configured properly.");
	}

	curl_close($curl);

	$out_ison = json_decode($res,true);
//print_r($out_ison); die(' 1');
	if($out_ison["status"] == 200)
	{
		$_SESSION["cm_balance"] = 0;
		$_SESSION["cm_real_mode"] = false;
		$js = "<script>window.opener.postMessage(\"vp_withdraw\",\"*\" );";
		$js .= "window.location='https://cryptoo.me/check/$address'</script>";
		die("$js");
	}
//var_dump($out_ison);
//var_dump($res);
	$msg = $out_ison["message"];//$out_ison->message;
	die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
//if we here - no success, error!
}//do_withdraw


?>
