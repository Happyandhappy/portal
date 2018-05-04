	var listData = null;
	var current_view = null;
	
	$(document).ready(function(){
			var refreshRate = $("#refreshRate").val();
		    // Show Spinner after loading
			$(".loader").removeClass("hidden");
			$("#views").prop("disabled", true);

			$.ajax({
				url     : "./app/api.php?get=/services/data/v42.0/sobjects/Lead/listviews",
				success : function(res){
					  	 // Hide Spinner after loading
		     			 $(".loader").addClass("hidden");
		     			 $("#views").prop("disabled", false);
		     		     listData = JSON.parse(res);
		     			 

					     // check if session expired or not
					     if ( typeof listData[0]!== "undefined" && listData[0]){
					     	if (listData[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
					     }
					     console.log(refreshRate*60*1000);
					     showSelect(listData);
					     current_view = $("#views").val();
					     request(current_view,2);
					     setInterval( function(){request(current_view,2);}, refreshRate*60*1000);
				},
				error   : function(err){}
			});

			/* Change of view selector */
			$("#views").change(function(){
				current_view = $(this).val();
				$("#table").html('');
				request(current_view,2);
				setInterval( function(){request(current_view,2);},refreshRate*60*1000);
			});
	});
	
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
		     console.log(listData);

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
				     str = str + "<input type='checkbox' checked disabled>" + "</td></tr>";
			else 
				     str = str + "<input type='checkbox' disabled>" + "</td></tr>";	
			ownerId = data['records'][i]['columns'][11]['value'];
		}
		str = str + "</tbody>";
		str = str.split("null").join("");
		$("#table").append(str);
		console.log("ownerId" + ownerId);

		var url = "&ownerId=" + ownerId;
		request(url,3);	
	}


	