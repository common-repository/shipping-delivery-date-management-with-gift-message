// jQuery(document).ready(function(){

// jQuery("#ced_sadc_btnshow").click(function () {
// 		jQuery('#ced_sadc_dialog').show();
//     });
// });


var ajaxurl = sads_obj.ajaxurl;
var selected_days = sads_obj.sadc_settings.ced_sadc_supply_order;
var myArray_sadc = {};
for(var i in selected_days)
{
	var id = selected_days[i];
	myArray_sadc[id]= id;
}
console.log(myArray_sadc);

function highlightDays(date) 
{
	var m = date.getMonth();
	var d = date.getDate();
	var y = date.getFullYear();
	if(m<=9)
	{
		 var currentdate = '0'+(m + 1) + '/' + d + '/' + y ;
	}	
	else
	{
		var currentdate = (m + 1) + '/' + d + '/' + y ;
	}		
	
	if(myArray_sadc[0]!=null)
	{
		if(myArray_sadc[0] == 0)
	 	{
			var sunday = date.getDay();
			if (sunday == 0) 
			{
				return [false] ; 
			} 
		 
	 	}
	 }

	if(myArray_sadc[1]!=null)
 	{
		if(myArray_sadc[1] == 1)
	 	{
		
			var monday = date.getDay();
			if (monday == 1) 
			{
				return [false] ; 
			} 
			
	 	}
	 	}
    if(myArray_sadc[2]!=null)
 	{
		if(myArray_sadc[2] == 2)
	 	{
	 		
			var tuesday = date.getDay();
			if (tuesday == 2) 
			{
				return [false] ; 
			} 
			
		}
	 }
	if(myArray_sadc[3]!=null)
 	{
		if(myArray_sadc[3] == 3)
	 	{
			
			var wednesday = date.getDay();
			if (wednesday == 3) 
			{
				return [false] ; 
			} 
			 
	 	}
	 }
	if(myArray_sadc[4]!=null)
 	{
			if(myArray_sadc[4] == 4)
	 	{
			
			var thrusday = date.getDay();
			if (thrusday == 4) 
			{
				return [false] ; 
			} 
			
	 	}
	 }
	if(myArray_sadc[5]!=null)
 	{
		if(myArray_sadc[5] == 5)
		{
			
			var friday = date.getDay();
			if (friday == 5) 
			{
				return [false] ; 
			} 
			
	 	}
	}
	if(myArray_sadc[6]!=null)
 	{
		if(myArray_sadc[6] == 6)
	 	{
		
			var saturday = date.getDay();
			if (saturday == 6) 
			{
				return [false] ; 
			} 
			
	 	}
	}
	if(myArray_sadc[7]!=null)
 	{
		if(myArray_sadc[7] == 7)
	 	{
		
			var all = date.getDay();
			if (saturday == 7) 
			{
				return [true, ''] ; 
			} 
			
	 	}
	}
		return [true, ''];	
}


function ced_sadc_additional_cost(date)
{
	var sadc_settings = sads_obj.sadc_settings;
	var dateformat = sadc_settings.ced_sadc_date_format;
	if(dateformat == "dd/mm/yy"){
		var d=new Date(date.split("/").reverse().join("-"));
	}
	else{
		date = date.replace("-", "/");
		date = date.replace("-", "/");
		var d = new Date(date);
	}
    var n = d.getDay(); 
	if(n == 0)
    {
		if(sads_obj.sadc_settings.ced_sadc_sunday)
	    {
	    	jQuery('.ced_sadc_add_cost').show();
		 	jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_sunday);
		 	jQuery('.ced_sadc_hide_cost_amount').val( sads_obj.sadc_settings.ced_sadc_sunday);
		}
		else
		{
		    jQuery('.ced_sadc_add_cost').hide();
			jQuery('.ced_sadc_hide_cost_amount').val(0);
		}	
	}		
    	   
	if(n == 1)
    {
		if(sads_obj.sadc_settings.ced_sadc_monday)
		{
			 jQuery('.ced_sadc_add_cost').show();
		   	 jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_monday);
			 jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_monday);
		}
		else
		{
		     jQuery('.ced_sadc_add_cost').hide();
			 jQuery('.ced_sadc_hide_cost_amount').val(0);
		}	
	}
	   
	if(n == 2)
	{
		 if(sads_obj.sadc_settings.ced_sadc_tuesday)
		 {
			 jQuery('.ced_sadc_add_cost').show();
		   	 jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_tuesday);
		   	 jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_tuesday);
		 }
		 else
		 {
		     jQuery('.ced_sadc_add_cost').hide();
			 jQuery('.ced_sadc_hide_cost_amount').val(0);
		 }	
	}
	   
    if(n == 3)
	{
    	  if(sads_obj.sadc_settings.ced_sadc_wednesday)
    	  {
    		  jQuery('.ced_sadc_add_cost').show();
    		  jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_wednesday);
    		  jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_wednesday);
		  }
    	  else
		  {
			  jQuery('.ced_sadc_add_cost').hide();
			  jQuery('.ced_sadc_hide_cost_amount').val(0);
		  }	
	}
	   
	if(n == 4)
	{
		  if(sads_obj.sadc_settings.ced_sadc_thrusday)
		  {
			  jQuery('.ced_sadc_add_cost').show();
			  jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_thrusday);
			  jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_thrusday);
		  }
		  else
		  {
			  jQuery('.ced_sadc_add_cost').hide();
			  jQuery('.ced_sadc_hide_cost_amount').val(0);
		  }	
	}
	
    if(n == 5)
	{
    	 if(sads_obj.sadc_settings.ced_sadc_friday)
    	 {
    		 jQuery('.ced_sadc_add_cost').show();
    		 jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_friday);
    		 jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_friday);
		 }
    	 else
		 {
			  jQuery('.ced_sadc_add_cost').hide();
			  jQuery('.ced_sadc_hide_cost_amount').val(0);
		 }	
	}
	   
	if(n == 6)
	{
		  if(sads_obj.sadc_settings.ced_sadc_saturday)
		  {
			  jQuery('.ced_sadc_add_cost').show();
			  jQuery('.ced_sadc_add_cost_amount').html(sads_obj.woo_curncy+sads_obj.sadc_settings.ced_sadc_saturday);
			  jQuery('.ced_sadc_hide_cost_amount').val(sads_obj.sadc_settings.ced_sadc_saturday);
		  }
		  else
		  {
			  jQuery('.ced_sadc_add_cost').hide();
			  jQuery('.ced_sadc_hide_cost_amount').val(0);
		  }	  
	}
}	
	
function ced_sadc_fetch_state(country)
{
	jQuery.post(ajaxurl,{'action':'sadc_fetch_state','country':country},function(response){
		if(response.hasOwnProperty('state'))
    	{
			var html = "<select name='ced_sadc_state_field' id='ced_sadc_state_field'>";	
			for (x in response.state) 
			{
				html += '<option value="'+response['state'][x]+'">'+response['state'][x]+'</response>';
			}	
			html += "</select>";
		}	
		else
		{
			var html = '<input type="text" class="input-text ced_sadc_required" name="ced_sadc_state_field" id="ced_sadc_state_field">';
		}
   		jQuery("#ced_sadc_country_select").html(html);		
	},'json');	
}	
jQuery( '.timepicker' ).timepicker({
		showPeriod: true,
	    showLeadingZero: true
});



jQuery(function () 
{
	jQuery("#ced_sadc_btnshow").click(function () {
		jQuery('#ced_sadc_dialog').show();
    });

	var sadc_settings = sads_obj.sadc_settings;
	var dateformat = sadc_settings.ced_sadc_date_format;
	if(dateformat == "dd/mm/yy"){
		
	}

	if(jQuery('#sddmwgm_datepicker').length){
		var selected_date = jQuery("#sddmwgm_datepicker").val();
		if(selected_date != null || selected_date != '')
		{
			ced_sadc_additional_cost(selected_date);
		}	

		
		jQuery("#sddmwgm_datepicker").datepicker({
	       minDate: 0,
	       dateFormat: dateformat,
	       inline: true,
	       beforeShowDay: highlightDays,
	       onSelect:ced_sadc_additional_cost,
	    });
	}
	jQuery("#ced_sadc_btnshow").click(function () {
		jQuery('#ced_sadc_dialog').show();
    });

	var country = jQuery('#ced_sadc_country_field').val();
	
	
    jQuery("#ced_sadc_country_field").change(function(){
		var country = jQuery(this).val();
    	ced_sadc_fetch_state(country);
	});   

	jQuery("#ced_sadc_save").click(function () {
		var error = false;
		var name_regex = /^[a-zA-Z]+$/;
		var email_regex = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		var phone_regx = /^[0-9-+]+$/;
		var address = {};
		jQuery(".ced_sadc_required").each(function(){
			if(jQuery(this).val() == null || jQuery(this).val() =='')
			{	
				jQuery(this).addClass('error');
				error = true;
			}
			else
			{
				jQuery(this).removeClass('error');
			}							
		});	

		address['firstname'] = jQuery('#ced_sadc_first_name_field').val();
		if (!address['firstname'].match(name_regex))
		{
			error = true;
			jQuery('#ced_sadc_first_name_field').addClass('error');
		}	
		else
		{
			jQuery('#ced_sadc_first_name_field').removeClass('error');
		}	
		
		address['email'] = jQuery('#ced_sadc_email_field').val();
		
		if(!address['email'].match(email_regex))
		{
			error=true;
			jQuery('#ced_sadc_email_field').addClass('error');
		}
		else
		{
			jQuery('#ced_sadc_email_field').removeClass('error');
		}
		
		address['lastname'] = jQuery('#ced_sadc_last_name_field').val();
		
		if (!address['lastname'].match(name_regex))
		{
			error = true;
			jQuery('#ced_sadc_last_name_field').addClass('error');
		}	
		else
		{
			jQuery('#ced_sadc_last_name_field').removeClass('error');
		}		

		address['phone'] = jQuery('#ced_sadc_phone_field').val();
		
		if (!address['phone'].match(phone_regx))
		{
			error = true;
			jQuery('#ced_sadc_phone_field').addClass('error');
		}	
		else
		{
			jQuery('#ced_sadc_phone_field').removeClass('error');
		}	
			
		
		if(error)
		{
			return false;
		}	
		
		address['companyname'] = jQuery('#ced_sadc_company_field').val();
		address['country'] = jQuery('#ced_sadc_country_field').val();
		address['state'] = jQuery('#ced_sadc_state_field').val();
		address['address1'] = jQuery('#ced_sadc_address_1_field').val();
		address['address2'] = jQuery('#ced_sadc_address_2_field').val();
		address['city'] = jQuery('#ced_sadc_city_field').val();
		var string_address = JSON.stringify(address);
		jQuery("#ced_sadc_rec_add").val(string_address);
		jQuery('#ced_sadc_dialog').hide();
		alert(sads_obj.alerttext);
	});	
	jQuery(".close-btn").click(function () {
		jQuery('#ced_sadc_dialog').hide();
	});
});

