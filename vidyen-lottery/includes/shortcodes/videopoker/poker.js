

var trof_wins_after_bonus = 0; 	//

var newdeal    = 1;                                    //  1 if ready for new deal, 0 if ready to trade, 2 if out of money
var bet        = 5;                                    //  Stores the amount of next bet
var winnings   = 0;                                    //  Stores cumulative winnings
var previous_winnings = 0;                              // Because cumulative winnings is always a gods damn bad idea.
var to_add_winnings = 0;                                //what we will feed through ajax
var back       = new Image(73,97);                     //  Image for back of card
var imgoffset     = 0;                                 //  First card should be (imgoffset + 1)th image on the HTML page

var picked     = new Array(52);                        //  To tell which cards already picked when dealing, 0 if unpicked
var flipped    = new Array(5);                         //  To tell which cards are flipped when trading, 0 if unflipped
var cards      = new Array(5);                         //  Stores images for dealt cards
var cardvals   = new Array(5);                         //  Stores values of dealt cards
var cardimg    = new Array(52);                        //  Stores images of entire deck


for ( var i = 0; i < 5; i++ ) {
  cards[i]   = new Image(73,97);                       //  Create array of five card images
  flipped[i] = 0;                                      //  Set flipped flags to unflipped
}

for ( var i = 0; i < 52; i++ ) {
  cardimg[i]     = new Image(73,97);                   //  Create image array for whole deck
  cardimg[i].src = poker_main_url + "img/" + (i+1) + ".gif";   //  Precache all the card images
  picked[i]      = 0;                                  //  Set picked flags to unpicked
}

back.src = poker_main_url + "img/b"+rand_bg_id+".gif";                        //  Store image for back of cards


jQuery(document).ready(function(){
	setTimeout(function(){
		if(jQuery("#vp_balance").val() == 0){
			animate_deposit();
		}
	},1000);
});

animate_deposit = function()
{
	jQuery("#vp_balance").effect( "highlight", {color:'red'}, 800,function(){
		jQuery("#cm_deposit").effect("shake", { direction: 'up',times:3,distance:5 },1000 ).effect( "highlight", {color:'lightgreen'}, 1000 );
	});
}

//  function dealcards() - randomly deals five cards or trade selected cards, called by clicking button

function dealcards(form)
{
	var form_deal = document.getElementById('vp_deal');
	var form_money = document.getElementById('vp_balance');
	var form_bet = document.getElementById('vp_bet');
	var form_info = document.getElementById('vp_info');
  if ( !newdeal )
  {                                                 //  Player has flipped his unwanted cards
    newdeal = 1;                                                    //  Switch newdeal flag
    form_deal.value = poker_text_poker_deal;                              //  Change description on button
    form_bet.disabled = false;                                      //  Make 'bet' text box read only
  }
  else
  {
  	var b = parseInt(0 + form_bet.value);
      if ( (b < 1) || (b > maximum_bet) ) {                         //  Check that 'bet' text box contains valid integer
      //     alert("You must enter an integer in the 'bet' field!");       //  Alert if it doesn't...
  	  jQuery.MessageBox(poker_text_must_bet_value + " <b>1</b> "+poker_text_and+" <b>" + maximum_bet + "</b>"
  	  ).done(function(data){
  	    jQuery("#vp_bet").effect( "highlight", {color:'red'}, 800, function(){
  			form_bet.focus();                                             //  ...then return focus to text box...
  			form_bet.select();                                            //  ...then highlight the invalid text...
  			if(b < 1) form_bet.value = 1;
  			if(b > maximum_bet) form_bet.value = maximum_bet;
  		})
  	  });
  	  return;                                                       //  ...then go back for another try
      }

  	if(parseInt(form_money.value) == 0 )
  	{
  		jQuery("#vp_balance").effect( "highlight", {color:'red'}, 800,function(){
  			jQuery.MessageBox(poker_text_you_have_zero + "!"+"<br>"+poker_text_deposit_some
  			).done(function(data){
  				animate_deposit();
  			});
  		});
  		return;
  	}

    if ( parseInt(form_bet.value) > parseInt(form_money.value) ) {  //  Check we aren't betting more than we have
	  jQuery.MessageBox(poker_text_not_that_much+"!<br>"+poker_text_bet_to_1
	  ).done(function(data){
		jQuery("#vp_bet").effect( "highlight", {color:'red'}, 800,function(){
			form_bet.focus();                                             //  ...then return focus to text box...
			form_bet.select();                                            //  ...then highlight the invalid text...
			form_bet.value = 1;
		});
	  });
      return;                                                       //  ...then go back for another try
    }

    newdeal = 0;                                                    //  This is a new hand, so do some stuff

    for ( var i = 0; i < 5; i++ ) {
      flipped[i] = 0;                                               //  Reset flipped flags for all five cards
    }
    for ( var i = 0; i < 52; i++ ) {
      picked[i] = 0;                                                //  Reset picked status of entire deck
    }

    form_info.value   = poker_text_click_cards_to_reade;        //  Update info box
    bet               = form_bet.value;                             //  Get amount to bet...
    winnings         -= bet;                                        //  ...deduct it from winnings...
    previous_winnings = winnings;                                   //Because we have to know how much we had before to add
    form_money.value  = winnings;                                   //  ...and update winnings
    form_deal.value   = poker_text_poker_trade;                          //  Change description on button
    form_bet.disabled = true;                                       //  Make 'bet' text box editable again

    //Deduct
    jQuery(document).ready(function($) {
     var data = {
       'action': 'vyps_run_deduct_action',
       'bet_amount': bet,
       'pointid': '3',
     };
     // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
     jQuery.post(ajaxurl, data, function(response) {
       output_response = JSON.parse(response);
     });
    });
  }

  form_deal.disabled = true;
  var trof_n = [];
  var e_params = '';

  if(!newdeal)
  {
	e_params = "bet="+bet;
  }
  else
  {
	var pat = '';
    for ( var i = 0; i < 5; i++ ) {
	if ( !newdeal || flipped[i] )
		pat += '1';
	else
		pat += '0';
	}
	e_params = "pat="+pat;
  }

  setWait(true);
  jQuery.post(poker_main_url+"a_poker.php?action=deal&"+e_params, {async:true},function(data, status){
	setWait(false);
console.log(data);
    trof_n = data.split(',');

	winnings = parseInt(trof_n[5]);
	if(winnings > 0)
	{
		form_money.value = winnings;
		bet = parseInt(trof_n[6]);
		form_bet.value = bet;
	}


	for ( var i = 0; i < 5; i++ ) {                                   //  Deal five random cards
		if ( !newdeal || flipped[i] ) {                                 //  If not a new hand, replace only the flipped cards
			var n = parseInt(trof_n[i]);                     //  Pick random card
		picked[n] = 1;                                                //  Got it, so set picked flag
		cards[i].src = cardimg[n].src;                                //  Update card image array
//update screen
		document.querySelector(".maintable_wrapper #vp_c"+i).src = cardimg[n].src;               //  Update on screen
//		document.images[i+imgoffset].src =  cardimg[n].src;               //  Update on screen

/*
		jQuery('#vp_c'+i).effect( "size", {to: { width: 1, height: 97 }}, 1, function(){
//alert(i)
			document.querySelector(".maintable_wrapper #vp_c"+i).src.src = cardimg[n].src;
			jQuery('#vp_c'+i).effect( "size", {to: { width: 73, height: 97 }}, 1);
		});
*/

		cardvals[i] = n;                                              //  Store value of card
		}
	}

	if ( newdeal )                                                    //  We've replaced the flipped cards...
		checkwin(form);                                                 //  So check if we've won

	form_deal.disabled = false;

	});
}


//  function flipcard() - flips a clicked card, called by clicking a card

function flipcard(i) {
  if ( newdeal )                              //  If it's the end of a hand, we can't flip, so just return
  {
	jQuery("#vp_deal").effect('pulsate').effect( "highlight", {color:'lightgreen'}, 800 );
    return;
  }

	setWait(true);
  if ( !flipped[i] ) {                        //  Either flip card over...
//    document.images[i+imgoffset].src = back.src;
	document.querySelector(".maintable_wrapper #vp_c"+i).src = back.src;
    flipped[i] = 1;
  }
  else {                                      //  ...or flip it back
//    document.images[i+imgoffset].src = cards[i].src;
	document.querySelector(".maintable_wrapper #vp_c"+i).src = cards[i].src;
    flipped[i] = 0;
  }
  setWait(false);
}



function checkwin(form) {
	if(!abcheck())
	{
		return;
	}
	var form_deal = document.getElementById('vp_deal');
	var form_money = document.getElementById('vp_balance');
	var form_bet = document.getElementById('vp_bet');
	var form_info = document.getElementById('vp_info');
	var won = 0;                                //  1 if there's a winning hand, 0 if not
	setWait(true);
	jQuery.post(poker_main_url+"a_poker.php?action=trade", {async:true},function(data, status){
console.info(data);
		setWait(false);
		trof_n = data.split(',');
		won = parseInt(trof_n[0]);
		winnings = parseInt(trof_n[1]);
		trof_wins_after_bonus = parseInt(trof_n[2]);
		form_info.value = trof_n[3];
		form_money.value = winnings;

  //  Check to see if we're out of money

		if ( !winnings ) {
			newdeal = 2;
			jQuery.MessageBox(poker_text_no_points_reset
			).done(function(data){
				window.location.reload();
				return;
			});

		}
		if ( won > 0 )
		{
			jQuery("#vp_balance").effect( "highlight", {color:'lightgreen'}, 1000 );

      //In theory if this only fires if they won so should be positive
      to_add_winnings = winnings - previous_winnings;


        //Add winnings if there are any
        jQuery(document).ready(function($) {
         var data = {
           'action': 'vyps_run_add_action',
           'win_amount': to_add_winnings,
           'pointid': '3',
         };
         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
         jQuery.post(ajaxurl, data, function(response) {
           output_response = JSON.parse(response);
         });
        });


		}
	}); //ajax
}//checkwin2
