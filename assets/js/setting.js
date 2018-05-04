var urlForInject = "https://www.chasedatacorp.com/HttpImport/InjectLead.php";
var urlForUpdate = "https://www.chasedatacorp.com/HttpImport/UpdateLead.php";

$(document).ready(function(){
	var alertStr = ' <div class="alert alert-dismissible fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Warning!</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please insert all required fields.</div>';
	$("input[name='submit']").click(function(){
		// event.preventDefault(); 
		var campaignName = $("#campaign").val();
		var subcampaignName = $("#subcompaign").val();
		var securityCode = $("#securityCode").val();
		var gruopId = $("#gruopId").val();
		var refreshRate = $("#refreshRate").val();

		if (campaignName ==="" || subcampaignName==="" || securityCode === "" || gruopId === "" || refreshRate ===0){
			$("#alert").append(alertStr);
		}else{
			$("#alert").html('');
			



			$(".loader").removeClass("hidden");
			$(this).prop("disabled", true);
			$.ajax({
				url     : "./app/api.php?get=/services/data/v42.0/sobjects/User/" + $('#ownerId').val(),
				success : function(res){
					console.log( JSON.parse(res));
					var response = JSON.parse(res);

					$(".loader").addClass("hidden");
					$("button[name='submit']").prop("disabled", false);
				    
				    if ( typeof response[0]!== "undefined" && response[0]){
				     	if (response[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
				    }

					var firstName = response['FirstName'];
					var lastName  = response['LastName'];
					var address   = response['Address']['street'] + ", " + response['Address']['state'] + ", " + response['Address']['country'];
					var city      = response['City'];
					var state     = response['State'];
					var zipcode   = response['Address']['postalCode'];
					var notes     = response['StayInTouchNote'];
					var mobile	  = response['MobilePhone'];
					var phone	  = response['Phone'];
				},
				error : function(err){

				}
			});
			
		}
	});
});