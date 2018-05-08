	var listData = null;
	var current_view = null;
	
	$(document).ready(function(){
			var refreshRate = $("#refreshRate").val();
			current_view = $("#views").val();

			console.log($('#option').val()==='1');
			if(refreshRate >0)setInterval( function(){
				request(current_view,2); 
				if($('#option').val()==='1') send_ourend();
			}, refreshRate*60*1000);

			/* Change of view selector */
			$("#views").change(function(){
				current_view = $(this).val();
				$("#table").html('');
				$(".form").submit();
				// request(current_view,2);
				// setInterval( function(){request(current_view,2);},refreshRate*60*1000);
			});

			$('#option').change(function(){
				var url = "./app/api.php?option=" + $('#option').val() + "&view=" + $('#views').val();
				$.ajax({
					url : url,
					success : function(res){
						
					},
					error : function(err){
						
					}
				});
			});
	});
	
	function send_ourend(){
		var campaign = $('#campaign').val();
		var subcampaign = $('#subcampaign').val();
		var securityCode = $('#securityCode').val();
		var groupId = $("#groupId").val();
		var firstName = $('#firstName').val();
		var lastName = $('#lastName').val();
		var address = $('#address').val();
		var city = $('#city').val();
		var state = $('#state').val();
		var zipcode = $('#zipcode').val();
		var notes = $('#notes').val();
		var phone = $('#phone').val();
		var mobile = $('#mobile').val();

		var url = "https://www.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" + campaign + "&Subcampaign=" + subcampaign +
					"&GroupId=" + groupId + "&SecurityCode=" + securityCode + "&FirstName=" + firstName + "&LastName=" + lastName +
					"&ClientId=1&Address=" + address + "&City=" + city + "&State=" + state + "&ZipCode=" + zipcode + "&Notes=" + notes +
					"&PrimaryPhone=" + phone + "&adv_MobilePhone=" + mobile + "&DuplicatesCheck=2";
		console.log(url);

		$.ajax({
			url : url,
			success : function(res){
				res = res.replace("<br>","");
				res = res.replace("\n", "");
				console.log(res);
				if (res != "Result: OK") {
					url = "https://www.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=" + groupId + "&SecurityCode=" + securityCode + 
						  "&SearchField=Phone&Identifier=" + phone + "&FirstName=" + firstName +
						  "&LastName=" + firstName + "&adv_MobilePhone=" + mobile + 
						  "&Address="  + address   + "&State=" + state + 
						  "&ZipCode="  + zipcode   + "&Notes=" + notes;
					send_update(url);
				}
			},
			error : function(err){

			}
		});

	}

	function send_update(url){
		$.ajax({
			 url : url,
			 success : function(res){
			 	console.log(res);
			 },
			 error: function(err){

			 }
		})
	}

	function request(url, view){
	    // Show Spinner after loading
		$(".loader").removeClass("hidden");
		$("#views").prop("disabled", true);
		$.ajax({
		  url: "./app/api.php?get=" + url,
		  success: function(res){
		  	 // Hide Spinner after loading
		     $(".loader").addClass("hidden");

		     listData = JSON.parse(res);
		     // console.log(listData);

		     // check if session expired or not
		     if ( typeof listData[0]!== "undefined" && listData[0]){
		     	if (listData[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
		     }

 			 if (view===1) showSelect(listData);
			 else if (view===2) showTable(listData);

		     $("#views").prop("disabled", false);

		  },
		  error: function(err){
		  	return err;
		  }
		});
	}

	function showSelect(data){
		for (var i = 0 ; i < data['listviews'].length ; i++){
			$('#views').append("<option value = '" + data['listviews'][i]['resultsUrl'] + "'>" + data['listviews'][i]['label'] + "</option>")
		}
	}

	function showTable(data){
		var i;
		$("#table").html('');
	    $("#table").append("<thead><tr> <th>_No</th> <th>NAME</th> <th>COMPANY</th> <th>STATE/PROVINCE</th> <th>EMAIL</th>  <th>LEAD STATUS</th> <th>CREATED DATE</th> <th>OWNER ALIAS</th> <th>UNREAD BY OWNER</th> </tr></thead>");
		
		var str = "<tbody>";		
		var ownerId = "";		
		for ( i = 0 ; i < data['records'].length ; i++){
			str = str + "<tr><td>" + (i + 1) + "</td><td>" + data['records'][i]['columns'][0]['value'] + "</td><td>"
				      + data['records'][i]['columns'][1]['value'] + "</td><td>"
				      + data['records'][i]['columns'][2]['value'] + "</td><td>"
				      + data['records'][i]['columns'][3]['value'] + "</td><td>"
				      + data['records'][i]['columns'][4]['value'] + "</td><td>"
				      + data['records'][i]['columns'][5]['value'] + "</td><td>"
				      + data['records'][i]['columns'][6]['value'] + "</td><td>";
			if (data['records'][i]['columns'][7]['value']==='true')	
				     // str = str + "<input type='checkbox' checked disabled>" + "</td></tr>";
				 	 str = str + "<input type='checkbox' id='test" + i + "' checked disabled/><label for='test" + i + "'></label>";
			else 
				     // str = str + "<input type='checkbox' disabled>" + "</td></tr>";	
				 str = str + "<input type='checkbox' id='test" + i + "' disabled/><label for='test" + i + "'></label>";
			ownerId = data['records'][i]['columns'][11]['value'];
		}
		str = str + "</tbody>";
		str = str.split("null").join("");
		$("#table").append(str);
		// console.log("ownerId" + ownerId);

		// var url = "&ownerId=" + ownerId;
		// request(url,3);	
	}


	