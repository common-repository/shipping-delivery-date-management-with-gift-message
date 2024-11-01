jQuery(document).ready(function()
{
	jQuery(".ced_product_show").hide();
	// jQuery(".ced_product_show_click").on("click",function()
	// {
	// 	jQuery(this).parent("p").next(".ced_product_show").slideToggle()
	// })

	jQuery(document).on('click touchstart','.ced_product_show_click',function(){
		jQuery(this).parent("p").next(".ced_product_show").slideToggle()
		
	})
});