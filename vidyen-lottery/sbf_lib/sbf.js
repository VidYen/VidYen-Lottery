/* Simple Bitcoin Faucets 
Relies on jQuery !
*/

jQuery(document).ready(function() {


//	jQuery(".sbf_edit").live('change keyup paste', function (event) {
	jQuery(document).on('change keyup paste', '.sbf_edit', function (event) {
        if(event.handled !== true){ // This will prevent event triggering more then once
            event.handled = true;
        }else{
			return;
		}
		var s = jQuery(this).val()
		var n = s.replace(/[^0-9]/g,'');
		if(s != n)
		{
			jQuery(this).val(n);
		}
		if(event.type == 'change')
		{
			if(n == ''){
				jQuery(this).val('0');
			}
			sbf_sf_copy_to_options(this);
		}
	});

//	jQuery(".sbf_delete_pair").live('click',function(e){ 
	jQuery(document).on('click', '.sbf_delete_pair', function (event) {
        if(event.handled !== true){ // This will prevent event triggering more then once
            event.handled = true;
        }else{
			return;
		}
		if(confirm(sfbg_main_confirm + '?') )
		{
			var this_not_removed = jQuery(this).parents('.sbfg_sf_current').children('.sbf_add_pair');//gotta exist
			this_not_removed.prop( "disabled", false );
			jQuery(this).parents('.sbf_edit_pair').remove();
			sbf_sf_copy_to_options(this_not_removed);// can not use 'this' here - not going to exist
//console.log(jQuery(this_not_removed).parents('.sbfg_sf_current').children('.sbf_edit_pair').length) //no 'this' also
			if(jQuery(this_not_removed).parents('.sbfg_sf_current').children('.sbf_edit_pair').length == 1)
			{
				this_not_removed.parents('.sbfg_sf_current').children('.sbf_delete_pair').prop( "disabled", true );
			}
		}
	});//sbf_delete_pair

//	jQuery(".sbf_add_pair").live('click',function(e){
	jQuery(document).on('click', 'button.sbf_add_pair', function (event) {
        if(event.handled !== true){ // This will prevent event triggering more then once
            event.handled = true;
        }else{
			return;
		}
		jQuery(this).prev().clone().insertBefore(this);
		jQuery(this).prev().find('.sbf_edit_score').val(parseInt(jQuery(this).prev().find('.sbf_edit_score').val()) + 1);
		jQuery(this).parents('.sbfg_sf_current').children('.sbf_delete_pair').prop( "disabled", false );
//console.log(jQuery(this).parents('.sbfg_sf_current').children('.sbf_edit_pair').length)
		if(jQuery(this).parents('.sbfg_sf_current').children('.sbf_edit_pair').length == 10)
		{
			jQuery(this).prop( "disabled", true );
		}
		sbf_sf_copy_to_options(this);
	});//sbf_add_pair
	


//	jQuery("#sbf_admin_form").live("submit", function(e){
	jQuery(document).on('submit', '#sbf_admin_form', function (e) {
//e.preventDefault();
//console.log('submit #sbf_admin_form',e);
	});//#sbf_admin_form.submit
	
})//document.ready



sbf_sf_make_line = function(sf_score,sf_faucet,i,pref,allow_edit,allow_delete){
	var e_readonly = ' READONLY ';
	if(allow_edit)
	{
		e_readonly = '';
	}
	var e_score = "<input "+e_readonly+" class='sbf_edit sbf_edit_score sbf_edit_score_"+pref+"' value='"+sf_score+"'></input>";
//console.log(e_score);
	var e_faucet = "<input  class='sbf_edit sbf_edit_faucet sbf_edit_faucet_"+pref+"' value='"+sf_faucet+"'></input>";
//console.log(e_faucet);	
	var e_delete = "<button  type=button class='sbf_delete_pair sbf_button' title='"+sfbg_main_delete+"'>&ndash;</button>";
//console.log(e_delete);
	if(!allow_delete)
	{
		e_delete = '';
	}
	var e_line = "<div class='sbf_edit_pair sbf_edit_pair_"+pref+"'>"+e_delete+e_score+e_faucet+"</div>";
	return(e_line);
}

sbf_sf_copy_to_options = function(changed_object){
//console.log(changed_object);
	var parent_of_lines = jQuery(changed_object).parents('.sbfg_sf_current');
//console.log(parent_of_lines);
	var sfb_game = parent_of_lines.attr('sbf_game');
//console.log("sbf_game",sfb_game);
	var a_pairs = [];
	parent_of_lines.children().each(function(){
//console.log(this);	
		var v_score = jQuery(this).children('.sbf_edit_score').val();
		var v_faucet = jQuery(this).children('.sbf_edit_faucet').val();
		if(v_score && v_faucet)	{
			var sf_line = v_score.trim() + ':' + v_faucet.trim();
			a_pairs.push(sf_line);
		}
	});//each
	a_pairs.sort(function(a, b){return parseInt(a) - parseInt(b)});
	//now gotta get rid of same score
//	a_pairs = a_pairs.reduce(function(a,b){console.log(a);if(a.indexOf(b)<0)a.push(b);return a;},[]);
//console.log(a_pairs);
	var s_pairs = a_pairs.join(',');
//console.log(s_pairs);
//console.log(jQuery('input[sbf_game="'+sfb_game+'"]'));
	jQuery('input[sbf_game="'+sfb_game+'"]').val(s_pairs);
	jQuery('div[sbf_game_settings="'+sfb_game+'"]').html(s_pairs);
}


sbf_sf_parse_from_options = function(pref,force_default,allow_edit,allow_add_delete) {
//like "blockrain" '#sbfg_sf_h_blockrain','#sbfg_sf_current_blockrain','#sbfg_sf_default_blockrain'

	var sel_from_hidden = '#sbfg_sf_h_' + pref;
	var sel_to_div = '#sbfg_sf_current_' + pref;
	var sel_default_div = '#sbfg_sf_default_' + pref;
	var sf_pairs = jQuery(sel_from_hidden).val();
	if((sf_pairs.trim().length == 0) || force_default)
	{
		sf_pairs = jQuery(sel_default_div).html();
		jQuery(sel_from_hidden).val(sf_pairs);
	}
	jQuery('div[sbf_game_settings="'+pref+'"]').html(sf_pairs);
	a_sf_pairs = sf_pairs.split(',');
//console.log(a_sf_pairs);
	a_sf_pairs.sort(function(a, b){return parseInt(a) - parseInt(b)});
	var s_out = '';
	for(var i = 0; i < a_sf_pairs.length; i++)
	{
		var a_sf_score_faucet = a_sf_pairs[i].split(':');
		var sf_score = a_sf_score_faucet[0].trim();
		var sf_faucet = a_sf_score_faucet[1].trim();
//console.log(sf_score,sf_faucet)
		s_out += sbf_sf_make_line(sf_score,sf_faucet,i,pref,allow_edit,allow_add_delete);
	}
	if(allow_add_delete)
	{
		var e_add = "<button type=button  class='sbf_button sbf_add_pair' title='"+sfbg_main_add+"'>+</button>";
		s_out += e_add;
	}
	jQuery(sel_to_div).html(s_out);
}//sbf_sf_parse_from_options

/*----------------------------------------------------------------------------------*/
sbf_sf_show_reward_confirm = function(score,starter_callback,game_tag)
{
	jQuery.confirm({
		useBootstrap: false,
		columnClass: 'small',
		title: sfbg_main_ready,
		content: sfbg_main_score +' : '+ score,
		buttons: {
				yes: {
					text: sfbg_main_yes,
					btnClass: 'btn-green',
					keys: ['Enter', 'Y'],
					action: function(){
						sbf_sf_get_faucet_by_score(score,starter_callback,game_tag)
					}
				},
				no: {
					text: sfbg_main_no,
					keys: ['Esc', 'N'],
					btnClass: 'btn-red',
					action: function(){
					}
				},
			}
	});
}

sbf_sf_get_faucet_by_score = function(score,starter_callback,game_tag){ //calls starter_callback
//console.log(score,starter_callback,game_tag);
	var res_faucet = 123456;
	var s_score_faucets = jQuery('div[sbf_game_settings="'+game_tag+'"]').html();
	var a_score_faucets = s_score_faucets.split(',');
	for(var i = 0; i < a_score_faucets.length; i++)
	{
		var a_line = a_score_faucets[i].split(':');
		var c_score = a_line[0];
		var c_faucet = a_line[1];
		if(i == 0){
			res_faucet = c_faucet;
		}
		if(score >= c_score){
			res_faucet = c_faucet;
			if(score == c_score) {
				break;
			}
		}
	}
console.log('sbf_sf_get_faucet_by_score score:',score,' faucet:',res_faucet,' calling:',starter_callback);	
	starter_callback(res_faucet);
}//sbf_get_faucet_by_score


scroll_to_hints = function(mark){
	document.getElementById(mark).scrollIntoView();
}
