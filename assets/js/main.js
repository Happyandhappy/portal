	var current_view = null;
	var index = 0;
	var size = 0;
	var refreshRate = 0;
	var data;
	var interval=null;
	var opt = 0;
	var id_index = 0;
	var symbols = {};
	var isPhone = false;
	var phone = "";
	$(document).ready(function(){
			refreshRate = $("#refreshRate").val();
			current_view = $("#views").val();
			
			if ($('#option').val() === 'Start')	opt = 0;
			else opt = 1;
			
			if (current_view !="- None -")	getmappingData();
			
			if(refreshRate >0 && current_view !="- None -"){
				if (interval!= null) clearInterval(interval);
				interval = setInterval(function(){ getmappingData();}, refreshRate*60*1000);
			}
//////////////////////////////////////////////////////////////////////////////////////////////////////
			/* Change of view selector */
			$("#views").change(function(){
				current_view = $(this).val();
				$("#table").html('');
				$(".loader").removeClass("hidden");

				$.ajax({
					url : "./app/api.php?get=" + current_view,
					success : function(res){
						var dt = JSON.parse(res);
						console.log(dt);
						$(".loader").addClass("hidden");
						for (var i = 0 ; i < dt['columns'].length ; i++){
							if (dt['columns'][i]['hidden'] === false){
								$('.form').append("<input type='hidden' name='" + dt['columns'][i]['fieldNameOrPath'] + "' value='" + dt['columns'][i]['label'] + "'>");
							}
						}
						$(".form").submit();						
					}
				});
			});
/////////////////////////////////////////////////////////////////////////////////////////////////////
			// set stop / start sending requests
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

				$.ajax({
					url : "./app/api.php?option=" + opt + "&view=" + $('#views').val(),
					success : function(res){},
					error : function(err){}
				});
			});
	});
	
	function getmappingData(){
		var url = current_view ; 
		$(".loader").removeClass("hidden");
		$("#views").prop("disabled", true);
		var sym = $('.custom');
		
		for (var i = 0 ; i < sym.length ; i++){
			if (sym[i].value!=""){
				symbols[sym[i].name]=sym[i].value;
				if (sym[i].name==="Phone") isPhone = true;   // check Phone field
			}
		}

		$.ajax({
			url : "./app/api.php?get=" + url,
			success : function(res){
			    $(".loader").addClass("hidden");
		     	data = JSON.parse(res);

				showTable();
				$("#views").prop("disabled", false);
			},
			error : function(err){
				console.log(err);
			}
		});
	}

	function showTable(){
		console.log(data);
		if (!isPhone)	alert("There is no phone field"); // alert show when phone field does not exist.
		size  = data['records'].length;
		if (size === 0) return;
		var i,j=-1;
		$("#table").html('');

		// add thead in table
		var str = "<thead><th> _No </th>";
		for ( i = 0 ; i < data['columns'].length ; i++){
			if (data['columns'][i]['hidden']===false)
				str = str + "<th>" + data['columns'][i]['label'] + "</th>";
			if (data['columns'][i]['fieldNameOrPath'] === 'Id') id_index = i;
		}
	
		str = str + "</thead>";
		$("#table").append(str);
	
		// add tbody in table
		str = "<tbody>";		
		for ( i = 0 ; i < data['records'].length ; i++){
			str = str + "<tr><td>" + (i+1) + "</td>";
			for ( var j = 0 ; j < data['columns'].length ; j++){
				if (data['columns'][j]['hidden']===false){
					str = str + "<td>" + data['records'][i]['columns'][j]['value'] + "</td>";
				}
			}
			str = str + "</tr>";
		}
		str = str + "</tbody>";
		str = str.split("null").join("");
		$("#table").append(str);

		index = 0;
		if (opt===1 && isPhone) send();
	}

	function checkSym(ddt){
		var result = false;
		$.each(symbols, function(key, value) {
			if (key===ddt)	result = true;
		});
		return result;
	}

	function send(){
		if (index > size) return;
		var campaign 	= $('#campaign').val();
		var subcampaign = $('#subcampaign').val();
		var securityCode = $('#securityCode').val();
		var groupId 	= $("#groupId").val();
		var _url = "";
		for (var i = 0 ; i < data['columns'].length ; i++){
			if (data['columns'][i]['hidden']===false && checkSym(data['columns'][i]['fieldNameOrPath'])===true && data['records'][index]['columns'][i]['value']!=null){
				_url = _url + "&" + $('#' + convertName(data['columns'][i]['fieldNameOrPath'])).val() + "=" + data['records'][index]['columns'][i]['value'];
				if (data['columns'][i]['fieldNameOrPath']==='Phone') phone = data['records'][index]['columns'][i]['value'];				
			}
		}
		url1 =  "http://api.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" + campaign + "&Subcampaign=" + subcampaign +
						"&GroupId=" + groupId + "&SecurityCode=" + securityCode + _url + "&DuplicatesCheck=2";
		console.log(url1);
		
		$.ajax({
			url : url1,
			success : function(res){

				if (res!="<br><br>Result:  OK"){
					var url2 = "http://api.chasedatacorp.com/HttpImport/UpdateLead.php?Campaign=" + campaign
					+ "&Subcampaign="   + subcampaign 
					+ "&SecurityCode=" 	+ securityCode
					+ "&GroupId=" 		+ groupId + _url
					+ "&SearchField=Phone&Identifier=" + phone;
					console.log("--------------- Update ----------------");
					console.log(url2);
					$.ajax({
						url : url2,
						success : function(res){console.log(res); index++;send();
					}});
				}else{
					index++; send();
				}
			}
		});

/*
		var url = "./app/api.php?get=/services/data/v42.0/sobjects/Lead/" + data['records'][index]['columns'][id_index]['value'];
		$.ajax({
			url : url,
			success : function(res){
				var listData = JSON.parse(res);
				if ( typeof listData[0]!== "undefined" && listData[0]){
			     	if (listData[0]['errorCode'] == "INVALID_SESSION_ID") window.location.replace("./app/api.php?logout="); 				     	
			    }

				console.log(listData);
				// get data to send	
				var campaign 	= $('#campaign').val();
				var subcampaign = $('#subcampaign').val();
				var securityCode = $('#securityCode').val();
				var groupId 	= $("#groupId").val();
				var url1 = "";
				for (var i = 0 ; i < data['columns'].length ; i++){
					if (data['columns'][i]['hidden']===false){
						console.log( i + ":" + convertName(data['columns'][i]['fieldNameOrPath']) + ":" + listData[data['columns'][i]['fieldNameOrPath']])
						url1 = url1 + "&" + $('#' + convertName(data['columns'][i]['fieldNameOrPath'])).val() + "=" + listData[data['columns'][i]['fieldNameOrPath']];
					}
				}


				url1 =  "http://api.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" + campaign + "&Subcampaign=" + subcampaign +
						"&GroupId=" + groupId + "&SecurityCode=" + securityCode + url1;
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
		});*/
	}

	function convertName(name){
		return name.replace('.','_');
	}

// // https://www.chasedatacorp.com/HttpImport/InjectLead.php?GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&DuplicatesCheck=2&Campaign=TEST&Subcampaign=HotLeads&FirstName=John&LastName=Smith&PrimaryPhone=9541231234&adv_MobilePhone=9543214321
// https://www.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&DuplicatesCheck=2&Campaign=111&Subcampaign=HotLeads&Name=John&LastName=Smith&SearchField=Phone&Identifier=9541231234&MobilePhone=9543214321&Notes=null&ZipCode=null&Address=null null, PA null USA
// https://www.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=111&Subcampaign=American Banking Corp.1&GroupId=777&SecurityCode=D416ED4A9E45453292A0EC3872DA3081&FirstName=Eugena Luce&Address=null null, MA null USA&City=null&State=MA&ZipCode=null&Notes=null&email=eluce@pacificretail.com&Company=Pacific Retail Group&Working - Contacted=Closed - Not Converted&PrimaryPhone=(781) 270-6510&MobilePhone=null&DuplicatesCheck=2
