jQuery(document).ready(function($){
	jQuery(".ced_product_show").hide();
	jQuery(".ced_product_show_click").on("click",function()
	{
		jQuery(this).parent("p").next(".ced_product_show").slideToggle()
	});
	jQuery("#ced_sadc_product_categories").select2({
		placeholder: 'select options'
	});
	jQuery("#ced_sadc_day_selection").select2({
		placeholder: 'select days'
	});

	
	
	jQuery(".ced_sadc_settings_form").submit(function(e){
        
        var ced_sadc_product_categories_val = jQuery("#ced_sadc_product_categories").val();
        
		if (ced_sadc_product_categories_val != null && typeof ced_sadc_product_categories_val !== 'undefined'  ) {
		return;
		}
        
        e.preventDefault();
        var body = jQuery("html, body");
        body.stop().animate({scrollTop:0}, '500', 'swing', function() { 
        	jQuery(".ced_sadc_product_categories_error").show();
        });
   
    });

    jQuery('#ced_sadc_send_email').click(function(e) {
			e.preventDefault();
			jQuery(".ced_sadc_email_image p").removeClass("ced_sadc_email_image_error");
			jQuery(".ced_sadc_email_image p").removeClass("ced_sadc_email_image_success");

			jQuery(".ced_sadc_email_image p").html("");
			var email = jQuery('.ced_sadc_email_field').val();
			jQuery("#ced_sadc_loader").removeClass("hide");
			jQuery("#ced_sadc_loader").addClass("dislay");
			//alert(ajax_url);
			$.ajax({
		        type:'POST',
		        url :ajax_url,
		        data:{action:'ced_sadc_send_mail',flag:true,emailid:email},
		        success:function(data)
		        {
					var new_data = JSON.parse(data);
					jQuery("#ced_sadc_loader").removeClass("dislay");
					jQuery("#ced_sadc_loader").addClass("hide");
					if(new_data['status']==true)
			        {
						jQuery(".ced_sadc_email_image p").addClass("ced_sadc_email_image_success");
						jQuery(".ced_sadc_email_image p").html(new_data['msg']);
						jQuery('.ced_sadc_email_field').val("");
			        }
			        else
			        {
			        	jQuery(".ced_sadc_email_image p").addClass("ced_sadc_email_image_error");
						jQuery(".ced_sadc_email_image p").html(new_data['msg']);
			        }
		        }
	    	});
		});

});