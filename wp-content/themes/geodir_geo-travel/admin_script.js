jQuery(document).ready(function($){
	
	var enforeMutualExcludedCheckBox = function(group){
	    return function() {
	      var isChecked= $(this).prop("checked");
	      $(group).prop("checked", false);
	      $(this).prop("checked", isChecked);
	    }
	};
	
	$(".travel_package_promotion_checkbox").click(enforeMutualExcludedCheckBox(".travel_package_promotion_checkbox"));
	
});