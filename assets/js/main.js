	var listData = null;
	var current_view = null;
	
	$(document).ready(function(){
			var refreshRate = $("#refreshRate").val();
			current_view = $("#views").val();

			console.log($('#option').val()==='1');
			if(refreshRate >0){
				getmappingData();
				setInterval( function(){getmappingData();}, refreshRate*60*1000);
			}

			/* Change of view selector */
			$("#views").change(function(){
				current_view = $(this).val();
				$("#table").html('');
				$(".form").submit();
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
	
	function getmappingData(){
		var url = current_view ; 
		$(".loader").removeClass("hidden");
		$("#views").prop("disabled", true);

		$.ajax({
			url : "./app/api.php?get=" + url,
			success : function(res){
			    $(".loader").addClass("hidden");
		     	listData = JSON.parse(res)['records'];
				
				var name 	= $('#name').val();
				var company = $('#company').val();
				var email 	= $('#email').val();
				var lead_status = $('#lead_status').val();
				var result  = [];
				for ( var i = 0 ; i < listData.length ; i++){
					if (listData[i]['columns'][0]['value'] === name && listData[i]['columns'][1]['value']===company && listData[i]['columns'][3]['value'] === email && listData[i]['columns'][4]['value'] === lead_status)
						result.push(listData[i]);						
				}
				console.log(result);
				showTable(result);
				$("#views").prop("disabled", false);
			},
			error : function(err){
				console.log(err);
			}
		});
	}

	function showTable(data){
		var i;
		$("#table").html('');
	    $("#table").append("<thead><tr> <th>_No</th> <th>NAME</th> <th>COMPANY</th> <th>STATE/PROVINCE</th> <th>EMAIL</th>  <th>LEAD STATUS</th> <th>CREATED DATE</th> <th>OWNER ALIAS</th> <th>UNREAD BY OWNER</th> </tr></thead>");
		
		var str = "<tbody>";		
		var ownerId = "";		
		var data;
		for ( i = 0 ; i < data.length ; i++){
			str = str + "<tr><td>" + (i + 1) + "</td><td>" + data[i]['columns'][0]['value'] + "</td><td>"
				      + data[i]['columns'][1]['value'] + "</td><td>"
				      + data[i]['columns'][2]['value'] + "</td><td>"
				      + data[i]['columns'][3]['value'] + "</td><td>"
				      + data[i]['columns'][4]['value'] + "</td><td>"
				      + data[i]['columns'][5]['value'] + "</td><td>"
				      + data[i]['columns'][6]['value'] + "</td><td>";
			if (data[i]['columns'][7]['value']==='true')	
			 	 str = str + "<input type='checkbox' id='test" + i + "' checked disabled/><label for='test" + i + "'></label>";
			else 
			     str = str + "<input type='checkbox' id='test" + i + "' disabled/><label for='test" + i + "'></label>";
			if ($('option')==='1')send(data[i]['columns'][8]['value']);
		}

		str = str + "</tbody>";
		str = str.split("null").join("");
		$("#table").append(str);
	}

	function send(url){
		url = "./app/api.php?get=/services/data/v42.0/sobjects/Lead/" + url;
		var data;
		$.ajax({
			url : url,
			success : function(res){
				var listData = JSON.parse(res);
				if ( typeof listData[0]!== "undefined" && listData[0]){
			     	if (listData[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
			    }

				console.log(listData);
				var campaign 	= $('#campaign').val();
				var subcampaign = $('#subcampaign').val();
				var securityCode = $('#securityCode').val();
				var groupId 	= $("#groupId").val();
				var firstName 	= listData['FirstName'];
				var lastName  	= listData['LastName'];
				var	address   	= listData['Street'] + " " + listData['City'] + ", " + listData['State'] + " " + listData['PostalCode'] + " " + listData['Country'];
				var city 	  	= listData['City'];
				var state 		= listData['State'];
				var zipcode 	= listData['PostalCode'];
				var notes 		= listData['Notes__c'];
				var phone 		= listData['Phone'];
				var mobile 		= listData['MobilePhone'];

				url1 =  "https://www.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" + campaign + "&Subcampaign=" + subcampaign +
						"&GroupId=" + groupId + "&SecurityCode=" + securityCode + "&FirstName=" + firstName + "&LastName=" + lastName +
						"&ClientId=1&Address=" + address + "&City=" + city + "&State=" + state + "&ZipCode=" + zipcode + "&Notes=" + notes +
						"&PrimaryPhone=" + phone + "&adv_MobilePhone=" + mobile + "&DuplicatesCheck=2";
						console.log("--------------- Inject ----------------");
						console.log(url1);
				$.ajax({
					url : url1,
					success : function(res){
						console.log(res);
						if (res!="<br><br>Result: OK"){
							url2 = url = "https://www.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=" + groupId + "&SecurityCode=" + securityCode + 
								  "&SearchField=Phone&Identifier=" + phone + "&FirstName=" + firstName +
								  "&LastName=" + firstName + "&adv_MobilePhone=" + mobile + 
								  "&Address="  + address   + "&State=" + state + 
								  "&ZipCode="  + zipcode   + "&Notes=" + notes;
							console.log("--------------- Update ----------------");
							console.log($url2);
							$.ajax({
								url : url2,
								success : function(res){
									console.log(res);
								}
							});
						}
					},	
					error: function(err){
						console.log(err);
					}
				});
						  
			},
			error : function(err){}
		});
	}
