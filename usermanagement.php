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
		
		<script>
			var userid = <?php echo $_SESSION['userid']; ?>
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
			if ($permissions['Reports']==0) {
				echo $GLOBALS['accesserrormsg']; 
				die;
			}
		?>
		
		<?php 
			$defaultTitle = 'User Management';
			include("inc/navbar.inc.php"); 
		?>


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
        	                <h2 class="step">Current User List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" data-target="#addNewUser">Add new User</button></h2>

                            <?php

                                $ch = curl_init();
                                curl_setopt($ch,CURLOPT_URL, "https://informer4smb.com.au/pla/user/user.php");
                                curl_setopt($ch, CURLOPT_POST, TRUE);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'list','format' => 'json'));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                $result = curl_exec($ch);
                                curl_close($ch);
                                json_decode($result, true);

                            ?>

                            <script type="text/javascript">
                                var userdata = <?php echo $result ?>

                                function simpleFunction(numb) {
                                    console.log(userdata);

                                }
                            </script>

                            <!-- user list -->
                            <table class="table user-management-list-table">
                               <tbody>

                                          <tr>
                                            <td class="middle">
                                              <div class="media">
                                                <div class="media-body">
                                                  <h4 class="media-heading">Contact 1</h4>
                                                  <address class="no-margin">contact1@sample.com</address>
                                                </div>
                                              </div>
                                            </td>
                                            <td width="100" class="middle">
                                              <div>
                                                <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit" onclick="simpleFunction(4)">
                                                  <i class="glyphicon glyphicon-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                  <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                              </div>
                                            </td>
                                          </tr>

                                           <tr>
                                              <td class="middle">
                                                <div class="media">
                                                  <div class="media-body">
                                                    <h4 class="media-heading">Contact 1</h4>
                                                    <address class="no-margin">contact1@sample.com</address>
                                                  </div>
                                                </div>
                                              </td>
                                              <td width="100" class="middle">
                                                <div>
                                                  <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                  </a>
                                                  <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                  </a>
                                                </div>
                                              </td>
                                            </tr>

                                          <tr>
                                           <td class="middle">
                                             <div class="media">
                                               <div class="media-body">
                                                 <h4 class="media-heading">Contact 1</h4>
                                                 <address class="no-margin">contact1@sample.com</address>
                                               </div>
                                             </div>
                                           </td>
                                           <td width="100" class="middle">
                                             <div>
                                               <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                 <i class="glyphicon glyphicon-edit"></i>
                                               </a>
                                               <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                 <i class="glyphicon glyphicon-trash"></i>
                                               </a>
                                             </div>
                                           </td>
                                         </tr>

                                          <tr>
                                           <td class="middle">
                                             <div class="media">
                                               <div class="media-body">
                                                 <h4 class="media-heading">Contact 1</h4>
                                                 <address class="no-margin">contact1@sample.com</address>
                                               </div>
                                             </div>
                                           </td>
                                           <td width="100" class="middle">
                                             <div>
                                               <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                 <i class="glyphicon glyphicon-edit"></i>
                                               </a>
                                               <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                 <i class="glyphicon glyphicon-trash"></i>
                                               </a>
                                             </div>
                                           </td>
                                         </tr>
                                </tbody>
                            </table>
                        </div>

                         <div id="organisationlist" class="tab-pane fade">
                                <h2 class="step">Current Organisation List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" data-target="#addNewUser">Add new Organisation</button></h2>

                         </div>


                        </div>
                     </div>
                 </div>
         </div>

	</div>
</div>
</section>

<!-- Modal -->
<div id="addNewUser" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add new user</h4>
      </div>
       <form class="form-horizontal" action="/action_page.php">
      <div class="modal-body">


          <div class="form-group">
            <label class="control-label col-sm-2" for="email">User ID:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="User ID">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Password:</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="pwd" placeholder="Enter password">
            </div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-2" for="email">First Name:</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="userid" placeholder="Enter First Name">
              </div>
            </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Last Name:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="Enter Last Name">
            </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Address:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="Enter Address">
            </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Email:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="Enter Email">
            </div>
          </div>


        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Mobile:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="Enter Mobile">
            </div>
          </div>


        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Phone:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userid" placeholder="Enter Phone">
            </div>
          </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="email">Organisation:</label>
            <div class="col-sm-10">
              <select class="form-control" id="orgid" name="orgid">
              <option value="15">ABC</option>
              <option value="1">Company B</option>
              <option value="3">def company</option>
              <option value="4">xyz company</option>
              </select>
            </div>
          </div>

          <div class="form-group">
              <label class="control-label col-sm-2" for="email">User Role:</label>
              <div class="col-sm-10">
                <select  class="form-control" id="role" name="role">
                <option value="8">Administrator</option>
                <option value="2">Junior</option>
                <option value="1">Senior</option>
                <option value="3">User</option>
                </select>
              </div>
            </div>


          <div class="form-group">
              <label class="control-label col-sm-2" for="email">Assigned Organisation:</label>
              <div class="col-sm-10">
                <select class="form-control" id="assignorgid[]" name="assignorgid[]" multiple="">
                <option value="15">ABC</option>
                <option value="1">Company B</option>
                <option value="3">def company</option>
                <option value="4">xyz company</option>

                </select>
              </div>
            </div>


      </div>
      <div class="modal-footer">
            <button type="submit" class="btn btn-default">Submit</button>
           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>

      </form>
    </div>

  </div>
</div>


	<!-- footer include -->
	<?php 
		include("inc/footer.inc.php"); 
	?>
	<!-- end footer -->

</body>
</html>
<?php
ob_end_flush;
?>
