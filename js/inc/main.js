
(function() {
	function promptToChangeData(pageID) {
		// clear the selects in the dialog
		$('.change-data-dialog select').val('');
		$('.change-data-dialog option').not('.change-data-dialog option[value=""]').remove();
		$('.period-select-container').addClass('hidden');
		
		if(typeof userid == 'undefined') alert('User ID undefined');
		
		$.ajax({
		  method: "POST",
		  url: "../user/org.php",
		  data: { 
			  mode: 'getorgassigned',
			  userid: userid
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				var options = '';
				xml.find('org').each(function(index) {
					//alert($(this).find('orgname').text());
					var orgid = $(this).find('orgid').text();
					var orgname = $(this).find('orgname').text();
					//$('.company-select').append('<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>');
					options += '<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>';
				});
				
				// prepare the dialog content
				var dialogContent = $('.change-data-dialog').clone();
				$('.company-select', dialogContent).append(options);
				
				// set the change handlers
				$('.company-select', dialogContent).change(function(e) {
					var company = $(this).val();
					
					if(company != '') {
						updateAvailablePeriods(company);
					} else {
						$('.period-select-container').addClass('hidden');
						changeDataDialog.getButton('ok').disable();
					}
				});
				
				// show the dialog
				var changeDataDialog = BootstrapDialog.show({
					title: 'Select data source',
					message: dialogContent,
					cssClass: 'period-dialog',
					type: BootstrapDialog.TYPE_PRIMARY,
					closable: false,
					width: '150px',
					onshow: function(dialog) {
						dialog.getButton('ok').disable();
					},
					onshown: function(dialog) {
						
					},
					buttons: [{
						id: 'cancel',
						label: 'Cancel',
						cssClass: 'btn-secondary',
						action: function(dialogRef){
							dialogRef.close();
						}
					},
					{
						id: 'ok',
						label: 'OK',
						cssClass: 'btn-primary',
						action: function(dialogRef){
							// disable the dialog elements so user can't do much until page changes
							$('.company-select, .period-select').attr('disabled', 'disabled');
							
							dialogRef.getButton('ok').disable();
							dialogRef.getButton('cancel').disable();
								
							// refresh with selected company & period
							var selectedCompany = $('.company-select', dialogContent).length > 0 ? $('.company-select', dialogContent).val() : $('.company-value').text();
							var selectedPeriod = $('.period-select', dialogContent).val();
							
							var path = "main.php?company="+selectedCompany+"&month="+selectedPeriod;
							if(pageID) path += '&page='+pageID;
							
							window.location.assign(path);
						}
					}]
				});
				
				// set the change handler for the period (needs dialog ref)
				$('.period-select', dialogContent).change(function(e) {
					var period = $(this).val();
					
					if(period != '') {
						// a period has been selected, so enable the 'OK' button
						changeDataDialog.getButton('ok').enable();
					} else {
						changeDataDialog.getButton('ok').disable();
					}
				});
				
				
			} else {
				message = 'Unable to get company information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			alert('fail');
			message = 'Failed to get company information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	function updateAvailablePeriods(company) {
		// clear any existing period options
		$('.period-select option').not('.period-select option[value=""]').remove();
		
		$.ajax({
		  method: "POST",
		  url: "../informer/reports.php",
		  data: { 
			  mode: 'monthyear',
			  company: company
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				xml.find('Value').each(function(index) {
					var month = parseInt($(this).find('month').text());
					var year = $(this).find('year').text();
					
					var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
					
					$('.period-select').append('<option value="'+month+'|'+year+'">'+monthNames[month-1]+' '+year+'</option>');
				});
				
				// show the period select
				$('.period-select-container').removeClass('hidden');
			} else {
				message = 'Unable to get period information.';	
				$('#error .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get period information.';	
			$('#error .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	$('.btn-test, [data-action="changeData"]').click(function(e) {
		var pageID = $(this).attr('data-page');
		
		promptToChangeData(pageID);
	});	
	
})();