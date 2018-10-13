
(function() {
	// force the page to refresh if the user has got there using the browser's 'Back' button
	window.addEventListener( "pageshow", function ( event ) {
	  var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
	  if ( historyTraversal ) {
		// Handle page restore.
		window.location.reload();
	  }
	});
	
	var previousContentPanel;
	var dataDialog;
	var isAdmin = true;
	
	// button actions
	$('[data-action]').click( function (e) {
        e.preventDefault();
		
		var action = $(this).data('action');
		switch(action) {
			case 'changeContentPanel':
				// get the ID of the target panel
				var panelID = $(this).data('panel');
				
				if(panelID == 'previous') {
					panelID = previousContentPanel ? previousContentPanel : 'login';
				} else {
					// store a reference to current (soon to be previous) content panel
					previousContentPanel = $('.content-panel').not('.hidden').attr('id');
				}
				
				// clear any messages
				$('.message').html('').addClass('hidden');
				
				// change what content is visible on the login page
				$('.content-panel').not('#'+panelID).addClass('hidden');
				$('#'+panelID).removeClass('hidden');
				
				if(panelID == 'help') $('[data-panel="help"]').addClass('hidden');
				else $('[data-panel="help"]').removeClass('hidden');
				
				break;
		}
	});
	
	$('#btn-login').click(function(e) {
		e.preventDefault();
		if($(this).hasClass('disabled')) return;
		
		// clear any previous message
		var message = '';
		$('#login .message').html('').addClass('hidden').removeClass('error');
		
		var username = $('#login input[name="userid"]').val();
		var pwd = $('#login input[name="passwd"]').val();
		
		// check whether both username & pwd have been entered
		if(username == '' || pwd == '') {
			// show an error message
			message = 'You need to enter ' + (username == '' ? 'a username.' : 'your password.');			
			$('#login .message').html(message).addClass('error').removeClass('hidden');
			
			return;
		}
		
		// try the login
		$.ajax({
		  method: "POST",
		  url: "../user/user.php",
		  data: { 
			  mode: 'authenticate',
			  userid: username, 
			  passwd: pwd 
			}
		})
		.done(function(data) {
		//alert( "success - " +data);
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				var userid = xml.find( "id" ).text();
				
				// prompt the user to select company & period
				promptForData(userid);
				
				// TEMP - go to main page
				//window.location.assign("main.php?company=Company C&month=1|2017")
			} else {
				message = 'Incorrect username or password.';			
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to authenticate with the server.';			
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	});
	
	function promptForData(userid) {
		// clear the selects in the dialog
		$('.data-dialog select').val('');
		$('.data-dialog option').not('.data-dialog option[value=""]').remove();
		$('.period-select-container').addClass('hidden');
		
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
				var dialogContent = $('.data-dialog').clone();
				
				xml.find('org').each(function(index) {
					//alert($(this).find('orgname').text());
					var orgid = $(this).find('orgid').text();
					var orgname = $(this).find('orgname').text();
					$('.company-select', dialogContent).append('<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>');
					
					if(index == 0) $('.company-set .value', dialogContent).html(orgname);
				});
				
				if(xml.find('org').length == 1) {
					$('.company-set', dialogContent).removeClass('hidden');
					updateAvailablePeriods($('.company-set .value', dialogContent).text(), dialogContent);
				} else {
					$('.company-select-container', dialogContent).removeClass('hidden');
				}
				
				$('.company-select', dialogContent).change(function(e) {
					var company = $(this).val();
					
					if(company != '') {
						updateAvailablePeriods(company);
					} else {
						$('.period-select-container', dialogContent).addClass('hidden');
						dataDialog.getButton('ok').disable();
					}
				});
				
				$('.period-select', dialogContent).change(function(e) {
					var period = $(this).val();
					
					if(period != '') {
						// a period has been selected, so enable the 'OK' button
						dataDialog.getButton('ok').enable();
					} else {
						dataDialog.getButton('ok').disable();
					}
				});
				
				var dialogButtons = [{
					id: 'ok',
					label: 'OK',
					cssClass: 'btn-primary',
					action: function(dialogRef){
						// disable the dialog elements so user can't do much until page changes
						$('.company-select, .period-select', dialogContent).attr('disabled', 'disabled');
						
						dialogRef.getButton('ok').disable();
						dialogRef.getButton('admin').disable();
							
						// redirect to main content with selected company & period
						var selectedCompany = ($('.company-select option', dialogContent).length > 2) ? $('.company-select', dialogContent).val() : $('.company-value', dialogContent).text();
						var selectedPeriod = $('.period-select', dialogContent).val();
						
						$('#formLogin input[name="company"]').val(selectedCompany);
						$('#formLogin input[name="period"]').val(selectedPeriod);
						
						$('#formLogin').submit();
						
						//window.location.href = "main.php?company="+selectedCompany+"&month="+selectedPeriod;
					}
				}];
				
				if(isAdmin) {
					dialogButtons.push({
						id: 'admin',
						label: 'Admin',
						cssClass: 'btn-link pull-left',
						action: function(dialogRef) {
							// disable the dialog elements so user can't do much until page changes
							$('.company-select, .period-select', dialogContent).attr('disabled', 'disabled');
							
							dialogRef.getButton('ok').disable();
							dialogRef.getButton('admin').disable();
							
							$('#formLogin input[name="company"]').remove()
							$('#formLogin input[name="period"]').remove();
							
							$('#formLogin').submit();
							
							// redirect to main content with no company & period
							//window.location.href = "main.php?userid4="+userid;
						}
					});
				}
				
				// show the dialog
				dataDialog = BootstrapDialog.show({
					title: 'Select data source',
					message: dialogContent,
					cssClass: 'period-dialog',
					type: BootstrapDialog.TYPE_PRIMARY,
					closable: false,
					width: '150px',
					onshow: function(dialog) {
						dialog.getButton('ok').disable();
					},
					buttons: dialogButtons
				});
			} else {
				message = 'Unable to get company information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get company information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	function updateAvailablePeriods(company, context) {
		if(!context) context = $('.modal-body');
		
		// clear any existing period options
		$('.period-select option', context).not('.period-select option[value=""]').remove();
		
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
				var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
					
				xml.find('Value').each(function(index) {
					var month = parseInt($(this).find('month').text());
					var year = $(this).find('year').text();
					
					$('.period-select', context).append('<option value="'+month+'|'+year+'">'+monthNames[month-1]+' '+year+'</option>');
					
					if(index == 0) $('.period-set .value', context).html(monthNames[month-1]+' '+year);
				});
				
				if(xml.find('Value').length == 1) {
					$('.period-set', context).removeClass('hidden');
					
					// only a single period, so enable the 'OK' button
					dataDialog.getButton('ok').enable();
				} else {
					$('.period-select-container', context).removeClass('hidden');
				}
			} else {
				message = 'Unable to get period information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get period information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	
	// reset password
	$('#btn-reset').click(function(e) {
		e.preventDefault();
		if($(this).hasClass('disabled')) return;
		 
		// clear any previous message
		var message = '';
		$('#forget-password .message').html('').addClass('hidden').removeClass('error').removeClass('success');
		
		var username = $('#forget-password input[name="userid"]').val();
		
		// check whether username has been entered
		if(username == '') {
			// show an error message
			message = 'You need to enter a username.';			
			$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
			
			return;
		}
		
		// try to reset password
		$.ajax({
		  method: "POST",
		  url: "../user/user.php",
		  data: { 
			  mode: 'forgotpass',
			  userid: username
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
			var reason = xml.find( "message" ).text();
		  
			if(result == 'ok') {
				message = 'Your password has been reset and emailed to you.';			
				$('#forget-password .message').html(message).addClass('success').removeClass('hidden');
			} else {
				message = 'Unable to reset your password: '+reason;			
				$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to reset passowrd.';			
			$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
		});
	});
	
	
})();