var urlForInject = "https://www.chasedatacorp.com/HttpImport/InjectLead.php";
var urlForUpdate = "https://www.chasedatacorp.com/HttpImport/UpdateLead.php";

$(document).ready(function(){

	$(".loader").removeClass("hidden");
	$("input[name='submit']").prop("disabled", true);
	$.ajax({
		url : "./app/api.php?get=/services/data/v42.0/sobjects/User/" + $('#ownerId').val(),
		success : function(res){
					var response = JSON.parse(res);
					console.log(response);
					$(".loader").addClass("hidden");
					$("input[name='submit']").prop("disabled", false);

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

					$("#form").append("<input type='hidden' name='firstName' value='" + firstName + "'>");
					$("#form").append("<input type='hidden' name='lastName' value='" + lastName + "'>");
					$("#form").append("<input type='hidden' name='address' value='" + address + "'>");
					$("#form").append("<input type='hidden' name='city' value='" + city + "'>");
					$("#form").append("<input type='hidden' name='state' value='" + state + "'>");
					$("#form").append("<input type='hidden' name='zipcode' value='" + zipcode + "'>");
					$("#form").append("<input type='hidden' name='notes' value='" + notes + "'>");
					$("#form").append("<input type='hidden' name='mobile' value='" + mobile + "'>");
					$("#form").append("<input type='hidden' name='phone' value='" + phone + "'>");

		},
		error : function(error){
			window.location.replace("./app/api.php?logout="); 
		}
	});
});