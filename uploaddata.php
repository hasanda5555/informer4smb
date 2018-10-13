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
			$defaultTitle = 'Upload';
			include("inc/navbar.inc.php"); 
		?>


<section id="main">
<div id="content" class="">
	<div class="container">
<?php 
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/userrolepermissions.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'rolepermission','roleid' => $role,'category'=>'Upload Data'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);
	curl_close($ch);
	//echo $result;
	
	$xml = simplexml_load_string(trim($result)) ;
		
	$userresult=$xml->result;
	$permissions=array();
	if($userresult=="ok") {
		foreach ($xml->permissions->permission as $element) {
			//$permissions[$element->category]=$element->access;
			$category=trim($element->category);
			$access=trim($element->access);
			$permissions[$category]=$access;
		}
		//print_r($permissions);
	}
/*
if ($permissions['Upload Data']==0) {
	echo $GLOBALS['accesserrormsg']; 
	die;
}
*/
?>
	
<div class="tab-content">
	<?php if($_POST && isset($_POST["upload"])) { ?>
	<div class=" content-panel">
	<?php } else { ?>
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
	<?php } ?>
		<div class="panel-body">
<?php if ($permissions['Upload Data']==0) { ?>
			<p>You do not have permission to upload data.</p>
<?php } else { ?>
<!-- upload content -->
	
<?php
require_once("../lib/PHPExcel/IOFactory.php");

$data=array();
$msg=false;
$upload=false;
$year=array();
$company="";
$msgtext="";

if($_POST) {
    
  
     // var_dump($_POST);
      

	if(isset($_POST["upload"])) {
	    
		$target_dir = "../uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		
		$upload=upload($target_file);
		
		//echo $upload;
		
		if($upload && isset($_POST['source']) && isset($_POST['xlstype'] ))
			require_once("../includes/source/".$_POST['xlstype']."/".$_POST['source'].".php");
			
	
			
			
	}
	elseif(isset($_POST["map"])) {
	    
	        $srctype=($_POST['srctype']!='pl') ? $_POST['srctype']."_" : "";
	   // echo "here";
    //die;
                 $year=unserialize($_POST['year']);
        foreach ($year as $yearvalue) {
		
			$key=preg_replace("/[^a-zA-Z0-9]+/", "", $yearvalue);
			
			$monthyear=array();
			
			if($_POST['srctype']=="bs"){
			    $monthyear=explode(" ",substr($yearvalue,3));
			    $curyear=$monthyear[1];
			}
			else{
			    $monthyear=explode("-",$yearvalue);
			    $curyear=$GLOBALS['yearprefix'].$monthyear[1];
			}
			//echo $srctype;  
			//print_r($monthyear);
		
			if($monthyear[0]!="YTD"){
				$date=date_parse($monthyear[0]);
			    $buildsql="";
			    $buildsql2="";
			   
				for ($i=0;$i<$_POST['key'];$i++){
				    
					$value=($_POST['value'.$key.$i]=="") ? 0 : $_POST['value'.$key.$i];
					$cat1=($_POST['cat1'.$key.$i]=="") ? 100 : $_POST['cat1'.$key.$i];
					$cat2=($_POST['cat2'.$key.$i]=="") ? 0 : $_POST['cat2'.$key.$i];
					$cat3=($_POST['cat3'.$key.$i]=="") ? 0 : $_POST['cat3'.$key.$i];
					
					$mapid=mysqli_real_escape_string ($GLOBALS['link'],$_POST['mapid']);
                	$company=mysqli_real_escape_string ($GLOBALS['link'],$_POST['company']);
                	$type=mysqli_real_escape_string ($GLOBALS['link'],$_POST['type'.$key.$i]);
                	$item=mysqli_real_escape_string ($GLOBALS['link'],$_POST['item'.$key.$i]);
                	$cat1=mysqli_real_escape_string ($GLOBALS['link'],$cat1);
                	$cat2=mysqli_real_escape_string ($GLOBALS['link'],$cat2);
                	$cat3=mysqli_real_escape_string ($GLOBALS['link'],$cat3);
                	$curyear=mysqli_real_escape_string ($GLOBALS['link'],$curyear);
	                $month=mysqli_real_escape_string ($GLOBALS['link'],$date['month']);
	                $value=mysqli_real_escape_string ($GLOBALS['link'],$value);
	                
	                $value=number_format($value,2,'.','');
	                $cat1=($value==0) ? 0 : number_format((($cat1/100)*$value),2,'.','');
	                 $cat2=($value==0) ? 0 : number_format((($cat2/100)*$value),2,'.','');
	                 $cat3=($value==0) ? 0 : number_format((($cat3/100)*$value),2,'.','');
	    
					//$msg=insertmap($_POST['mapid'],$_POST['company'],$_POST['type'.$key.$i],$_POST['item'.$key.$i],$cat1,$cat2,$cat3);
						$numrows=getmapping($company,$item,$mapid,$type);
	
                	if($numrows>0){
                		$delsql="delete from ".$srctype."mapping where company='$company' and item='$item' and typeid=$type and mapid=$mapid ";
                	
                	if(!mysqli_query($GLOBALS['link'],$delsql)){ print 'MySQL Error: PLA ERROR0x9001' . mysqli_error($GLOBALS['link']); }
                	}
                
                	
                $delsql="delete from ".$srctype."mapping_amount where company='$company' and year=$curyear and month=$month and item='$item' and typeid=$type ";
	
	        //echo $delsql;
	                if(!mysqli_query($GLOBALS['link'],$delsql)){ print 'MySQL Error: PLA ERROR0x9004' . mysqli_error($GLOBALS['link']); } 
	             
	               
	                
					$buildsql.="($mapid,'$company',$type,'$item',$cat1,$cat2,$cat3),";

					$buildsql2.="('$company',$curyear,$month,$type,'$item',$value,$cat1,$cat2,$cat3,'".$curyear."-".$month."-01'),";
				}
				
				$buildsql=substr($buildsql,0,-1);
				$buildsql2=substr($buildsql2,0,-1);
				
				//echo $buildsql."<br/>".$buildsql2;
			//	die;
				$msg=insertmap_amount($buildsql,$buildsql2,$srctype);
				
				if($msg)
						$msgtext="Data mapping successful.";
						
				if($_POST['srctype']=='pl')
				    calculatetotals($_POST['company'],$curyear,$month);
			}
			
		}
	}
}

?>

  <div id="content">
  
       <div id="frmmsg">
<?php 

if(!$upload && sizeof($data)<=0 && !$msg) { ?>
<div id="uploadfrm">
	<form name="frmupload" action="uploaddata.php?company=<?php echo urlencode($selectedCompany); ?>&month=<?php echo $selectedMonth; ?>&page=upload" method="post" enctype="multipart/form-data">
	    <h2 class="step">Step 1: Upload file</h2>
		
		<p class="left-content">Select CSV to upload:</p>
		<p class="right-content"><input type="file" name="fileToUpload" id="fileToUpload"></p>
		<p class="spacer">&nbsp;</p>
		
		<p class="left-content">Source:</p>
		<p class="right-content">
			<select id="source" name="source">
				<option value="myob">MYOB</option>
				<option value="xero" selected>XERO</option>
			</select>
		</p>
		<p class="spacer">&nbsp;</p>
		
		<p class="left-content">Type:</p>
		<p class="right-content">
			<select id="xlstype" name="xlstype">
				<option value="bs">Balance Sheet</option>
				<option value="pl" selected>Profit & Loss</option>
			</select>
		</p>
		<p class="spacer">&nbsp;</p>
		
		<button type="submit" name="upload" class="btn btn-primary pull-right m-r-30" >Upload CSV</button>
	    
	</form>
</div>
<?php 
} 
if(sizeof($data)>0){
    
        $types = ($_POST['xlstype'] == 'bs') ? getbstypes() : gettypes();
		
		$company=getcompany($data);
		$year=setyear($data);
?>

	<form name="frmmap" action="uploaddata.php?company=<?php echo $companyname; ?>&month=<?php echo $companymonth; ?>&page=upload" method="post" onsubmit="return checkform();">
		<h2 class='step'>Step 2: Map the data</h2>
		
		<p>Company: 
			<?php if($company=="") { ?>
				<input style="width:400px;" type="text" name="company" id="company" value="" size="50" />
			<?php } else { 
				echo $company;
			?>
				<input type="hidden" name="company" id="company" value="$company" />
			<?php } ?>
		</p>
		<p class="indented">
			<input type="checkbox" name="chkmapall" id="chkmapall" onclick='mapall(this, <?php echo json_encode($year); ?>);' /> Use same mapping for all months
		</p>
		<p class="spacer">&nbsp;</p>
		
		<ul class="tab">
			<?php 
				foreach ($year as $key=>$value) {
				  echo "<li><a href=\"javascript:void(0)\" class=\"tablinks btn btn-primary\" data-value='".trim($value)."' onclick-=\"openTab(event, '".trim($value)."')\" id=\"tab$key\">$value</a></li>\n";
				}
			?>
		</ul>
		
<?php
		
				
		$csvdata=getcsvdata($data,$year);
		$oldkey="";
		$endtag=false;
		foreach ($csvdata as $key=>$value) {
			if($oldkey!=$key){
				if($endtag){
					echo "</table>\n";
					echo "</div>\n";
				}
				
				$keyString = implode('-', explode(' ', $key));
				echo "<div id=\"$key\" data-period='$keyString' class=\"tabcontent hidden\">\n";
				echo "<table id=\"pla\"> \n";
				
				if($_POST['xlstype']=='pl'){
				    echo "<tr><th>Item</th><th>Type</th><th>Value ($)</th><th>Make/Buy (%)</th><th>Obtain/Retain <br/>Customers (%)</th><th>Admin (%)</th></tr> \n";
				}
				elseif($_POST['xlstype']=='bs'){
				    echo "<tr><th>Item</th><th>Type</th><th>Value ($)</th><th style=\"display:none;\">Assets (%)</th><th style=\"display:none;\">Liabilities (%)</th><th style=\"display:none;\">Equity (%)</th></tr> \n";
				}
				
				$oldkey=$key;
				$endtag=true;
			}
			
			//print_r($value);
			 $i=0;
    	foreach ($value as $itemamount) {
    	    
                $key=preg_replace("/[^a-zA-Z0-9]+/", "", $key);
			    
				//$mapvalues=getmapping($company,key($itemamount),$i);
				echo "<tr><td class=\"item\">".trim(key($itemamount))."<input type=\"hidden\" name=\"item$key$i\" id=\"item$key$i\" value=\"".trim(key($itemamount))."\" />";
				echo "<input type=\"hidden\" name=\"mapid\" id=\"mapid\" value=\"$i\" /></td>";
				echo "<td><select name=\"type$key$i\" id=\"type$key$i\">";
				$curgrp="";
				$onclick="";
				foreach ($types as $typeval){
					$type=explode("|",$typeval);
					$selected=($type[2]==1) ? "selected" : "";
					
					
					if($_POST['xlstype']=='bs'){
					    
					    $group=explode("-",$type[1]);
					    
					    if(trim($group[0])!=$curgrp){
					        
					        if($curgrp!=""){
					            echo "</optgroup>";
					            $curgrp=""; 
					        }
					        else{						      
					            echo "<optgroup label=\"".trim($group[0])."\"> \n";
					            $curgrp=trim($group[0]);
                            }
					    }
					    
					    $type[1]=trim($group[1]);
					    
				        $onclick="onclick=\"populatefield('cat".trim($type[3])."','$key$i');\"";	    
					}
					
					echo "<option value=\"".$type[0]."\" $selected $onclick >".$type[1]."</option>";
				}
				
				$inputtype="text";
				$td="<td class=\"item percent\">";
				$slashtd="</td>";
				if($_POST['xlstype']=='bs'){
				    echo "</optgroup>";
				    $inputtype="hidden";
				    $td="";
				    $slashtd="";
				}
				
			   echo "</select></td>";
			   echo "<td class=\"item amount\"><input style=\"text-align:right;\" type=\"text\" name=\"value$key$i\" value=\"".$itemamount[key($itemamount)]."\" /></td>";
				echo "$td<input style=\"text-align:right;\" type=\"$inputtype\" name=\"cat1$key$i\" id=\"cat1$key$i\" value=\"100\"  onclick=\"populatefield('cat1','$key$i');\" onchange=\"checkblank(this);\" / >$slashtd";
				echo "$td<input style=\"text-align:right;\"  type=\"$inputtype\" name=\"cat2$key$i\" id=\"cat2$key$i\" value=\"0\" onclick=\"populatefield('cat2','$key$i');\" onchange=\"checkblank(this);\" />$slashtd";
				echo "$td<input style=\"text-align:right;\" type=\"$inputtype\" name=\"cat3$key$i\" id=\"cat3$key$i\" value=\"0\" onclick=\"populatefield('cat3','$key$i');\" onchange=\"checkblank(this);\" />$slashtd";
				echo "</tr> \n";
				$i++;
			}  
		}
		
		if($endtag){
			echo "</table>\n";
			echo "</div>\n";
		}
		
		echo "<input type=\"hidden\" name=\"key\" id=\"key\" value=\"".$i."\" /> \n";
		echo "<input type=\"hidden\" name=\"srctype\" id=\"srctype\" value=\"".$_POST['xlstype']."\" /> \n";
		echo "<input type=\"hidden\" name=\"year\" id=\"year\" value=\"".htmlentities(serialize($year))."\" /> \n";
		echo "<p style=\"text-align:center; \">";
	
		echo "<button type=\"reset\" name=\"reset\" id=\"reset\" class='btn btn-secondary' >Reset Selections</button> \n";
		echo "<button type=\"submit\" name=\"map\" id=\"map\" class='btn btn-primary' >Map Fields</button> \n";
		echo "</p>";
		echo "</form> \n";
		echo "<p>&nbsp;</p>";
		
		echo "<script>\n document.getElementById('tab0').click();\n</script>\n";
		
	}	
	
	
	echo "<h1>$msgtext</h1>";

	/*if($msg){
		$reports="";
		
		echo "<p style=\"font-weight:bold; font-size:18px; padding-left:10px;\"><font style=\"color:green;\">Step 1. File Uploaded</font> <font style=\"font-size: 1.75rem;\">&rArr;</font> <font style=\"color:green;\">Step 2. Fields Mapped</font> <font style=\"font-size: 1.75rem;\">&rArr;</font> Step 3. Reports</p>";
		echo "<table> \n";
		echo "<tr><td style=\"padding-top:10px; padding-left:10px; padding-bottom:10px; font-size:16px;\"><strong>Company:</strong>&nbsp;".$_POST['company']."</td></tr> \n";
		
		echo "</table>\n";
		if(sizeof($year)>1){
	?>
		<p style="padding-left:10px;"><button onclick='document.getElementById("divrpt").innerHTML=""; showrpttabs(<?php echo  json_encode($year) ?>,"<?php echo $_POST['company']?>","1")'>Show Monthly Report</button>
		<button onclick='document.getElementById("divrpt").innerHTML=""; showrpttabs(<?php echo  json_encode($year) ?>,"<?php echo $_POST['company']?>","3")'>Show Quaterly Report</button></p>
		
	<?php	
		} else {
	?>	
		<ul class="tab">
			<li><a href="javascript:void(0)" class="tablinks" onclick="showrpt('<?php echo $_POST['company']?>','20','7');" id="tab0">July 2014 through June 2015</a></li>
		</ul>
	<?php		
		}
		
		//echo "<p><button onclick=\"window.open('charts.php?company=$company&amp;year=$year&amp;month=$month','_blank');\">Show Chart</button></p>";
		
		echo "<div id=\"divrpttabs\" ></div>";
	
		echo "<script>\n document.getElementById('tab0').click();\n</script>\n";
		
		echo "<div id=\"divrpt\" ></div>";
		
		
		
	} */

?>

<div style="clear:both"></div>
</div>
</div>

</div>

<!-- end upload content -->
<?php } ?>
		</div>
	</div>
</div>
</section>


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
