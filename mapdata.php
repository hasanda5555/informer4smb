<?php
ob_start();
	
	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";
	require_once("../lib/PHPExcel/IOFactory.php");
	
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
        <script src="js/mapdata_function.js"></script>
		
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

	function mapall(tabs) {
	    
	var ele=document.getElementById('chkmapall');

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
		
		    $ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/userrolepermissions.php");
    	curl_setopt($ch, CURLOPT_POST, TRUE);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'rolepermission','roleid' => $role,'category'=>'Map Data'));
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
    	
			// CHECK PERMISSIONS
			if ($permissions['Map Data']==0) {
				echo $GLOBALS['accesserrormsg']; 
				die;
			}
	
			$defaultTitle = 'Map Data';
			include("inc/navbar.inc.php"); 
		?>


<section id="main">
<div id="content" class="">
	<div class="container">


<?php

$data=array();
$msg=false;
$year=array();
$company="";
$msgtext="";

if($_POST) {


    
    if(isset($_POST["getfile"])) {
        
        $company=$_POST['company'];
	    $source=$_POST['source'];
	    $filetype=$_POST['xlstype'];
	    
        $target_dir = "../uploads/$company/$source/$filetype/";
		
		$target_file = urldecode($_POST['filename']).".csv";
        if(getfile($target_dir,$target_file)){
            require_once("../includes/source/$filetype/$source.php");
        }
        else
            $msgtext="Cannot read data file";
        
    }

    if(isset($_POST["map"])) {
	    
	        $srctype=($_POST['srctype']!='pl') ? $_POST['srctype']."_" : "";
	    //echo "here";
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
				$month=$date['month'];
				
			    $buildsql="";
			    $buildsql2="";
			   
				for ($i=0;$i<$_POST['key'];$i++){
				    
					$value=($_POST['value'.$key.$i]=="") ? 0 : $_POST['value'.$key.$i];
					$cat1=($_POST['cat1'.$key.$i]=="") ? 100 : $_POST['cat1'.$key.$i];
					$cat2=($_POST['cat2'.$key.$i]=="") ? 0 : $_POST['cat2'.$key.$i];
					$cat3=($_POST['cat3'.$key.$i]=="") ? 0 : $_POST['cat3'.$key.$i];
					
					$mapid=mysqli_real_escape_string ($GLOBALS['link'],$_POST['mapid'.$key.$i]);
                	$company=mysqli_real_escape_string ($GLOBALS['link'],$_POST['company']);
                	$type=mysqli_real_escape_string ($GLOBALS['link'],$_POST['type'.$key.$i]);
                	$item=mysqli_real_escape_string ($GLOBALS['link'],$_POST['item'.$key.$i]);
                	$cat1=mysqli_real_escape_string ($GLOBALS['link'],$cat1);
                	$cat2=mysqli_real_escape_string ($GLOBALS['link'],$cat2);
                	$cat3=mysqli_real_escape_string ($GLOBALS['link'],$cat3);

            		$delsql="delete from ".$srctype."mapping where company='$company' and item='$item' and mapid=$mapid ";
            	
            	    if(!mysqli_query($GLOBALS['link'],$delsql)){ print 'MySQL Error: PLA ERROR0x9001' . mysqli_error($GLOBALS['link']); }

                $delsql="delete from ".$srctype."mapping_amount where company='$company' and year=$curyear and month=$month and item='$item' and mapid=$mapid ";
	
	      //  echo $delsql;
	                if(!mysqli_query($GLOBALS['link'],$delsql)){ print 'MySQL Error: PLA ERROR0x9004' . mysqli_error($GLOBALS['link']); } 

					$buildsql.="($mapid,'$company',$type,'$item',$cat1,$cat2,$cat3),";
				
				    $curyear=mysqli_real_escape_string ($GLOBALS['link'],$curyear);
	                $month=mysqli_real_escape_string ($GLOBALS['link'],$month);
	                $value=mysqli_real_escape_string ($GLOBALS['link'],$value);
	                
	                $value=number_format($value,2,'.','');
	                $cat1=($value==0) ? 0 : number_format((($cat1/100)*$value),2,'.','');
	                 $cat2=($value==0) ? 0 : number_format((($cat2/100)*$value),2,'.','');
	                 $cat3=($value==0) ? 0 : number_format((($cat3/100)*$value),2,'.','');

					$buildsql2.="($mapid,'$company',$curyear,$month,$type,'$item',$value,$cat1,$cat2,$cat3,'".$curyear."-".$month."-01'),";
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

  <div id="content" class="content-panel mapdata">
        <h2 class="step">Map Data</h2>
       <div id="frmmsg" class="row">

<?php 

if(sizeof($data)<=0) { 

?>


<div id="uploadfrm" class="col-md-12">
	<form name="frmmap" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form-horizontal">
<?php




        echo '<div class="form-group"> <label  class="control-label col-sm-2" >Company</label> ';
        if(isset($_POST['company'])) {
	        $company=$_POST['company'] ;

	        echo '<div class="col-sm-10">';
	        echo '<div class="selected-item">';
	        echo $company;
	        echo '</div>';
	        echo "<input type=\"hidden\" name=\"company\" id=\"company\" value=\"$company\" />";
	    } else {
	        echo '<div class="col-sm-10">';
	        echo "<select id=\"company\"  name=\"company\">";
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/org.php");
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'getorgassigned','userid'=>$_SESSION["userid"]));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$result = curl_exec($ch);
			
			echo $result;
			
			curl_close($ch);

			$xml = simplexml_load_string(trim($result)) ;

			$userresult=$xml->result;
			$authresult=$xml->message; 
			echo "<option value=\"\">(please select)</option> \r\n";
			foreach ($xml->orgs->org as $org) {
				$orgid=$org->orgid;
				$orgname=$org->orgname;
				echo "<option value=\"$orgname\">$orgname</option> \r\n";
			}
			
	        echo "</select>";
	    }
    
        echo "</div></div>";

        echo '<div class="form-group"> <label class="control-label col-sm-2">Source</label> ';
        if(isset($_POST['source'])) {
	        $source=$_POST['source'] ;
	        echo '<div class="col-sm-10">';
	        echo '<div class="selected-item">';
            echo strtoupper($source);
            echo '</div>';
	        echo "<input type=\"hidden\" name=\"source\"  id=\"source\" class='selected-item' value=\"$source\" />";
	    } else { 
        echo '<div class="col-sm-10">';
        echo "<select id=\"source\"  name=\"source\">
	   	<option value=\"myob\">MYOB</option>
	   	<option value=\"xero\" selected>XERO</option>
	   </select>";
	   }

	    echo "</div></div>";

       echo '<div class="form-group"> <label class="control-label col-sm-2">Type</label> ';

	    if(isset($_POST['xlstype'])) {
	        $xlstype=$_POST['xlstype'] ;


	        echo '<div class="col-sm-10">';
	        echo '<div class="selected-item">';
            echo strtoupper($xlstype);
            echo '</div>';
	        echo "<input type=\"hidden\" name=\"xlstype\" id=\"xlstype\" value=\"$xlstype\" />";
	    } else {
	       echo '<div class="col-sm-10">';
    	   echo "<select id=\"xlstype\"  name=\"xlstype\">
    	   	<option value=\"bs\">Balance Sheet</option>
    	   	<option value=\"pl\" selected>Profit & Loss</option>
    	   </select>";
	    }

	    echo "</div></div>";

	    if(isset($_POST['filename'])) {
	        $filename=$_POST['filename'] ;
	        echo "<div class='form-group'> <label class='control-label col-sm-2' >File </label> $filename ";
	        echo "</div>";
	    } 
	    elseif(isset($_POST['listfiles'])) {
	        $dir = "../uploads/$company/$source/$xlstype/";
	         echo "<div class='form-group'> <label class='control-label col-sm-2'>File </label><div class='col-sm-10'> <select id=\"filename\" name=\"filename\">";
	         echo "<option value=\"\">(please select)</option> \r\n";
            foreach (glob($dir."*.csv") as $file) {
                $filename=pathinfo($file, PATHINFO_FILENAME);
              	echo "<option value=\"".urlencode($filename)."\">$filename</option> \r\n";
            }
            echo "</select>";
            echo "</div></div>";

            echo "<div class='form-group'><label class='control-label col-sm-2'></label>";
            echo "<div class='col-sm-10'><input type=\"submit\" class='btn btn-primary' value=\"Load File\" name=\"getfile\">";
            echo "</div></div>";
	    }
	    else {
	        echo "<div class='form-group'><label class='control-label col-sm-2'></label>";
	        echo "<div class='col-sm-10'><input type=\"submit\" class='btn btn-primary'  value=\"List Files\" name=\"listfiles\">";
	        echo "</div></div>";
	    }
?>
	    
	</form>
</div>
<?php 
} 
if(sizeof($data)>0 ){
    
        $types=($_POST['xlstype']=='bs') ? getbstypes() : gettypes();
        $company=getcompany($data);
		$year=setyear($data);
		
		echo "<form name=\"frmmap\" action=\"mapdata.php\" method=\"post\" onsubmit='mapall(".json_encode($year)."); return checkform();' > \n";
		echo "<table> \n";
		echo "<tr><td colspan=\"6\">&nbsp;</td></tr> \n";
		echo "<tr><td style=\"padding-left:10px; font-size:16px; \" ><strong>Company: </strong></td><td style=\"font-size:16px; \" colspan=\"5\">&nbsp;";

		if($company=="")
			echo "<input style=\"width:400px;\" type=\"text\" name=\"company\" id=\"company\" value=\"\" size=\"50\" />";
		else
			echo "$company<input type=\"hidden\" name=\"company\" id=\"company\" value=\"$company\" />";

		echo "</td> \n</tr>\n</table>\n";
		
		echo "<p style=\"padding-top:10px; padding-left:10px;  font-size:16px;\"><input type=\"checkbox\" name=\"chkmapall\" id=\"chkmapall\" onclick='mapall(".json_encode($year).");' checked />&nbsp;Copy first month's mapping to all months</p>";
		
		echo "<ul class=\"tab\">\n";
		
		
		
		foreach ($year as $key=>$value) {
			  echo "<li><a href=\"javascript:void(0)\" class=\"tablinks\" onclick=\"openTab(event, '".trim($value)."')\" id=\"tab$key\">$value</a></li>\n";
	        }
		echo "</ul>\n";
				
		$csvdata=getcsvdata($data,$year);
		$oldkey="";
		$endtag=false;
	//	var_dump($csvdata);
		
		//echo $csvdata;
		foreach ($csvdata as $key=>$value) {
			if($oldkey!=$key){
				if($endtag){
					echo "</table>\n";
					echo "</div>\n";
				}
				echo "<div id=\"$key\" class=\"tabcontent\">\n";
				echo "<table id=\"pla\" class='table table-bordered table-striped informer-table'> \n";
				
				if($_POST['xlstype']=='pl'){
				    echo "<tr class='theadtr'><th>Item</th><th>Type</th><th>Value ($)</th><th>Make/Buy (%)</th><th>Obtain/Retain Customers (%)</th><th>Admin (%)</th><th>Mark for Review</th></tr> \n";
				}
				elseif($_POST['xlstype']=='bs'){
				    echo "<tr><th>Item</th><th>Type</th><th>Value ($)</th><th style=\"display:none;\">Assets (%)</th><th style=\"display:none;\">Liabilities (%)</th><th style=\"display:none;\">Equity (%)</th><th>Mark for Review</th></tr> \n";
				}
				
				$oldkey=$key;
				$endtag=true;
			}
			
			//print_r($value);
			 $i=0;
    	foreach ($value as $itemamount) {
    	    
                $key=preg_replace("/[^a-zA-Z0-9]+/", "", $key);
                $selectedval=0;
		        $cat1=100;
		        $cat2=0;
		        $cat3=0;

				foreach ($GLOBALS['labelarray'] as $label){
				    $matchstr=substr(trim(key($itemamount)),0,strlen($label)); 
                    if(strcasecmp($matchstr,$label)==0)
                        $selectedval=4;
                }
                
                $mapvalues=explode("|",getmapping($company,key($itemamount),$i));
                
			    if($mapvalues[0]!=""){
			        $selectedval=$mapvalues[0];
			        $cat1=$mapvalues[1];
			        $cat2=$mapvalues[2];
			        $cat3=$mapvalues[3];
			    }
				
				echo "<tr><td class=\"item\">".trim(key($itemamount))."<input type=\"hidden\" name=\"item$key$i\" id=\"item$key$i\" value=\"".trim(key($itemamount))."\" />";
				echo "<input type=\"hidden\" name=\"mapid$key$i\" id=\"mapid$key$i\" value=\"$i\" /></td>";
				echo "<td><select name=\"type$key$i\" id=\"type$key$i\">";
				$curgrp="";
				$onclick="";
				foreach ($types as $typeval){
					$type=explode("|",$typeval);
					
					if($type[0]==$selectedval)
					    $selected="selected";
					elseif ($type[2]==1 && $selectedval==0)
					    $selected="selected";
					else
					    $selected="";
					
					if($_POST['xlstype']=='bs')
					   $onclick="onclick=\"populatefield('cat".trim($type[3])."','$key$i');\"";	    
					
					
					echo "<option value=\"".$type[0]."\" $selected $onclick >".$type[1]."</option>";
				}
				
				$inputtype="text";
				$td="<td class=\"item\">";
				$slashtd="</td>";
				if($_POST['xlstype']=='bs'){
				    echo "</optgroup>";
				    $inputtype="hidden";
				    $td="";
				    $slashtd="";
				}
				
			   echo "</select></td>";
			   echo "<td class=\"item\"><input style=\"text-align:right;\" type=\"text\" name=\"value$key$i\" value=\"".$itemamount[key($itemamount)]."\" /></td>";
				echo "$td<input style=\"text-align:right;\" type=\"$inputtype\" name=\"cat1$key$i\" id=\"cat1$key$i\" value=\"$cat1\"  onclick=\"populatefield('cat1','$key$i');\" onchange=\"checkblank(this);\" / >$slashtd";
				echo "$td<input style=\"text-align:right;\"  type=\"$inputtype\" name=\"cat2$key$i\" id=\"cat2$key$i\" value=\"$cat2\" onclick=\"populatefield('cat2','$key$i');\" onchange=\"checkblank(this);\" />$slashtd";
				echo "$td<input style=\"text-align:right;\" type=\"$inputtype\" name=\"cat3$key$i\" id=\"cat3$key$i\" value=\"$cat3\" onclick=\"populatefield('cat3','$key$i');\" onchange=\"checkblank(this);\" />$slashtd";
				echo "<td class=\"item\"><input style=\"text-align:right;\" type=\"checkbox\" name=\"review$key$i\" value=\"1\" /></td>";
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
	
		echo "<input type=\"submit\" name=\"map\" id=\"map\" class='btn btn-primary' value=\"Map Fields\"  /> \n";
		echo "<input type=\"reset\" name=\"reset\" id=\"reset\" class='btn btn-default' value=\"Reset Form\" /> \n";
		echo "</p>";
		echo "</form> \n";
		echo "<p>&nbsp;</p>";
		
		echo "<script>\n document.getElementById('tab0').click();\n</script>\n";
		
	}
else 
    echo "<h1>$msgtext</h1>";
//if(sizeof($data)>0 && $company!=trim(getcompany($data)))
  //  $msgtext="Chosen file does not match selected organisation.";

	
?>








</div>

<!-- end upload content -->

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
