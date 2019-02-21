<?php
//just for readability, to not ot overwhelm main plugin file
if( !session_id() )
{
    session_start();
}


function SBFG_WP_get_poker_init()
{
//	SBFG_WP_poker_settings_to_session();
	if(isset($_GET['CRYPTOOMEDEPOSIT']))
	{
		return SBFG_WP_poker_do_deposit($_GET['CRYPTOOMEDEPOSIT']);
	}
	if(isset($_GET['CRYPTOOMEWITHDRAW']))
	{
		return SBFG_WP_poker_do_withdraw($_GET['CRYPTOOMEWITHDRAW']);
	}
}


function SBFG_WP_get_poker_body()
{
  /*** VidYen Code ***/
  $VYPS_power_url = plugins_url( 'images/', dirname(__FILE__) ) . 'powered_by_vyps.png'; //Well it should work out.
  $VYPS_power_row = "<div align=\"left\">Powered by <a href=\"https://wordpress.org/plugins/vidyen-point-system-vyps/\" target=\"_blank\"><img src=\"$VYPS_power_url\" alt=\"Powered by VYPS\"></a></div>";

  $atts = array(
        'pro' => '',
    );

  //Procheck here. Do not forget the ==
  if (vyps_procheck_func($atts) == 1)
  {
    $VYPS_power_row = ''; //No branding if procheck is correct.
  }
  /*** END VidYen Code ***/

	$ret = "
<style>
.vp_fancy_bg{
background:url(\"" . poker_get_main_url() . "img/bg2.jpg\");
}
</style>
<div class='maintable_wrapper'><center>
<table class='maintable vp_fancy_bg'>
  <tr >
	<td colspan='5'>
		<table class='titletable' border=0 style='width:100%;'>
			<tr>
				<td colspan='2'><label >" . poker_text('poker_bet') . ":</label> <input type='text' name='bet' id='vp_bet'  value='5' size='5' /></td>
				<td colspan='2'><label >" . poker_text('poker_balance') . ":</label> <input type='text' id='vp_balance' readonly='readonly' name='money'  value='0' size='5' /></td>
			</tr>
		</table>
	</td>
  </tr>
  <tr>
		<td class='vp_card' onclick='flipcard(0)'><img id='vp_c0' class='vp_card'  /></td>
		<td class='vp_card' onclick='flipcard(1)'><img id='vp_c1' class='vp_card'  /></td>
		<td class='vp_card' onclick='flipcard(2)'><img id='vp_c2' class='vp_card'  /></td>
		<td class='vp_card' onclick='flipcard(3)'><img id='vp_c3' class='vp_card'  /></td>
		<td class='vp_card' onclick='flipcard(4)'><img id='vp_c4' class='vp_card'  /></td>
  </tr>
  <tr>
		<td class='control' colspan='5'><input  type='button' name='deal' id='vp_deal' value=\"" . poker_text('poker_deal') . "\" onclick='dealcards(this.form)' /></td>
  </tr>
  <tr>
		<td class='control' colspan='5'><input class='vp_msg' type='text' id='vp_info' readonly='readonly' name='info' value=\"" . poker_text('poker_time_to_play') . "\" size='50' /></td>
  </tr>
</table>
<br>$VYPS_power_row
<script>vp_trof_set_bg();WP_vp_trof_update();</script>
<script>
</script>
</center></div>
	";
 return $ret;
}

function SBFG_WP_poker_settings_to_session()
{
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

  $maximum_bet = $vidyen_maximum_bet; //Prolly not needed.

	$_SESSION['vp_s_api_key'] = $api_key;
	$_SESSION['vp_s_maximum_bet'] = $maximum_bet;
	$_SESSION['vp_s_minimum_initial_bonus'] = $minimum_initial_bonus;
	$_SESSION['vp_s_maximum_initial_bonus'] = $maximum_initial_bonus;
	$_SESSION['vp_s_bonuses_before_deposit'] = $bonuses_before_deposit;
	$_SESSION['vp_s_wins_before_withdraw'] = $bonus_wins_before_withdraw;
	$_SESSION['vp_s_maximum_deposit'] = $maximum_deposit;
	$_SESSION['vp_s_minimum_deposit'] = $minimum_deposit;
	$_SESSION['vp_s_balance_page_leave_confirm'] = $balance_page_leave_confirm;
	$_SESSION['vp_s_stop_if_adblock'] = $stop_if_adblock;
}

function SBFG_WP_poker_do_withdraw($address)
{
	ob_clean();


	if(strlen($address) > 0) //correct?
	{
		SBFG_WP_poker_do_withdraw_to_address($address);//start transaction
	}
	else
	{
		die('Address must me set');//go away
	}
}

function SBFG_WP_poker_do_withdraw_to_address($address)
{
//print_r($_SESSION);
	$api_key = get_option('sfbg_sf_videopoker_api_key','');
	$bonus_wins_before_withdraw = get_option('sfbg_sf_videopoker_wins_before_withdraw',3);

	if(strlen($api_key) < 40)
	{
		die('Please provide correct cryptoo.me API Key');
	}


	$balance = intval($_SESSION["cm_balance"]);
	if($balance <= 0)
	{
		$msg = "Your balance is $balance points. Nothing to withdraw yet!";
		die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
	}

	if($_SESSION["cm_wins_after_bonus"] < $bonus_wins_before_withdraw)
	{
		$js = "<script>window.opener.postMessage(\"vp_withdraw\",\"*\" );</script>";
		$msg = "When playing on the bonus you must win at least ".$bonus_wins_before_withdraw." times before withdraw.";
		die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
	}


	$fields = array(
	'api_key'=> $api_key,
	'to'=>$address,
	'amount'=>$balance
	);
//print_r($fields); die;

/*
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
*/
	$out = wp_remote_post( 'https://cryptoo.me/api/v1/send', array(
		'method' => 'POST',
		'body' => $fields)  );
	$out_ison = json_decode($out['body']);

//print_r($out_ison); die(' 1');
	if($out_ison->status == 200)
	{
		$_SESSION["cm_balance"] = 0;
		$_SESSION["cm_real_mode"] = false;
		$js = "<script>window.opener.postMessage(\"vp_withdraw\",\"*\" );";
		$js .= "window.location='https://cryptoo.me/check/$address'</script>";
		die("$js");
	}
//var_dump($out_ison);
//var_dump($res);
	$msg = $out_ison->message;//$out_ison->message;
	die("<br><center>$msg<br><button onclick='window.close()'>close</button></center>");
//if we here - no success, error!
}//do_withdraw

function SBFG_WP_poker_do_deposit($amount)
{
	ob_clean();

	//$api_key = get_option('sfbg_sf_videopoker_api_key','');
	$maximum_deposit = get_option('sfbg_sf_videopoker_maximum_deposit',10000);
	$minimum_deposit = get_option('sfbg_sf_videopoker_minimum_deposit',5);

  /*
	if(strlen($api_key) < 40)
	{
		die('Please provide correct  cryptoo.me API Key');
	}
  */

	$amount = intval($amount); //to make sure

	if(!isset($_GET['data']))
	{
		if(($amount >= $minimum_deposit) && ($amount <= $maximum_deposit)) //correct?
		{
			SBFG_WP_poker_step_one($amount);//start transaction
		}
		else
		{
			die("Amount must be between $minimum_deposit and $maximum_deposit");//go away
		}
	}
	else //no amount - we check
	{
		SBFG_WP_poker_step_two();//check transaction
	}
	die;
}

function SBFG_WP_poker_step_two()
{
	$api_key = get_option('sfbg_sf_videopoker_api_key','');

	if(	( intval($_SESSION["cm_deposit_amount"]) > 0) && ($_SESSION["cm_deposit_invid"]) )
	{
		$fields = array(
			'key'=> $api_key,
			'invid'=>$_SESSION["cm_deposit_invid"]
		);
//print_r($fields); die();
/*
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://cryptoo.me/api/v1/invoice/state/');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		$out = curl_exec($curl);
		curl_close($curl);
		$out_ison = json_decode($out);
*/

		$out = wp_remote_post( 'https://cryptoo.me/api/v1/invoice/state/', array(
			'method' => 'POST',
			'body' => $fields)  );
		$out_ison = json_decode($out['body']);

		if($out_ison->success == true)
		{
			if($_SESSION["cm_deposit_amount"] == $out_ison->invoice->amount)
			{
				$balace = intval($_SESSION["cm_balance"]);
				$balace += intval($out_ison->invoice->amount);
				$_SESSION["cm_balance"] = $balace;
				$_SESSION["cm_bonuses_diven"] = 0;
				$ret = 'Balance: ' . $balace . ' points';
				$deposits = intval($_SESSION["cm_deposits"]);
				$deposits++;
				$_SESSION["cm_deposits"] = $deposits;
				$_SESSION["cm_wins_after_bonus"] = 9999; //allow to withdraw
			}
			else
			{
				$ret = $out_ison->message . '<br>Amount error. Try again';
			}
		}
		else
		{
			$ret = 'Transaction error. Try again';
		}
		unset($_SESSION["cm_deposit_amount"]);
		unset($_SESSION["cm_deposit_invid"]);
	}
	else //session check
	{
		$ret = 'Unexpected data. Try again';
	}
	$js = "<script>window.opener.postMessage(\"vp_deposit\",\"*\" );</script>";
	die("$js<center>$ret<br><button onclick='window.close()'>close</button></center>");
}//step_two

function  SBFG_WP_poker_step_one($amount)
{
	$api_key = get_option('sfbg_sf_videopoker_api_key','');

	if(!function_exists('curl_version'))
	{
		die('cURL is not enabled on this hosting');
	}


	$server_request_scheme = 'http';
	if( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
		(! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
		(! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
		$server_request_scheme = 'https';
	}

	$self_url = $server_request_scheme . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

/*
	if ($pos_get = strpos($self_url, '?'))
	{
		$self_url = substr($self_url, 0, $pos_get);
	}
*/
	$callback_url = $self_url . '';


	$fields = array(
		'key'=>$api_key,
		'amount'=>$amount,
		'notice'=>'Deposit for VP',
		'data'=>'VP'.$amount,
		'redirect_url'=>$callback_url,
	);
//print_r($fields); die();
/*
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://cryptoo.me/api/v1/invoice/create/');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
	$out = curl_exec($curl);
	if($errno = curl_errno($curl))
	{
		$error_message = curl_strerror($errno);
		die("cURL error ({$errno}):\n {$error_message},\nMake sure cURL is configured properly.");
	}
	curl_close($curl);
	$out_ison = json_decode($out);
*/

	$out = wp_remote_post( 'https://cryptoo.me/api/v1/invoice/create/', array(
		'method' => 'POST',
		'body' => $fields)  );
	$out_ison = json_decode($out['body']);
//print_r($out_ison); die();
	$_SESSION["cm_deposit_amount"] = $amount;
	$_SESSION["cm_deposit_invid"] = $out_ison->invid;

	header('Location: https://cryptoo.me/api/v1/invoice/open/'.$out_ison->invid);
}//step_one
