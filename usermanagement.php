<?php
    ob_start();

	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";



	$role=logincheck();
	if ($role == "") header("Location: index.php?msg=Please login to continue.");
	
	$initialContent = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Informer 4 SMB</title>


        <?php 
			include("inc/head.inc.php"); 
		?>
		<style>
		.multiselect-container .checkbox input[type=checkbox] {
            opacity: 1;
            left: 8px;
            top: 7px;
        }

        span.login_id {
            color: #bf712d;
            font-weight: 400;
            font-size: 14px;
        }

        .multiselect-container .checkbox {
            line-height: 30px;
        }

        .multiselect-container>li {
            display: block;
            height: 30px;
        }

        .multiselect-container>li>a {
            height: 30px;
        }

        span.multiselect-native-select {
            position: relative;
            top: 10px;
        }
        </style>
		<script>
			var userid = <?php echo $_SESSION['userid']; ?>;
			var isAdmin = <?php
			                    if ($role == 9)
			                        echo 'true';
			                     else
			                        echo 'false';
			               ?>;
		</script>
			
		<script>



			function showrpt(comp,yr,mnth) {
				$.get('reports.php', {showrpt: true, company: comp,year:yr,month:mnth}, function(response) {
					document.getElementById('divrpt').innerHTML=response;
				});
			}

			function showrpttabs(yrs,comp,type) {
				$.get('reports.php', {yeararray: yrs, company: comp, rpttype: type}, function(response) {
					// Log the response to the console
				   // alert("Response: "+response);
					document.getElementById('divrpttabs').innerHTML=response;
				});
			}

			function mapall(ele,tabs) {
				if(ele.checked) {
					//confirm("Do you wish to use same mapping for all the months?");
					var items = parseInt(document.getElementById('key').value);
					for (var tab=0; tab<tabs.length; tab++) {
						tabs[tab]=tabs[tab].trim();
						tabs[tab]=tabs[tab].replace("-", "");
						tabs[tab]=tabs[tab].replace(" ", "");
						
						if(tab==0) continue;
						
						for (var i = 0; i < items; i++) {
							//alert ('cat1'+tabs[tab]+i+'--'+'\n'+'cat2'+tabs[tab]+i+'--'+'\n'+'cat3'+tabs[tab]+i+'--'+'\n'+'type'+tabs[tab]+i+'--');
							//alert('cat1'+(tabs[0].trim()).replace(" ", "")+i);
						//	alert(document.getElementById('cat130Jun20170').value);
						//	alert(document.getElementById('cat1'+(tabs[0].trim()).replace(" ", "")+i).value);
							
							var cat1val=document.getElementById('cat1'+(tabs[0].trim()).replace(" ", "")+i).value;
							var cat2val=document.getElementById('cat2'+(tabs[0].trim()).replace(" ", "")+i).value;
							var cat3val=document.getElementById('cat3'+(tabs[0].trim()).replace(" ", "")+i).value;
							var type=document.getElementById('type'+(tabs[0].trim()).replace(" ", "")+i).value;
							
					//	alert('cat1'+(tabs[tab].trim()).replace(" ", "")+i);
						//	alert ('cat1'+tabs[tab]+i);
							document.getElementById('cat1'+(tabs[tab].trim()).replace(" ", "")+i).value=cat1val;
							document.getElementById('cat2'+(tabs[tab].trim()).replace(" ", "")+i).value=cat2val;
							document.getElementById('cat3'+(tabs[tab].trim()).replace(" ", "")+i).value=cat3val;
							document.getElementById('type'+(tabs[tab].trim()).replace(" ", "")+i).value=type;
							//alert ('cat1'+tabs[tab]+i+'--'+cat1val+'\n'+'cat2'+tabs[tab]+i+'--'+cat2val+'\n'+'cat3'+tabs[tab]+i+'--'+cat3val+'\n'+'type'+tabs[tab]+i+'--'+type);
							//alert(items );
						} 
					} 
				}
			}
			</script>
    </head>
			
	<body class="">
		<?php
			// CHECK PERMISSIONS
			if ($permissions['Admin users']==0) {
				echo $GLOBALS['accesserrormsg']; 
				die;
			}
		?>
		
		<?php 
			$defaultTitle = 'User Management';
			include("inc/navbar.inc.php"); 
		?>

		<?php

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, "https://informer4smb.com.au/pla/user/user.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'list','format' => 'json','userid' => $_SESSION['userid']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            json_decode($result, true);


             $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, "https://informer4smb.com.au/pla/user/org.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'orglist','format' => 'json','userid' => $_SESSION['userid']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $orgresult = curl_exec($ch);
            curl_close($ch);
            json_decode($orgresult, true);

        ?>

                <script type="text/javascript">

                    var userdata = <?php echo $result ?>;
                    var orgdata = <?php echo $orgresult ?>;

                    console.log(orgdata);
                    console.log(userdata);



                    function resetforNewUser(){

                        var newUserMode = '<input class="newUserMode" type="hidden" name="mode" value="create">';

                        $("#userform").prepend(newUserMode);
                        $(".editUserMode").remove();

                        $("#addNewUser").removeClass('edit-UserModal');

                        $("#userid").val('');
                        $("#loginid").val('').removeAttr("disabled");
                        $("#verifyAbn").removeAttr("disabled");
                        $('#fname').val('');
                        $('#lname').val('');
                        $('#address').val('');
                        $('#mobile').val('');
                        $('#phone').val('');
                        $('#email').val('');
                        $("#confemail").val('');
                        $('#orgid option').prop("selected", false);
                        $("#regtype input[name='regtype'][value='other']").prop('checked', true);
                        $('#role').val('');
                        $("#assignorgid option").prop("selected", false);
                        $('#assignorgid').multiselect('refresh');


                    }


                    function editUser(id){

                        resetforNewUser();

                        var tempUser;
                        var editUserMode = '<input class="editUserMode" type="hidden" name="mode" value="modify">';

                        $("#userform").prepend(editUserMode);
                        $(".newUserMode").remove();



                        if(userdata.user.length == undefined){
                            var singleUser = userdata.user
                            userdata.user = [];
                            userdata.user.push(singleUser);
                        }

                        $.each(userdata.user, function(index, element) {
                            if(element.id == id){
                                tempUser = element;
                            }
                        });

                        $("#addNewUser").addClass('edit-UserModal');
                        //var el = $("#addNewUser").elements;

                        var orgUrl = 'https://informer4smb.com.au/pla/user/org.php';

                        var getObject = { mode: null, userid: null, format : 'json'};
                        getObject.mode = 'getorgassigned';
                        getObject.userid = tempUser.id;

                         $.ajax({
                               type: "POST",
                               url: orgUrl,
                               data: $.param(getObject), // serializes the form's elements.
                               success: function(data)
                               {
                                  var returnobj = JSON.parse(data);
                                  var dtaobj = returnobj.orgs.org;
                                  var tempAssingOrgarry = [];

                                  if(returnobj.orgs.org.length == undefined){
                                      var singleorg= returnobj.orgs.org
                                      returnobj.orgs.org = [];
                                      returnobj.orgs.org.push(singleorg);
                                      dtaobj = [];
                                      dtaobj.push(singleorg);
                                  }

                                  Object.keys(dtaobj).forEach(function(key) {
                                    //console.log(key, dtaobj[key].orgid);
                                    tempAssingOrgarry.push(dtaobj[key].orgid)

                                  });

                                  tempUser['assignorgid'] = tempAssingOrgarry;

                                  var values = tempUser.assignorgid;
                                  if ( tempUser.assignorgid  == null){
                                      values= ''+tempUser.orgid;
                                      $("#assignorgid selected disabled hidden option[value='" + tempUser.orgid + "']").prop("selected", true);
                                  }

                                  if ( tempUser.assignorgid  != null){
                                      $.each(values, function(i,e){
                                          $("#assignorgid option[value='" + e + "']").prop("selected", true);
                                      });
                                  }

                                  $('#assignorgid').multiselect('refresh');

                                  /*$('#assignorgid-later').multiselect({
                                           buttonText: function(options, select) {
                                               console.log(select[0].length);
                                               if (options.length === 0) {
                                                   return 'None selected';
                                               }
                                               if (options.length === select[0].length) {
                                                   return 'All selected ('+select[0].length+')';
                                               }
                                               else if (options.length >= 4) {
                                                   return options.length + ' selected';
                                               }
                                               else {
                                                   var labels = [];
                                                   console.log(options);
                                                   options.each(function() {
                                                       labels.push($(this).val());
                                                   });
                                                   return labels.join(', ') + '';
                                               }
                                           }

                                   });*/


                               }
                         });



                        $("#userid").val(tempUser.id);
                        $("#loginid").val(tempUser.login).prop("disabled", true);


                        if(jQuery.isEmptyObject(tempUser.first_name)){tempUser.first_name = ''};
                        $('#fname').val(tempUser.first_name);
                        $('#lname').val(tempUser.surname);
                        if(jQuery.isEmptyObject(tempUser.address)){tempUser.address = ''};
                        $('#address').val(tempUser.address);
                        if(jQuery.isEmptyObject(tempUser.mobile)){tempUser.mobile = ''};
                        $('#mobile').val(tempUser.mobile);
                        if(jQuery.isEmptyObject(tempUser.phone)){tempUser.phone = ''};
                        $('#phone').val(tempUser.phone);
                        $('#email').val(tempUser.email);
                        $("#confemail").val(tempUser.email);
                        //$('#orgid').val(tempUser.orgid);
                        $("#orgid option[value='" + tempUser.orgid + "']").prop("selected", true);
                        //$("#regtype input[value='" + tempUser.regtype + "']").prop("checked");
                        $("#regtype input[name='regtype'][value='" + tempUser.regtype + "']").prop('checked', true);
                        $('#role').val(tempUser.role);



                    }

                    function editUserField(id, objkey , value, pass, fail){


                        var tempUser;
                        var url = 'https://informer4smb.com.au/pla/user/user.php';

                        $.each(userdata.user, function(index, element) {
                            if(element.id == id){
                                tempUser = element;
                            }
                        });

                        tempUser.mode = 'modify';
                        tempUser.fname = tempUser.first_name;
                        tempUser.lname = tempUser.surname;
                        tempUser.userid = tempUser.id;
                        tempUser.format = 'json';
                        tempUser[objkey] = value;

                        $.ajax({
                           type: "POST",
                           url: url,
                           data: $.param(tempUser), // serializes the form's elements.
                           success: function(data)
                           {
                                var returnobj = JSON.parse(data);

                                if(returnobj.result == 'pass' || returnobj.result == 'ok'){
                                    bootbox.alert({message: returnobj.message, size: 'small', className: 'success-alert alert-with-icon',callback: function () { pass(); }}); // show response from the php script.


                                }else{
                                    bootbox.alert({message: returnobj.message, size: 'small', className: 'danger-alert alert-with-icon',callback: function () { fail(); }}) // show response from the php script.

                                }
                                console.log("debug : ", returnobj);



                           }
                         });

                    }

                     function resetforNewOrg(){

                        var newUserMode = '<input class="newOrgMode" type="hidden" name="mode" value="create">';

                        $("#orgform").prepend(newUserMode);
                        $(".editOrgMode").remove();

                        $("#addNewOrg").removeClass('edit-OrgModal');

                        $("#abn").val('').removeAttr("disabled");
                        $("#verifyAbn").val('').removeAttr("disabled");
                        //$('#orgidnew').val('').removeAttr('disabed');

                        $('#editorgid').val('');


                        $("#moreOrgName").after('<div id="oneOrgName"></div>');
                        $("#oneOrgName").html('<input type="text" class="form-control" id="orgname" name="orgname" value="" size="50" placeholder="Organisation Name">');
                        $("#moreOrgName").remove();

                        $('#orgname').val('');
                        $("#orgname").removeAttr("disabled");
                        $('#compaddress').val('');
                        $('#compemail').val('');
                        $('#compphone').val('');

                    }

                    function editOrg(id){

                        resetforNewOrg();

                        var tempOrg;
                        var editOrgMode = '<input class="editOrgMode" type="hidden" name="mode" value="modify">';

                        $("#orgform").prepend(editOrgMode);
                        $(".newOrgMode").remove();

                        $.each(orgdata.orgs.org, function(index, element) {
                            if(element.orgid == id){
                                tempOrg = element;
                            }
                        });

                        $("#addNewOrg").addClass('edit-OrgModal');
                        //var el = $("#addNewUser").elements;

                        $("#abn").val(tempOrg.abn).prop("disabled", true);
                        $("#verifyAbn").val(tempOrg.abn).prop("disabled", true);
                        $('#editorgid').val(tempOrg.orgid);
                        $('#orgname').val(tempOrg.orgname).prop("disabled", true);
                        if(jQuery.isEmptyObject(tempOrg.orgaddress)){tempOrg.orgaddress = ''};
                        $('#compaddress').val(tempOrg.orgaddress);
                        if(jQuery.isEmptyObject(tempOrg.orgemail)){tempOrg.orgemail = ''};
                        $('#compemail').val(tempOrg.orgemail);
                        if(tempOrg.orgphone.length){
                            $('#compphone').val(tempOrg.orgphone);
                        }else {
                            $('#compphone').val('');
                        }


                    }

                    function userManagementUserList() {

                         if(!(userdata.user === undefined)){

                             if(userdata.user.length == undefined){
                                var singleUser = userdata.user
                                userdata.user = [];
                                userdata.user.push(singleUser);
                             }



                            $.each(userdata.user, function(index, element) {
                                 //console.log(element);
                                 var userRow = '<tr><td class="middle"> <div class="media"> <div class="media-body"> <h4 class="media-heading">'+element.first_name +' '+ element.surname+' <span class="login_id">('+ element.login+')</span></h4> <span class="label label-veify label-verifts-'+ element.verified+'"> </span> <div class="hint--right hint--rounded hint--bounce toggle-middle admin-only st-'+element.verified+' " aria-label="User verification '+(element.verified === 'true' ? 'active' : 'pending' )+'" for="verified"><input type="hidden" name="'+element.id+'" value="'+element.verified+'"><i class="fa fa-toggle-on fa-lg on-fa" ></i><i class="off-fa fa fa-toggle-on fa-lg fa-rotate-180" ></i></div>  <address class="no-margin">'+ element.email+'</address> </div></div></td><td width="100" class="middle"> <div> <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit" data-toggle="modal" data-target="#addNewUser" onclick="editUser('+element.id+')"> <i class="fa fa-lg fa-pencil-square-o" aria-hidden="true"></i> </a> <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn hide "  title="Delete"> <i class="glyphicon glyphicon-trash"></i> </a> <div class="toggle-middle status-toggle hint--left hint--rounded hint--bounce st-'+element.status+' " aria-label="User status '+(element.status  === 'true' ? 'active' : 'inactive')+'" for="status" ><input type="hidden" name="'+element.id+'" value="'+element.status+'"><i class="fa fa-toggle-on fa-lg on-fa" ></i><i class="off-fa fa fa-toggle-on fa-lg fa-rotate-180" ></i></div>  </div></td></tr>';
                                 $(".user-management-list-table tbody").append(userRow)
                             });

                         }


                    }

                    function userManagementOrgList() {

                        if(orgdata.orgs.org.length == undefined){

                            var singleEle = orgdata.orgs.org
                            orgdata.orgs.org = [];
                            orgdata.orgs.org.push(singleEle);
                        }

                        //console.log("orgta s",orgdata);

                        $.each(orgdata.orgs.org , function(index, element) {
                            //console.log(element);
                            if(jQuery.isEmptyObject(element.orgemail)){element.orgemail = ''};
                            var userRow = '<tr><td class="middle"> <div class="media"> <div class="media-body"> <h4 class="media-heading">'+element.orgname +' <small>('+ element.abn+')</small></h4> <address class="no-margin">'+ element.orgemail+'</address> </div></div></td><td width="100" class="middle"> <div> <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit" data-toggle="modal" data-target="#addNewOrg"  onclick="editOrg('+element.orgid+')"> <i class="fa fa-lg fa-pencil-square-o" aria-hidden="true"></i> </a> <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn hide" title="Delete"> <i class="glyphicon glyphicon-trash"></i> </a> </div></td></tr>';
                            $(".organisation-list-table tbody").append(userRow)
                        });

                    }

                    function checkEmailMatch() {
                        var email = $("#email").val();
                        var confirmemail = $("#confemail").val();

                        if (email != confirmemail)
                            $('#conf-email-error').show();
                        else
                            $('#conf-email-error').hide();
                    }


                    $(document).ready(function() {

                        $('#compemail').on('keyup', function() {
                            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.value);
                            if(!re) {
                                $('#compemail-error').show();
                            } else {
                                $('#compemail-error').hide();
                            }
                        })

                        $('#email').on('keyup', function() {
                            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(this.value);
                            if(!re) {
                                $('#email-error').show();
                            } else {
                                $('#email-error').hide();
                            }
                        })

                        $("#email, #confemail").keyup(checkEmailMatch);

                        userManagementUserList();
                        userManagementOrgList();

                        console.log(isAdmin);

                        if(isAdmin){
                            $(".admin-only").removeClass("admin-only");
                        }

                        $('.toggle-middle').click(function() {
                            var clickBox = $(this);
                            var toggleinput = $(this).children("input");
                            var currentStatus = toggleinput.val();

                            if(currentStatus == "true"){
                                currentStatus = false;
                            }else {
                                currentStatus = true;
                            }
                            var useridforedit = toggleinput.attr("name")
                            var temobjkey = clickBox.attr("for");

                            console.log('useridforedit : '+useridforedit + ' currentStatus '+ !currentStatus );

                            editUserField(useridforedit , temobjkey , currentStatus , function(){clickBox.toggleClass('st-true st-false');location.reload();}, function(){});

                        });

                        $('#verifyAbn').click(function() {

                                var abnNumber = $("#abn").val();
                                abnNumber = abnNumber.replace(/\s+/g, '');
                                var resultData;

                                $.ajax({
                                    type:"GET",
                                    url: "https://abr.business.gov.au/json/AbnDetails.aspx?abn="+abnNumber+"&guid=4536d14e-23fc-42f3-8db8-991a70eb481d",
                                    success: function(data) {

                                            if(data.BusinessName.length){
                                                $("#oneOrgName").after('<div id="moreOrgName"></div>');
                                                $("#moreOrgName").html('<div id="moreOrgName"><select class="form-control orgSelectList" id="orgname" name="orgname" placeholder="Organisation Name"></select></div>')
                                                $("#oneOrgName").remove();

                                                $.each(data.BusinessName, function (i, item) {
                                                    $('.orgSelectList').append($('<option>', {
                                                        value: item,
                                                        text : item
                                                    }));
                                                });
                                            }else {
                                                $("#moreOrgName" ).remove();
                                                $("#oneOrgName #orgname").val(data.EntityName);
                                            }

                                            //console.log(data.EntityName)
                                        },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                            alert("Yor abn number is "+abnNumber+". Wating to abr provide with GUID -  Error code : " + jqXHR.status);
                                        },
                                   dataType: "jsonp"
                                })

                        });


                    });

                    function validateuserform(form) {
                        var msg="";
                        el=form.elements;
                        var illegalChars = /\W/;

                        if(el.namedItem('userid').value=="")
                            msg+="Enter User ID \n";
                        if (illegalChars.test(el.namedItem('userid').value))
                            msg+= "The User ID contains illegal characters. Only letters, numbers, and underscores are allowed\n";
                        if(el.namedItem('fname').value=="")
                            msg+="Enter First Name\n";
                        if(el.namedItem('lname').value=="")
                            msg+="Enter Last Name\n";
                        if(el.namedItem('pass').value=="")
                            msg+="Enter Password\n";
                        if(!matchpassword(el.namedItem('pass').value,el.namedItem('confirmpass').value))
                            msg+="Passwords do not match \n";
                        if(el.namedItem('email').value=="")
                            msg+="Enter Email \n";
                        if(el.namedItem('email').value!="" && !validateEmail(el.namedItem('email').value))
                            msg+="Invalid email address\n";
                        if(el.namedItem('orgid').value=="")
                            msg+="Enter Organisation\n";

                        if(msg!="")	{
                            alert(msg);
                            return false;
                        }

                        return true;

                    }

                    $( document ).ready(function() {

                            $.each(orgdata.orgs.org, function(index, element) {
                                $('#assignorgid , #orgid').append($('<option>', {
                                        value: element.orgid,
                                        text : element.orgname
                                }));
                             });




                            // this is the id of the form
                            $("#userform").submit(function(e) {
                                var form = $(this);
                                var url = 'https://informer4smb.com.au/pla/user/user.php';


                                $.ajax({
                                       type: "POST",
                                       url: url,
                                       data: form.serialize(), // serializes the form's elements.
                                       success: function(data)
                                       {
                                            var returnobj = JSON.parse(data);

                                            //console.log(data);

                                            if(returnobj.result == 'pass' || returnobj.result == 'ok'){
                                                bootbox.alert({message: returnobj.message, size: 'small', className: 'success-alert alert-with-icon', callback: function () {location.reload();}}); // show response from the php script.
                                                $("#addNewUser").modal("hide");

                                            }else{
                                                bootbox.alert({message: returnobj.message, size: 'small', className: 'danger-alert alert-with-icon'}); // show response from the php script.
                                            }
                                            console.log("debug : ", returnobj);

                                       }
                                     });

                                //console.log('form', form);
                                //console.log('form serialize', form.serialize());

                                e.preventDefault(); // avoid to execute the actual submit of the form.
                            });


                            $("#orgform").submit(function(e) {
                                var form = $(this);
                                var url = 'https://informer4smb.com.au/pla/user/org.php';

                                $.ajax({
                                       type: "POST",
                                       url: url,
                                       data: form.serialize(), // serializes the form's elements.
                                       success: function(data)
                                       {
                                          var returnobj = JSON.parse(data);

                                          if(returnobj.result == 'pass' || returnobj.result == 'ok' ){
                                              bootbox.alert({message: returnobj.message, size: 'small', className: 'success-alert alert-with-icon', callback: function () {location.reload();}}); // show response from the php script.
                                              $("#addNewOrg").modal("hide");

                                          }else{
                                              bootbox.alert({message: returnobj.message, size: 'small', className: 'danger-alert alert-with-icon'}); // show response from the php script.
                                          }
                                          console.log("debug : ",returnobj);
                                       }
                                     });

                                e.preventDefault(); // avoid to execute the actual submit of the form.
                            });

                            //console.log( "ready!" );
                    });

                </script>



<section id="main">
<div id="content" class="">
	<div class="container">


        <div class="tab-content">
        		<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
        			<div class="panel-body">
        			    <ul class="nav nav-tabs">
                          <li class="active"><a data-toggle="tab" href="#userlist">User List</a></li>
                          <li><a data-toggle="tab" href="#organisationlist" >Organisation List</a></li>
                        </ul>
                        <!-- user management content -->
        	            <div id="content" class="tab-content">
        	              <div id="userlist" class="tab-pane fade in active">
        	                <h2 class="step">Current User List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" onclick="resetforNewUser()" data-target="#addNewUser">Add new User</button></h2>
                            <!-- user list -->
                            <table class="table user-management-list-table">
                               <tbody></tbody>
                            </table>
                        </div>

                         <div id="organisationlist" class="tab-pane fade">
                                <h2 class="step">Current Organisation List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" onclick="resetforNewOrg()"  data-target="#addNewOrg">Add new Organisation</button></h2>
                                <!-- Organisation list -->
                                <table class="table organisation-list-table">
                                   <tbody></tbody>
                                </table>
                         </div>


                        </div>
                     </div>
                 </div>
         </div>

	</div>
</div>
</section>

<!-- Add new user  Modal -->
<div id="addNewUser" class="modal fade informer-md-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title newUser-title">Add new user</h4>
        <h4 class="modal-title editUser-title">Edit user</h4>
      </div>
       <form class="form-horizontal" id="userform" name="users" onsubmit="return validateuserform(document.getElementById('userform'));">
                <input  type="hidden" name="format" value="json">
                <input type="hidden" class="form-control" id="userid" name="userid" placeholder="User ID">
       <div class="modal-body">


          <div class="form-group">
            <label class="control-label col-sm-3" for="loginid">User Login name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="loginid" name="loginid" placeholder="Enter new login name">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-3" for="pwd">Password:</label>
            <div class="col-sm-9">
              <input type="password" class="form-control"  id="pass" name="pass" placeholder="Enter password">
            </div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-3" for="confirmpass">Confirm Password:</label>
              <div class="col-sm-9">
                <input type="password" class="form-control"  id="confirmpass" name="confirmpass" placeholder="Enter Confirm password">
              </div>
            </div>

          <div class="form-group">
              <label class="control-label col-sm-3" for="fname">First Name:</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="fname" name="fname"  placeholder="Enter First Name">
              </div>
            </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="lname">Last Name:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name">
            </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="emaaddressil">Address:</label>
            <div class="col-sm-9">
              <textarea type="text" class="form-control" id="address" name="address" rows="3" cols="50" placeholder="Enter Address"></textarea>
            </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="email">Email:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email">
              <span id="email-error" class="bg-danger" style="display : none" >Invaid Email Address</span>
            </div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-3" for="confemail">Confirm email:</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="confemail" onChange="checkEmailMatch();" name="confemail" placeholder="Enter Confirm Email">
                <span id="conf-email-error" class="bg-danger" style="display : none" >Mismatched Email</span>
              </div>
            </div>
        <div class="form-group hide-for-edit">
            <label class="control-label col-sm-3" for="verifyemail">Send Verification Email:</label>
            <div class="col-sm-9" id="regtype">
              <label class="radio-inline"><input type="radio"  value="<?php echo $_SESSION['userid']; ?>" name="verifyemail" >To Me</label>
              <label class="radio-inline"><input type="radio" value="" name="verifyemail" onclick="this.value=document.getElementById('loginid').value">To User</label>
              </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="mobile">Mobile:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="mobile" name="mobile"  placeholder="Enter Mobile">
            </div>
          </div>


        <div class="form-group">
            <label class="control-label col-sm-3" for="phone">Phone:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone">
            </div>
          </div>



          <div class="form-group">
            <label class="control-label col-sm-3" for="role">User Type:</label>
            <div class="col-sm-9" id="regtype">
              <label class="radio-inline"><input type="radio"  value="owner" name="regtype">Entity</label>
              <label class="radio-inline"><input type="radio" value="advisor" name="regtype">Advisor</label>
              <label class="radio-inline"><input type="radio" value="other" name="regtype">Other</label>
            </div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-3" for="role">Assigned Role:</label>
              <div class="col-sm-9">
                <select  class="form-control" id="role" name="role">
                <option value="" selected disabled hidden>Choose Role</option>
                <option value="8">Administrator</option>
                <option value="2">Junior</option>
                <option value="1">Senior</option>
                <option value="3">User</option>
                </select>
              </div>
            </div>

             <div class="form-group">
                <label class="control-label col-sm-3" for="orgid">Organisation:</label>
                <div class="col-sm-9">
                  <select class="form-control" id="orgid" name="orgid" required></select>
                </div>
              </div>


          <div class="form-group">
              <label class="control-label col-sm-3" for="assignorgid">Assigned Organisation(s):</label>
              <div class="col-sm-9">
                <select class="form-control" id="assignorgid" name="assignorgid[]" multiple=""></select>
              </div>
          </div>



      </div>
      <div class="modal-footer">
           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           <button type="submit" class="btn btn-primary m-r-30 waves-effect">Submit</button>
      </div>

      </form>
    </div>
  </div>
</div>

<!-- new user modal end -->

<!-- Add new Org Modal -->
<div id="addNewOrg" class="modal fade informer-md-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close"  data-dismiss="modal">&times;</button>
        <h4 class="modal-title newOrg-title">Add New Organisation</h4>
        <h4 class="modal-title editOrg-title">Edit Organisation</h4>
      </div>
       <form class="form-horizontal" action="https://informer4smb.com.au/pla/addorgs.php" method="post" id="orgform" name="orgs" onsubmit="return validateorgform(document.getElementById('orgform'));">
            <input  type="hidden" name="format" value="json">
            <input  type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>" >
            <input  type="hidden" id="editorgid" name="orgid" value="" >

       <div class="modal-body">


          <div class="form-group">
            <label class="control-label col-sm-3" for="abn">ABN:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" id="abn" name="abn" value="" size="50" maxlength="15" placeholder="ABN">
            </div>
            <div class="col-sm-2"><button type="button" id="verifyAbn" class="btn btn-default waves-effect">Verify</button></div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-3" for="orgidnew">Organisation:</label>
              <div class="col-sm-9">

                <div id="oneOrgName">
                    <input type="text" class="form-control" id="orgname" name="orgname" value="" size="50" placeholder="Organisation Name">
                </div>

              </div>
            </div>


        <div class="form-group">
            <label class="control-label col-sm-3" for="compaddress">Organisation Address:</label>
            <div class="col-sm-9">
              <textarea type="text" class="form-control" id="compaddress" name="compaddress" rows="3" cols="50" placeholder="Enter Address"></textarea>
            </div>
          </div>


        <div class="form-group">
            <label class="control-label col-sm-3" for="orgmocompemailbile">Organisation Email:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="compemail" name="compemail"  placeholder="Enter Email">
              <span id="compemail-error"  class="bg-danger" style="display : none" >Invaid Email Address</span>
            </div>
          </div>


        <div class="form-group">
            <label class="control-label col-sm-3" for="compphone">Organisation Phone:</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="compphone" name="compphone" placeholder="Enter Phone">
            </div>
          </div>


      </div>
      <div class="modal-footer">

           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           <button type="submit" class="btn btn-primary m-r-30 waves-effect">Submit</button>
      </div>

      </form>
    </div>

  </div>
</div>
<!-- Modal End -->


	<!-- footer include -->
	<?php 
		include("inc/footer.inc.php"); 
	?>
	<!-- end footer -->
    <link href="https://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/css/bootstrap-multiselect.css" rel="stylesheet">
    <script src="https://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js"></script>

</body>
</html>
<?php
ob_end_flush;
?>
