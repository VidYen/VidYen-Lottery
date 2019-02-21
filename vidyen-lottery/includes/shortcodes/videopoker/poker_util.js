

var withdraw_popup;
vp_trof_withdraw = function(){
	if(withdraw_popup)
	{
		withdraw_popup.close();
	}
	url = '/?CRYPTOOMEWITHDRAW=';
//	url = poker_main_url + 'cm_withdraw.php?address=';
	if(withdraw_popup)
	{
		withdraw_popup.close();
	}
	if(trof_wins_after_bonus < bonus_wins_before_withdraw)
	{
		jQuery.MessageBox(poker_text_must_win_1+' '+bonus_wins_before_withdraw+' '+poker_text_must_win_2);	
		return;
	}
	var address = localStorage.getItem("bitcoin_addr");
	if(!address)
	{
		address = '';
	}
	jQuery('body').on('click', '.messagebox_button_done', function(){
		withdraw_popup = window.open(url,'withdraw_popup');
	});	
	jQuery.MessageBox({
		input    : address,
		message  : poker_text_enter_address,
		buttonFail  : poker_text_cancel,
	}).done(function(data){
		jQuery('body').off('click', '.messagebox_button_done');
	    var addr = jQuery.trim(data);
		localStorage.setItem("bitcoin_addr",addr);
		url = url + addr;
		withdraw_popup.location = url;
		var timer = setInterval(function(){
			if (withdraw_popup.closed) {
//console.log('Withdraw window closed');
				clearInterval(timer);
			}
		}, 100);
	})
	.fail(function(){
		jQuery('body').off('click', '.messagebox_button_done');
	});
}//trof_withdraw


var deposit_popup;
vp_trof_deposit = function(){
	if(deposit_popup)
	{
		deposit_popup.close();
	}
	url = '/?CRYPTOOMEDEPOSIT=';
//	url = poker_main_url+'cm_deposit.php?amount=';
	
	jQuery('body').on('click', '.messagebox_button_done', function(){
		deposit_popup = window.open(url,'deposit_popup');
	});
	jQuery.MessageBox({
		input    : '100',
		message  : poker_text_enter_amount,
		buttonFail  : poker_text_cancel,
	}).done(function(data){
		jQuery('body').off('click', '.messagebox_button_done');
	    var val = parseInt(0+jQuery.trim(data));
		if ( (val >= minimum_deposit) && (val <= maximum_deposit)) 
		{
			url = url + val;			
			deposit_popup.location = url;
			var timer = setInterval(function(){
				if (deposit_popup.closed) {
//console.log('Deposit window closed');
//here we do update everything   
					clearInterval(timer);
				}		
			}, 100);

		} 
		else 
		{
			deposit_popup.close();
			var s = '<b>'+jQuery.trim(data)+'</b> ' + poker_text_incorrect_amount + '.<br>';
			s += poker_text_must_be_between + ' <b>' + minimum_deposit + '</b> ' + poker_text_and + '  <b>' + maximum_deposit + '</b>';
			jQuery.MessageBox(s);
		}
	})
	.fail(function(){
		jQuery('body').off('click', '.messagebox_button_done');
	});
}//trof_deposit


window.addEventListener('message', function(event) {
    if( event.data === 'vp_deposit'){
//console.log('Deposit completed');
		trof_wins_after_bonus = 9999;
		vp_trof_update();
		jQuery("#vp_balance").effect( "highlight", {color:'lightgreen'}, 1000 );
	}
    if( event.data === 'vp_withdraw'){
//console.log('Withdraw completed');
		trof_wins_after_bonus = 0;
		vp_trof_update(); 
		jQuery("#vp_balance").effect( "highlight", {color:'lightblue'}, 500 );
	}
});

//not in use
WP_vp_trof_update = function(){
	if(typeof (sbf_admin) != 'undefined') {//we are in admin
		if(localStorage.getItem("sfbs_cur_tab") != 'videopoker') { //but not active tab
			return; //do nothing
		}
	}
	vp_trof_update();
}

WP_can_show_popups = function() {
	return ((typeof (sbf_admin) == 'undefined') || (localStorage.getItem("sfbs_cur_tab") == 'videopoker'));
}


vp_trof_update = function(){
	form = document.getElementById('vp_form');
	var form_deal = document.getElementById('vp_deal');
	var form_money = document.getElementById('vp_balance');
	var form_bet = document.getElementById('vp_bet');
	var form_info = document.getElementById('vp_info');
	var trof_n = [];
	form_deal.disabled = true; 
//console.log('--about to update');
	jQuery.post(poker_main_url+"a_poker.php?action=update", {async:true},function(data, status){
var d = new Date();var n = d.toLocaleTimeString();
//console.error(n + ' '+data);
        trof_n = data.split(',');
		var was_winnings = winnings;		
		winnings = parseInt(trof_n[5]);
		form_money.value = winnings;
		bet = parseInt(trof_n[6]);
		form_bet.value = bet;
		trof_wins_after_bonus = parseInt(trof_n[7]);
		if(trof_n[8] != '' && WP_can_show_popups() )
		{
			jQuery.MessageBox({message:trof_n[8],queue:true}
			).done(function(data){
				if(winnings > was_winnings)
				{
					jQuery("#vp_balance").effect( "highlight", {color:'lightgreen'}, 1000 );
				}
			}
			).fail(function(xhr, status, error) {
				console.error(xhr);
				console.error(status + ', ' + error);
				form_deal.disabled = false; 
			});
			
		}
		form_deal.disabled = false; 
//console.log('--out of update');
	});
}//trof_update

setInterval(function(){
	vp_trof_update();
},100000);

vp_trof_set_bg = function(){
	for(var i = 0; i < 5; i++){
		document.getElementById("vp_c"+i).src = poker_main_url + "img/b"+rand_bg_id+".gif";
	}
//	document.querySelector(".maintable_wrapper .maintable").style.backgroundImage= 'url('+poker_main_url + 'img/bg2.jpg)';
}

var we_are_ok = true;
window.onerror1 = function(msg, url, line, col, error){ 
	console.error(url);
	console.error(msg);
	console.error(line + ', ' + col + ', '+error);
	if(we_are_ok)
	{
		we_are_ok = false;
		var s = poker_text_something_went_wrong ; 
	    jQuery.MessageBox(s
		).done(function(data){
			window.location.reload();
		});
	}
}

window.onbeforeunload = function(){ 
	if( ( (typeof balance_page_leave_confirm !== 'undefined') && (winnings >= balance_page_leave_confirm)) 
	     && (we_are_ok) && (typeof no_exit_popup !== 'undefined') )
	{
		var s = poker_text_you_still_have  + ' '+winnings+' '+ poker_text_points + '.<br>'; 
	    jQuery.MessageBox(s);
		return s;
	}
	return undefined;
};


abcheck = function() {
	var t = document.getElementById("tester");
	if( (!t) && (stop_if_adblock != 0) ) {
		jQuery.MessageBox(poker_text_disable_adblock
		).done(function(data){
			window.location.reload();
			return false;
		});
	}
	return true;
}

function setWait(do_wait){
	if(do_wait){
		jQuery(".maintable_wrapper .maintable, .maintable_wrapper .card").addClass('poker_wait');
	} else 	{
		jQuery(".maintable_wrapper .maintable, .maintable_wrapper .card").removeClass('poker_wait');
	}
}

//setTimeout(function(){eval('}');},10000);