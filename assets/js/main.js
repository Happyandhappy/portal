	var listData = null;
	var current_view = null;
	var index = 0;
	var size = 0;
	var refreshRate = 0;
	var data;
	var interval=null;
	var opt = 0;
	$(document).ready(function(){
			refreshRate = $("#refreshRate").val();
			current_view = $("#views").val();
			if ($('#option').val() === 'Start'){
				opt = 0;
			}else{
				opt = 1;
			}
			console.log($('#option').val()==='1');
			if (current_view !="- None -"){
				getmappingData();
			}
			if(refreshRate >0 && current_view !="- None -"){
				if (interval!= null) clearInterval(interval);
				interval = setInterval(function(){
					getmappingData();
				}, refreshRate*60*1000);
			}

			/* Change of view selector */
			$("#views").change(function(){
				current_view = $(this).val();
				$("#table").html('');
				$(".form").submit();
			});

			$('#option').click(function(){
				opt = 0;
				if ($('#option').val() === 'Start'){
					opt = 1;
					$('#option').val('Stop');
					$('#option').removeClass('start');
					$('#option').addClass('stop');
				}
				else {
					opt = 0;
					$('#option').val('Start');
					$('#option').removeClass('stop');
					$('#option').addClass('start');

				}

				var url = "./app/api.php?option=" + opt + "&view=" + $('#views').val();
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
				
				data  = [];
				for ( var i = 0 ; i < listData.length ; i++){
						data.push(listData[i]);						
				}
				console.log(data);
				showTable();
				$("#views").prop("disabled", false);
			},
			error : function(err){
				console.log(err);
			}
		});
	}

	function showTable(){
		size  = data.length;
		if (size === 0) return;
		var i;
		$("#table").html('');
		var str = "<thead><th> _No </th>";
		for ( i = 0 ; i < 8 ; i++){
			str = str + "<th>" + data[0]['columns'][i]['fieldNameOrPath'] + "</th>";
		}
		if (data[0]['columns'][8]['fieldNameOrPath']!='Id')
			str = str + "<th>" + data[0]['columns'][8]['fieldNameOrPath'] + "</th>";	

		str = str + "</thead>";
		$("#table").append(str);
	    // $("#table").append("<thead><tr> <th>_No</th> <th>NAME</th> <th>COMPANY</th> <th>STATE/PROVINCE</th> <th>EMAIL</th>  <th>LEAD STATUS</th> <th>CREATED DATE</th> <th>OWNER ALIAS</th> <th>UNREAD BY OWNER</th> </tr></thead>");
		
		str = "<tbody>";		
		
		for ( i = 0 ; i < data.length ; i++){

			str = str + "<tr><td>" + (i + 1) + "</td><td>" + data[i]['columns'][0]['value'] + "</td><td>"
				      + data[i]['columns'][1]['value'] + "</td><td>"
				      + data[i]['columns'][2]['value'] + "</td><td>"
				      + data[i]['columns'][3]['value'] + "</td><td>"
				      + data[i]['columns'][4]['value'] + "</td><td>"
				      + data[i]['columns'][5]['value'] + "</td><td>"
				      + data[i]['columns'][6]['value'] + "</td>";
			if (data[i]['columns'][8]['fieldNameOrPath']!='Id'){
				str = str + "<td>" + data[i]['columns'][7]['value'] + "</td><td>";	
				if (data[i]['columns'][8]['value']==='true')	
			 	 	str = str + "<input type='checkbox' id='test" + i + "' checked disabled/><label for='test" + i + "'></label></td>";
				else 
			     	str = str + "<input type='checkbox' id='test" + i + "' disabled/><label for='test" + i + "'></label></td>";
			}else{
				if (data[i]['columns'][7]['value']==='true')	
				 	 str = str + "<td><input type='checkbox' id='test" + i + "' checked disabled/><label for='test" + i + "'></label></td>";
				else 
				     str = str + "<td><input type='checkbox' id='test" + i + "' disabled/><label for='test" + i + "'></label></td>";				
			}
		      	 

		}

		str = str + "</tbody>";
		str = str.split("null").join("");
		$("#table").append(str);

		index = 0;
		if (opt===1) send();
	}

	function send(){
		
		if (index > size) return;
		if (data[index]['columns'][8]['fieldNameOrPath'] === "Id"){
				var url = "./app/api.php?get=/services/data/v42.0/sobjects/Lead/" + data[index]['columns'][8]['value'];
		}else{
				var url = "./app/api.php?get=/services/data/v42.0/sobjects/Lead/" + data[index]['columns'][9]['value'];
		}

		$.ajax({
			url : url,
			success : function(res){
				var listData = JSON.parse(res);
				if ( typeof listData[0]!== "undefined" && listData[0]){
			     	if (listData[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
			    }

				console.log(listData);
				// get mapping fields
				var _name 		= $('#name').val();
				var _company 	= $('#company').val();
				var _email 		= $('#email').val();
				var _lead_status = $('#lead_status').val();
				var _phone 		= $('#phone').val();
				var _mobile 	= $('#mobile').val();
				// get data to send	
				var campaign 	= $('#campaign').val();
				var subcampaign = $('#subcampaign').val();
				var securityCode = $('#securityCode').val();
				var groupId 	= $("#groupId").val();
				var firstName 	= listData['FirstName'];
				var lastName  	= listData['LastName'];
				var name 		= listData['Name'];
				var	address   	= listData['Street'] + " " + listData['City'] + ", " + listData['State'] + " " + listData['PostalCode'] + " " + listData['Country'];
				var city 	  	= listData['City'];
				var state 		= listData['State'];
				var zipcode 	= listData['PostalCode'];
				var notes 		= listData['Notes__c'];
				var phone 		= listData['Phone'];
				var mobile 		= listData['MobilePhone'];
				var email 		= listData['Email'];
				var company 	= listData['Company'];
				var lead_status = listData['Status'];

				url1 =  "http://api.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" + campaign + "&Subcampaign=" + subcampaign +
						"&GroupId=" + groupId + "&SecurityCode=" + securityCode 
						+ "&" + _name + "=" + name 
						// + "&LastName=" 	+ lastName 
						+ "&Address=" 	+ address 
						+ "&City=" 		+ city 
						+ "&State=" 	+ state 
						+ "&ZipCode=" 	+ zipcode 
						+ "&Notes=" 	+ notes 
						+ "&" + _email  + "=" + email 
						+ "&" + _company+ "=" + company
						+ "&" + _lead_status  + "=" + lead_status
						+ "&" + _phone  + "=" + phone 
						+ "&" + _mobile + "=" + mobile + "&DuplicatesCheck=2";
						console.log("--------------- Inject ----------------");
						console.log(url1);
				$.ajax({
					url : url1,
					// method : "GET",
					success : function(res){
						console.log(res);
						// if (res!="<br><br>Result: CURL error[500]: Duplicated phone number. Lead ignored."){
						if (res!="<br><br>Result:  OK"){
							url2 = url = "http://api.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=" + groupId 
									+ "&SecurityCode=" 	+ securityCode 
									+ "&Address="  		+ address   
								  	+ "&State=" 		+ state 
								  	+ "&ZipCode=" 		+ zipcode   
								  	+ "&Notes=" 		+ notes
									+ "&SearchField=Phone&Identifier=" + phone 
									+ "&" + _name + "=" + name 
								  	// + "&LastName=" + lastName 
								  	+ "&" + _mobile + "=" + mobile 
								  	+ "&" + _email  + "=" + email 
									+ "&" + _company+ "=" + company
									+ "&" + _lead_status  + "=" + lead_status;
							console.log("--------------- Update ----------------");
							console.log(url2);
							$.ajax({
								url : url2,
								// method : "GET",
								success : function(res){
									console.log(res);
									index++;
									send();
								}
							});
						} else {
							index++;
							send();
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


// // https://www.chasedatacorp.com/HttpImport/InjectLead.php?GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&DuplicatesCheck=2&Campaign=TEST&Subcampaign=HotLeads&FirstName=John&LastName=Smith&PrimaryPhone=9541231234&adv_MobilePhone=9543214321
// https://www.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&DuplicatesCheck=2&Campaign=111&Subcampaign=HotLeads&Name=John&LastName=Smith&SearchField=Phone&Identifier=9541231234&MobilePhone=9543214321&Notes=null&ZipCode=null&Address=null null, PA null USA
// https://www.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=111&Subcampaign=American Banking Corp.1&GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&FirstName=Eugena Luce&Address=null null, MA null USA&City=null&State=MA&ZipCode=null&Notes=null&email=eluce@pacificretail.com&Company=Pacific Retail Group&Working - Contacted=Closed - Not Converted&PrimaryPhone=(781) 270-6510&MobilePhone=null&DuplicatesCheck=2
