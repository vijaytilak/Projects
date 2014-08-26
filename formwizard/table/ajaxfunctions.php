<?php
	//MYSQL CONNECTION to vtigercrm db
	$con = mysql_connect('localhost', 'root', '');
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	mysql_select_db("vtigercrm", $con);		

	//ROUTER
	$quoteaction = $_REQUEST["quoteaction"];
	switch($quoteaction){
	  case "ajaxlookuparchdetail": ajaxlookuparchdetail();
		break; 
	  case "getAdditionalItemOptions": getAdditionalItemOptions();
		break; 
	  case "getmaxcurtainsize": getmaxcurtainsize();
		break;  		
	  case "storeStepArray": storeStepArray();
		break;
	  case "storeFrontArray": storeFrontArray();
		break;
	  case "storeRightArray": storeRightArray();
		break;
	  case "getFillTypeOptions": getFillTypeOptions();		  
		break;
	  case "getFillTypeOptionsSelect2": getFillTypeOptionsSelect2();		  
		break;
	  case "getFabricTypeOptions": getFabricTypeOptions();		  
		break;
	  case "getFabricTypeOptionsSelect2": getFabricTypeOptionsSelect2();		  
		break;
	  case "getBracketTypeOptions": getBracketTypeOptions();		  
		break;
	  case "getTimberTypeOptions": getTimberTypeOptions();		  
		break;
	  case "getBracketTypeOptionsSelect2": getBracketTypeOptionsSelect2();		  
		break;
	  case "onchangeCanopyFabricType": onchangeCanopyFabricType();		  
		break;
	  case "getcanopyfabrictypeSelect2": getcanopyfabrictypeSelect2();		  
		break;
	  case "getArchMaterialDropdown": getArchMaterialDropdown();		  
		break;
	  case "getFrontRailMaterialDropdown": getFrontRailMaterialDropdown();		  
		break;
	  case "getZipClearCosts": getZipClearCosts();		  
		break;
	  case "validateSectionTag": validateSectionTag();		  
		break;
	  case "autopopulatestep1": autopopulatestep1();		  
		break;
	  case "lookupsteparray": lookupsteparray();		  
		break;
		
		//Server Validations
	  case "servervalidatestep0": servervalidatestep0();		  
		break;
	  case "servervalidatestep1": servervalidatestep1();		  
		break;
	  case "servervalidatestep2": servervalidatestep2();		  
		break;
	  case "servervalidatestep3": servervalidatestep3();		  
		break;
	  case "servervalidatestep4": servervalidatestep4();		  
		break;
	  case "servervalidatestep5": servervalidatestep5();		  
		break;
		
		//Retrieve Onload data
	  case "retrieveonloadstep2": retrieveonloadstep2();		  
		break;
	  case "retrieveonloadstep3": retrieveonloadstep3();		  
		break;
	  case "retrieveonloadstep4": retrieveonloadstep4();		  
		break;
	  case "retrieveonloadstep5": retrieveonloadstep5();		  
		break;
	}
	//MYSQL CONNECTION Closing
	mysql_close($con);
	
	
/******************* Ajax Functions **************************/

	//FUNCTION - GET ARCH SPACING	
	function ajaxlookuparchdetail(){
		$length = $_POST['archlength'];
		
		$sql="SELECT max_arch_spacing,max_post_spacing_".$frontrailmaterial." AS max_post_spacing,centre_brace_qty,arch_material,max_bracket_spacing FROM fresco_archtable where `archlength`='".$length."'";
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
		  $max_arch_spacing=$row['max_arch_spacing'];
		  $max_post_spacing=$row['max_post_spacing'];
		  $centre_brace_qty=$row['centre_brace_qty'];
		  $arch_material=$row['arch_material'];
		  $arch_material=$row['max_bracket_spacing'];
		  
		  }
		  
		//storing values into response object
		$response_object['success'] = 'y';
		$response_object['max_arch_spacing'] = $max_arch_spacing; 
		$response_object['max_post_spacing'] = $max_post_spacing; 
		$response_object['centre_brace_qty'] = $centre_brace_qty; 
		$response_object['arch_material'] = $arch_material; 
		$response_object['max_bracket_spacing'] = $arch_material; 
			
		print json_encode($response_object);
	}
	
	//FUNCTION - GET ADDITIONAL ITEMS
	function getAdditionalItemOptions() {
		$return_arr = array();
		$row_array = array();

		$final_arr['Result']="OK";
				
		$result_arr = array();

		$sql="SELECT id,itemname FROM fresco_additionalitems";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['Value'] = $row['id'];
			$row_array['DisplayText'] = utf8_encode($row['itemname']);
			array_push($return_arr,$row_array);		
		  }
		
		$final_arr['Options']=$return_arr;
		echo json_encode($final_arr);
		
	}

	//FUNCTION - GET CURTAIN SIZE	
	function getmaxcurtainsize(){
		
		$sectionlength = $_POST['sectionlength'];
		//Static
		$sql="SELECT id FROM fresco_filltype where '".$sectionlength."' BETWEEN min_length AND max_length";
		console.log ($sql);
		$result = mysql_query($sql);
		
		//initializing
		$response_object['static_size'] = $response_object['glidetrack_size'] = $response_object['zip_size'] = '0';
		
		while($row = mysql_fetch_array($result))
		  {
		  if ($row['id']=='1')
		  $response_object['static_size'] = '1';
		  if ($row['id']=='2')
		  $response_object['glidetrack_size'] = '1';
		  if ($row['id']=='3')
		  $response_object['zip_size'] = '1';
 
		  }
		  
		//storing values into response object
		$response_object['success'] = 'y';
			
		print json_encode($response_object);
	}


	//FUNCTION - STORE STEP ARRAY
	function storeStepArray() {
		$obj = json_decode($_POST['data']);
		// Convert Object into Array and store it in session
				
		foreach ($obj as $key => $value)
		{
			$steparray[$key]['length'] = $value->length;
			$steparray[$key]['width'] = $value->width;
		}		
		
		//Step Array Name in the session should be unique depending on the quoteid	
		session_start();
		$quoteid = $_SESSION['quoteid'];
		$sessionsteparray = 'stepArray'.$quoteid;
		//If Session Already exist, then unset the session and store new stepArray
		if(isset($_SESSION[$sessionsteparray]))
		  unset($_SESSION[$sessionsteparray]);
		$_SESSION[$sessionsteparray]=$steparray;
		
		$response_object['success'] = 'y';
					
		print json_encode($response_object);
	}
	
	//FUNCTION - STORE FRONT ARRAY
	function storeFrontArray() {
		$obj = json_decode($_POST['data']);
		
		console.log($obj);

		//Step Array Name in the session should be unique depending on the quoteid	
		session_start();
		$quoteid = $_SESSION['quoteid'];
		$sessionsteparray = 'stepArray'.$quoteid;
		
		

		$steparray = $_SESSION[$sessionsteparray];
		// Convert Object into Array and store it in session
		foreach ($obj as $key => $value)
		{
			$steparray['frontarray'][$key]['plength'] = $value->plength;
			$steparray['frontarray'][$key]['pheight'] = $value->pheight;
			$steparray['frontarray'][$key]['supporttype'] = $value->supporttype;
			$steparray['frontarray'][$key]['postposition'] = $value->postposition;
			$steparray['frontarray'][$key]['brackets'] = $value->brackets;
			$steparray['frontarray'][$key]['bracketspacing'] = $value->bracketspacing;
			$steparray['frontarray'][$key]['sectiontype'] = $value->sectiontype;
			$steparray['frontarray'][$key]['filltype'] = $value->filltype;
			$steparray['frontarray'][$key]['fabrictype'] = $value->fabrictype;
			$steparray['frontarray'][$key]['fabriccolor'] = $value->fabriccolor;
			
			
		}		
		
		$steparray['frontarray']['nfposts'] = $obj->nfposts;
		$steparray['frontarray']['isStepped'] = $obj->isStepped;
		
		//If Session Already exist, then add and store new stepArray
		$_SESSION[$sessionsteparray] = $steparray;
		
		$response_object['success'] = 'y';
					
		print json_encode($response_object);
	}

	//FUNCTION - STORE RIGHT ARRAY
	function storeRightArray() {
		$obj = json_decode($_POST['data']);
		
		console.log($obj);

		//Step Array Name in the session should be unique depending on the quoteid	
		session_start();
		$quoteid = $_SESSION['quoteid'];
		$sessionsteparray = 'stepArray'.$quoteid;

		$steparray = $_SESSION[$sessionsteparray];
		// Convert Object into Array and store it in session
		foreach ($obj as $key => $value)
		{
			$steparray['rightarray'][$key]['plength'] = $value->plength;
			$steparray['rightarray'][$key]['pheight'] = $value->pheight;
			$steparray['rightarray'][$key]['supporttype'] = $value->supporttype;
			$steparray['rightarray'][$key]['postposition'] = $value->postposition;
			$steparray['rightarray'][$key]['brackets'] = $value->brackets;
			$steparray['rightarray'][$key]['bracketspacing'] = $value->bracketspacing;
			$steparray['rightarray'][$key]['sectiontype'] = $value->sectiontype;
			$steparray['rightarray'][$key]['filltype'] = $value->filltype;
			$steparray['rightarray'][$key]['fabrictype'] = $value->fabrictype;
			$steparray['rightarray'][$key]['fabriccolor'] = $value->fabriccolor;
			
			
		}		
		
		$steparray['rightarray']['nrposts'] = $obj->nrposts;
		$steparray['rightarray']['suffit'] = $obj->suffit;
		
		//If Session Already exist, then add and store new stepArray
		$_SESSION[$sessionsteparray] = $steparray;
		
		$response_object['success'] = 'y';
					
		print json_encode($response_object);
	}


	//FUNCTION - GET FILL TYPE	
	function getFillTypeOptions() {
		$searchid = $_GET['sectiontype'];
		$return_arr =array();
		$result_arr = array();
		
		if($searchid=='1')
		{
			$sql="SELECT id,filltype FROM fresco_filltype";		
			$result = mysql_query($sql);
			
			while($row = mysql_fetch_array($result))
			  {
				$row_array['Value'] = $row['id'];
				$row_array['DisplayText'] = utf8_encode($row['filltype']);
				array_push($return_arr,$row_array);		
			  }
			
		} else { 
			$row_array['Value'] = '0';
			$row_array['DisplayText'] = utf8_encode('None');
			array_push($return_arr,$row_array);	
		}
		
					
		$result_arr['Result'] = "OK";
		$result_arr['Options'] = $return_arr;
		echo json_encode($result_arr);
	}
	
	//FUNCTION - GET FILL TYPE for select2	
	function getFillTypeOptionsSelect2() {
		$return_arr =array();
		
		$sql="SELECT id,filltype FROM fresco_filltype";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['id'] = $row['id'];
			$row_array['text'] = utf8_encode($row['filltype']);
			array_push($return_arr,$row_array);		
		  }
		
		echo json_encode($return_arr);
	}

	//FUNCTION - GET FABRIC TYPE	
	function getFabricTypeOptions() {
		$searchid = $_GET['filltype'];
		$return_arr = array();
		$result_arr = array();

		$sql="SELECT id,fabrictype FROM fresco_fabrictype WHERE filltypeid=".$searchid;		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['Value'] = $row['id'];
			$row_array['DisplayText'] = utf8_encode($row['fabrictype']);
			array_push($return_arr,$row_array);		
		  }

		$result_arr['Result'] = "OK";
		$result_arr['Options'] = $return_arr;
		echo json_encode($result_arr);
	}

	//FUNCTION - GET FABRIC TYPE	
	function getFabricTypeOptionsSelect2() {
		$searchid = $_GET['searchid'];
		$return_arr =array();
		
		$sql="SELECT id,fabrictype FROM fresco_fabrictype WHERE filltypeid=".$searchid;		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['id'] = $row['id'];
			$row_array['text'] = utf8_encode($row['fabrictype']);
			array_push($return_arr,$row_array);		
		  }
		
		echo json_encode($return_arr);
	}
	
	//FUNCTION - GET BRACKET TYPE	
	function getBracketTypeOptions() {
		$return_arr = array();
		$result_arr = array();

		$sql="SELECT id,connector as brackettype FROM fresco_costing_connectors WHERE category = 'bracket'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['Value'] = $row['id'];
			$row_array['DisplayText'] = utf8_encode($row['brackettype']);
			array_push($return_arr,$row_array);		
		  }

		echo json_encode($return_arr);
	}
	
	//FUNCTION - GET TIMBER TYPE	
	function getTimberTypeOptions() {
		$return_arr = array();
		$result_arr = array();

		$sql="SELECT id,timbertype FROM fresco_timbertype";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['Value'] = $row['id'];
			$row_array['DisplayText'] = utf8_encode($row['timbertype']);
			array_push($return_arr,$row_array);		
		  }

		echo json_encode($return_arr);
	}
	
	
	//FUNCTION - GET CANOPY FABRIC TYPE	
	function getcanopyfabrictypeSelect2() {
		$return_arr =array();
		
		$sql="SELECT id, fabrictype FROM fresco_canopyfabrictype";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['id'] = $row['id'];
			$row_array['text'] = utf8_encode($row['fabrictype']);
			array_push($return_arr,$row_array);		
		  }
		
		echo json_encode($return_arr);
	}
	
	//FUNCTION - GET ARCH MATERIAL	
	function getArchMaterialDropdown() {
		$return_arr =array();
		
		$sql="SELECT id,archmaterial FROM fresco_costing_archmaterial";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['id'] = $row['id'];
			$row_array['text'] = utf8_encode($row['archmaterial']);
			array_push($return_arr,$row_array);		
		  }
		
		echo json_encode($return_arr);
	}
	
	//FUNCTION - GET FRONT RAIL MATERIAL	
	function getFrontRailMaterialDropdown() {
		$return_arr =array();
		$temp_return_arr = array();
		$row_array = array();
		
		$sql="SELECT DISTINCT(archmaterialid) FROM fresco_costing_arch_siderail";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$key = $row['archmaterialid'];
			
			$sql1="SELECT id,frontrailmaterial FROM fresco_costing_arch_siderail WHERE archmaterialid =".$key;
			$result1 = mysql_query($sql1);
			$temp_return_arr = array();
			while($row1 = mysql_fetch_array($result1))
			  {
				$row_array['id'] = $row1['id'];
				$row_array['text'] = utf8_encode($row1['frontrailmaterial']);
				array_push($temp_return_arr,$row_array);		
			  }
			array_push($return_arr,$temp_return_arr);
		  }
		
		echo json_encode($return_arr);
	}

	//FUNCTION - GET ZIP CURTAIN CLEAR COSTS	
	function getZipClearCosts() {
		$width = $_GET['width'];
		$height = $_GET['height'];
		
		$return_arr =array();
		
		//Lookup labour cost for Zip Clear Curtains
		$sql="SELECT * FROM fresco_costing_curtain_labour";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['CutclearFixed'] = $row['CutclearFixed'];
			$row_array['text'] = utf8_encode($row['filltype']);
			array_push($return_arr,$row_array);		
		  }

		$CutClearFixedTotal = $CutClearFixed;
		echo json_encode($return_arr);
	}	
	
	//FUNCTION - VALIDATE SECTION TAG DUPLICATE	
	function validateSectionTag() {
	
		$validateValue=$_REQUEST['fieldValue'];
		$validateId=$_REQUEST['fieldId'];
		
		$quoteid = $_REQUEST['Edit-quoteid'];
		$pheight = $_REQUEST['Edit-pheight'];
		if($pheight=="")
		{$event="create";}
		else
		{$event="edit";}
		
		$validateError= "This Tag is already assigned";
		$validateSuccess= "Tag is available";
		
		/* RETURN VALUE */
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;
		
		//Lookup duplicate entries for section tag
		if($event=="create")
		{
		$sql="SELECT id FROM fresco_costing_frontarray WHERE sectiontag = '".$validateValue."' AND quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		
		//Set the flag if duplicate exist
		while($row = mysql_fetch_array($result))
		  {
			If($row['id'])
			$duplicate = "yes";
		  }
		
		}
		//Return Array with success/failure
		if($duplicate <>"yes"){		// validate??
			$arrayToJs[1] = true;			// RETURN TRUE
			echo json_encode($arrayToJs);			// RETURN ARRAY WITH success
		}else{
			for($x=0;$x<1000000;$x++){
				if($x == 990000){
					$arrayToJs[1] = false;
					
					echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
				}
			}
		}

	}

	//FUNCTION - CALCULATE STEP 0	
	function servervalidatestep0() {

		$quoteid = $_REQUEST['quoteid'];
		$noofsteps = 0;
		$return_array =array();
		$step_array =array();
		$row_array =array();
		$return_array['nooffrontposts'] ="";
		
		//Save inputs to db
		//Check if fields needs to be updated or Inserted into database
		$sql1="SELECT quoteid,freestanding FROM fresco_costing_quotetable WHERE quoteid = '" . $quoteid . "'";		
		$result = mysql_query($sql1);
		
		$row = mysql_fetch_array($result);
			if($row['quoteid']=='') 
			{
				//INSERT if record is not there in db
				$sql2="";
				$sql2="INSERT INTO fresco_costing_quotetable( quoteid, freestanding, canopytype ) VALUES('" . $_POST["quoteid"]. "','" . $_POST["freestanding"]. "','" .$_POST["canopytype"]. "')";		
				$result2 = mysql_query($sql2);
			} else {
				//UPDATE if record already exist in db
				$sql2="UPDATE fresco_costing_quotetable SET freestanding = '".$_POST["freestanding"]."',canopytype = '".$_POST["canopytype"]."'  WHERE quoteid = '" . $quoteid."'";		
				$result2 = mysql_query($sql2);
			}

		
		//Autopopulate fields for the next step from db if it exists
		$sql1="SELECT nooffrontposts, archmaterial, leftsuffitwidth, rightsuffitwidth, frontrailmaterial, backrailmaterial FROM fresco_costing_quotetable WHERE quoteid = '" . $quoteid . "'";		
		$result = mysql_query($sql1);
		
		$row = mysql_fetch_array($result);
			if($row['nooffrontposts'])
			{
				$return_array['nooffrontposts'] = $row['nooffrontposts'];
				$return_array['leftsuffitwidth'] = $row['leftsuffitwidth'];
				$return_array['rightsuffitwidth'] = $row['rightsuffitwidth'];
				$return_array['archmaterial'] = $row['archmaterial'];
				$return_array['frontrailmaterial'] = $row['frontrailmaterial'];
				$return_array['backrailmaterial'] = $row['backrailmaterial'];
			}
			
		
			
		//Lookup length and width from StepArray Table
		$sql="SELECT length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		$archlength =0;

		while($row = mysql_fetch_array($result))
		  {
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			if ($row['width']>$archlength)
			$archlength = $row['width'];
			array_push($return_array,$row_array);	
			
			$noofsteps++;
		  }
		  
		//Lookup Arch material from Arch Table and auto populate default arch material and side rail material dropdown
		$sql1="SELECT arch_material FROM fresco_archtable WHERE archlength = '".$archlength."'";		
		$result1 = mysql_query($sql1);
		$row = mysql_fetch_array($result1);
		
		$sql2="SELECT id FROM fresco_costing_archmaterial WHERE archmaterial = '".$row['arch_material']."'";		
		$result2 = mysql_query($sql2);
		$row = mysql_fetch_array($result2);
		$return_array['archmaterialid'] = $row['id'];

		$return_array['noofsteps'] = $noofsteps;
		$return_array['msg'] = 'success';
		$return_array['resultstep'] = '0';
		
		echo json_encode($return_array);
	}
		
	//FUNCTION - CALCULATE STEP 1	
	function servervalidatestep1() {
		
		//Store Step1 Fields in Database
		$quoteid = $_REQUEST['quoteid'];
		$nooffrontposts = $_REQUEST['nooffrontposts'];
		$archmaterial = $_REQUEST['archmaterial'];
		$frontrailmaterial = $_REQUEST['frontrailmaterial'];
		$backrailmaterial = $_REQUEST['backrailmaterial'];
		$leftsuffitwidth = $_REQUEST['leftsuffitwidth'];
		$rightsuffitwidth = $_REQUEST['rightsuffitwidth'];
		
		//Check if fields needs to be updated or Inserted into database
		$sql1="SELECT quoteid,nooffrontposts FROM fresco_costing_quotetable WHERE quoteid = '" . $quoteid . "'";		
		$result = mysql_query($sql1);
		
		$row = mysql_fetch_array($result);
			if($row['quoteid']=='') 
			{
				//INSERT if record is not there in db
				$sql2="";
				$sql2="INSERT INTO fresco_costing_quotetable( quoteid, nooffrontposts, archmaterial, frontrailmaterial, backrailmaterial,leftsuffitwidth,rightsuffitwidth ) VALUES('" . $_POST["quoteid"]. "','" . $_POST["nooffrontposts"]. "','" .$_POST["archmaterial"]. "','" .$_POST["frontrailmaterial"]. "','" .$_POST["backrailmaterial"]. "','" .$_POST["leftsuffitwidth"]. "','" .$_POST["rightsuffitwidth"]. "')";		
				$result2 = mysql_query($sql2);
			} else {
				//UPDATE if record already exist in db
				$sql2="UPDATE fresco_costing_quotetable SET nooffrontposts = '".$nooffrontposts."',archmaterial = '".$archmaterial."',frontrailmaterial = '".$frontrailmaterial."',backrailmaterial = '".$backrailmaterial."',leftsuffitwidth = '".$leftsuffitwidth."',rightsuffitwidth = '".$rightsuffitwidth."' WHERE quoteid = '" . $quoteid."'";		
				$result2 = mysql_query($sql2);
			}
		
		//Load values for Step 2 from database and pass it on
		
		$lookupfabrictype = $_REQUEST['lookupfabrictype'];

		//Initialization		
		$step_array =array();
		$canopylength = 0;
		$canopywidth = 0;
		$noofsteps = 0;
		if ( $lookupfabrictype =='')
		{
			//lookup canopy fabric type from database
			$sql1="SELECT canopyfabrictype FROM fresco_costing_quotetable WHERE quoteid = '" . $quoteid . "'";		
			$result = mysql_query($sql1);
			$row = mysql_fetch_array($result);
			if($row['canopyfabrictype'] == '')
			$defaulcanopyfabrictype = '1';
			else
			$defaulcanopyfabrictype = $row['canopyfabrictype'];
		}
		else
		$defaulcanopyfabrictype = $lookupfabrictype;


		//Lookup length and width from StepArray Table
		$sql="SELECT length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		

		while($row = mysql_fetch_array($result))
		  {
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			array_push($step_array,$row_array);	
			
			$noofsteps++;
			
			$canopylength += $row['length'];
			if($row['width'] > $canopywidth)
			$canopywidth = $row['width'];
		  }
		  
		foreach ($step_array as $key => $step)
		{
			//find out the row for archtable lookup for each step
			$lookuprow = ceil($step['width']/250)*250;
			if ($step['width']<750) 
			$lookuprow = 750;
			else if ($step['width']>8000)
			$lookuprow = 8000;
			
			//Arch Table Lookup
			$sql="SELECT max_arch_spacing,centre_brace_qty,arch_material FROM fresco_archtable WHERE `archlength`='".$lookuprow."'";
			$result = mysql_query($sql);
			
			while($row = mysql_fetch_array($result))
			{
				$step_array[$key]['max_arch_spacing']=$row['max_arch_spacing'];
				$step_array[$key]['reccentrebraceqty']=$row['centre_brace_qty'];
				$step_array[$key]['arch_material']=$row['arch_material'];
			}
		$step_array[$key]['recarchspaces'] = ceil($canopylength/$step_array[$key]['max_arch_spacing']);
		$step_array[$key]['recarchspacing'] = ceil($canopylength/$step_array[$key]['recarchspaces']);
		$step_array[$key]['recarchqty'] = $step_array[$key]['recarchspaces']+1;
		
		//Rollwidth Lookup
		$sql3="SELECT rollwidth FROM fresco_canopyfabrictype WHERE `id`='".$defaulcanopyfabrictype."'";
		$result3 = mysql_query($sql3);
		
		while($row = mysql_fetch_array($result3))
		{
			$defaultrollwidth=$row['rollwidth'];
		}
		
		if ($step_array[$key]['recarchspacing'] > $defaultrollwidth )
		{
			$step_array[$key]['recarchspacing'] = $defaultrollwidth;
			$step_array[$key]['recarchqty'] = ceil($canopylength/$step_array[$key]['recarchspacing']);
		}
		
		if ($step['length']>=7000) 
		$step_array[$key]['recarchbraces'] = 'Yes';
		else
		$step_array[$key]['recarchbraces'] = 'No';
		}

		$step_array['noofsteps'] = $noofsteps;
		$step_array['sql1'] = $sql1;
		$step_array['sql2'] = $sql2;
		$step_array['msg'] = 'success';
		$step_array['resultstep'] = '1';
		
		echo json_encode($step_array);
	}

	//FUNCTION - CALCULATE STEP 2	
	function servervalidatestep2() {
		
		//Store Step2 Fields in Database
		$quoteid = $_REQUEST['quoteid'];
		$noofsteps = $_REQUEST['noofsteps'];
		$canopyfabrictype = $_REQUEST['usecanopyfabrictype'];
		
		//Delete from database
		$sql1="DELETE FROM `fresco_costing_archarray` WHERE `quoteid` = '" . $quoteid . "'";		
		$result = mysql_query($sql1);

		
		for ($stepno=0; $stepno < $noofsteps; $stepno++)
		{
			//INSERT if record into db
			$sql="INSERT INTO `fresco_costing_archarray`(`quoteid`, `stepno`, `archqty`, `archspacing`, `archbraces`, `centrebraceqty`) VALUES ('" . $_POST["quoteid"]. "','" . $stepno. "','" . $_POST["archqty".$stepno]. "','" . $_POST["archspacing".$stepno]. "','" . $_POST["usearchbraces".$stepno]. "','" . $_POST["centrebraceqty".$stepno]. "')";		
			$result = mysql_query($sql);
				
		}
		
		//Insert canopy fabric type into quote table
		//UPDATE if record already exist in db
		$sql2="UPDATE fresco_costing_quotetable SET canopyfabrictype = '".$canopyfabrictype."' WHERE quoteid = '" . $quoteid."'";		
		$result2 = mysql_query($sql2);

		$step_array['msg'] = 'success';
		$step_array['resultstep'] = '2';
		
		echo json_encode($step_array);

	}

	//FUNCTION - CALCULATE STEP 3	
	function servervalidatestep3() {
		
		//Initialization		
		$step_array =array();
		$canopylength = 0;
		$canopywidth = 0;
		$quoteid = $_REQUEST['quoteid'];
		$noofsteps = 0;

		//Lookup StepArray Table for no of steps
		$sql="SELECT length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			array_push($step_array,$row_array);	
			
			$noofsteps++;
			
			$canopylength += $row['length'];
			if($row['width'] > $canopywidth)
			$canopywidth = $row['width'];
		  }
		
		//Delete from database
		$sql1="DELETE FROM `fresco_costing_endfillarray` WHERE `quoteid` = '" . $quoteid . "'";		
		$result = mysql_query($sql1);
$step_array['steps'] = $noofsteps;
		
		for ($stepno=0; $stepno < $noofsteps; $stepno++)
		{
			//INSERT record into db
			$sql="INSERT INTO `fresco_costing_endfillarray`(`quoteid`,`stepno`,`endfillrequiredleft`,`endfillrequiredmiddle`,`endfillrequiredright`,`endfilllengthleft`,`endfilllengthmiddle`,`endfilllengthright`,`endfillheightleft`,`endfillheightmiddle`,`endfillheightright`,`endfillfabricleft`,`endfillfabricmiddle`,`endfillfabricright`,`endfilltypeleft`,`endfilltypemiddle`,`endfilltyperight`,`endfillstubleft`,`endfillstubmiddle`,`endfillstubright`) VALUES ('" . $_POST["quoteid"]. "','" . $stepno. "','" . $_POST["endfillrequiredleft".$stepno]. "','" . $_POST["endfillrequiredmiddle".$stepno]. "','" . $_POST["endfillrequiredright".$stepno]. "','" . $_POST["endfilllengthleft".$stepno]. "','" . $_POST["endfilllengthmiddle".$stepno]. "','" . $_POST["endfilllengthright".$stepno]. "','" . $_POST["endfillheightleft".$stepno]. "','" . $_POST["endfillheightmiddle".$stepno]. "','" . $_POST["endfillheightright".$stepno]. "','" . $_POST["endfillfabricleft".$stepno]. "','" . $_POST["endfillfabricmiddle".$stepno]. "','" . $_POST["endfillfabricright".$stepno]. "','" . $_POST["endfilltypeleft".$stepno]. "','" . $_POST["endfilltypemiddle".$stepno]. "','" . $_POST["endfilltyperight".$stepno]. "','" . $_POST["endfillstubleft".$stepno]. "','" . $_POST["endfillstubmiddle".$stepno]. "','" . $_POST["endfillstubright".$stepno]. "')";		
			$result = mysql_query($sql);
			$step_array['sql'] = $sql;	
		}
		
		$step_array['msg'] = 'success';
		$step_array['resultstep'] = '3';
		
		echo json_encode($step_array);

	}

	//FUNCTION - CALCULATE STEP 4	
	function servervalidatestep4() {
		
		//Initialization		
		$step_array =array();
		$canopylength = 0;
		$canopywidth = 0;
		$quoteid = $_REQUEST['quoteid'];
		$noofsteps = 0;

		//Lookup StepArray Table for no of steps
		$sql="SELECT length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			array_push($step_array,$row_array);	
			
			$noofsteps++;
			
			$canopylength += $row['length'];
			if($row['width'] > $canopywidth)
			$canopywidth = $row['width'];
		  }
		
		//Delete from database
		$sql1="DELETE FROM `fresco_costing_additionalconfigarray` WHERE `quoteid` = '" . $quoteid . "'";		
		$result = mysql_query($sql1);
		$step_array['steps'] = $noofsteps;

//Build the Insert Query based on the values in Query String

			//INSERT Front side record into db
			$sql="INSERT INTO `fresco_costing_additionalconfigarray`(`quoteid`,`side`,`scolloped`,`gutter`,`downpipe`,`timber`) VALUES ('" . $_POST["quoteid"]. "','Front','" . $_POST["scallopedfront"]. "','" . $_POST["gutterfront"]. "','" . $_POST["downpipefront"]. "','" . $_POST["timberfront"]. "')";
			$result = mysql_query($sql);

		for ($stepno=0; $stepno < $noofsteps; $stepno++)
		{
			//INSERT Back Side steps into db
			$sql="INSERT INTO `fresco_costing_additionalconfigarray`(`quoteid`,`side`,`scolloped`,`gutter`,`downpipe`,`timber`) VALUES ('" . $_POST["quoteid"]. "','sidebackstep" . $stepno. "','" . $_POST["scallopedbackstep".$stepno]. "','" . $_POST["gutterbackstep".$stepno]. "','" . $_POST["downpipebackstep".$stepno]. "','" . $_POST["timberbackstep".$stepno]. "')";		
			$result = mysql_query($sql);
		}
		
		$step_array['msg'] = 'success';
		$step_array['resultstep'] = '4';
		
		echo json_encode($step_array);

	}

	//FUNCTION - CALCULATE STEP 5	
	function servervalidatestep5() {
		
		//Initialization		
		$step_array =array();
		$quoteid = $_REQUEST['quoteid'];
		
		//Delete from database
		$sql1="DELETE FROM `fresco_costing_otherconfigarray` WHERE `quoteid` = '" . $quoteid . "'";		
		$result = mysql_query($sql1);

//Build the Insert Query based on the values in Query String

			//INSERT Front side record into db
			$sql="INSERT INTO `fresco_costing_otherconfigarray`(`quoteid`,`adf`,`wq`,`fio`,`buildingconsent`,`gst`,`hotdipgalv`,`epoxy`,`polyester`,`abcite`,`leanto`) VALUES ('" . $_POST["quoteid"]. "','" . $_POST["adf"]. "','" . $_POST["wq"]. "','" . $_POST["fio"]. "','" . $_POST["buildingconsent"]. "','" . $_POST["gst"]. "','" . $_POST["hotdipgalv"]. "','" . $_POST["epoxy"]. "','" . $_POST["polyester"]. "','" . $_POST["abcite"]. "','" . $_POST["leanto"]. "')";
			$result = mysql_query($sql);

		
		$step_array['msg'] = 'success';
		$step_array['resultstep'] = '5';
		
		echo json_encode($step_array);

	}

	
	//FUNCTION - AUTOPOPULATE STEP 1	
	function autopopulatestep1() {
		
		//Posted Params
		$quoteid = $_REQUEST['quoteid'];
		$freestanding = $_REQUEST['freestanding'];
		$canopytype = $_REQUEST['canopytype'];
		$frontrailmaterial = $_REQUEST['frontrailmaterial'];
		$leftsuffitwidth = $_REQUEST['leftsuffitwidth'];
		$rightsuffitwidth = $_REQUEST['rightsuffitwidth'];
		
		//Delete from database
		$sql1="DELETE FROM fresco_costing_frontarray WHERE quoteid = '" . $quoteid . "'";		
		$result = mysql_query($sql1);

		//Initialization		
		$step_array =array();
		$canopylength = 0;
		$canopywidth = 0;
		$postspacing = 0;
		$noofsteps = 0;
		
		//Lookup length and width from StepArray Table
		$sql="SELECT length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			array_push($step_array,$row_array);	
			
			$noofsteps++;
			
			$canopylength += $row['length'];
			if($row['width'] > $canopywidth)
			$canopywidth = $row['width'];
		  }
		  
		  
		//Determine max length for openingtypes
		if($_REQUEST['openingtype'] =='0')
		{
			$sql3="SELECT `max_length` FROM `fresco_filltype` WHERE `id`=".$_REQUEST['openingtypeselect'];
			$result3 = mysql_query($sql3);
			$row = mysql_fetch_array($result3);
			$maxleftopeninglength = $maxfrontopeninglength = $maxrightopeninglength = $maxrearopeninglength = $row['max_length'];
			$filltypeleft=$filltypefront=$filltyperight=$filltyperear=$_REQUEST['openingtypeselect'];
			$fabrictypeleft=$fabrictypefront=$fabrictyperight=$fabrictyperear=$_REQUEST['openingtypeselectfabric'];
		} else
		{
			//Left
			$sql3="SELECT `max_length` FROM `fresco_filltype` WHERE `id`=".$_REQUEST['openingtypeleft'];
			$result3 = mysql_query($sql3);
			$row = mysql_fetch_array($result3);
			$maxleftopeninglength = $row['max_length'];
			$filltypeleft=$_REQUEST['openingtypeleft'];
			$fabrictypeleft=$_REQUEST['openingtypeleftfabric'];
			
			//Front
			$sql3="SELECT `max_length` FROM `fresco_filltype` WHERE `id`=".$_REQUEST['openingtypefront'];
			$result3 = mysql_query($sql3);
			$row = mysql_fetch_array($result3);
			$maxfrontopeninglength = $row['max_length'];
			$filltypefront=$_REQUEST['openingtypefront'];
			$fabrictypefront=$_REQUEST['openingtypefrontfabric'];

			//Right
			$sql3="SELECT `max_length` FROM `fresco_filltype` WHERE `id`=".$_REQUEST['openingtyperight'];
			$result3 = mysql_query($sql3);
			$row = mysql_fetch_array($result3);
			$maxrightopeninglength = $row['max_length'];
			$filltyperight=$_REQUEST['openingtyperight'];
			$fabrictyperight=$_REQUEST['openingtyperightfabric'];

			//Rear
			$sql3="SELECT `max_length` FROM `fresco_filltype` WHERE `id`=".$_REQUEST['openingtyperear'];
			$result3 = mysql_query($sql3);
			$row = mysql_fetch_array($result3);
			$maxrearopeninglength = $row['max_length'];
			$filltyperear=$_REQUEST['openingtyperear'];
			$fabrictyperear=$_REQUEST['openingtyperearfabric'];
		}
		
		
		foreach ($step_array as $key => $step)
		{
			//find out the row for archtable lookup for each step
			$lookuprow = ceil($step['width']/250)*250;
			if ($step['width']<750) 
			$lookuprow = 750;
			else if ($step['width']>8000)
			$lookuprow = 8000;
			
			//Arch Table Lookup
			$sql="SELECT `max_arch_spacing`,`centre_brace_qty`,`max_bracket_spacing`,`arch_material`,`max_post_spacing_".$frontrailmaterial."` AS max_post_spacing FROM `fresco_archtable` WHERE `archlength`='".$lookuprow."'";
			$result = mysql_query($sql);
			
			while($row = mysql_fetch_array($result))
			{
				$step_array[$key]['max_arch_spacing']=$row['max_arch_spacing'];
				$step_array[$key]['centre_brace_qty']=$row['centre_brace_qty'];
				$step_array[$key]['arch_material']=$row['arch_material'];
				$step_array[$key]['max_bracket_spacing']=$row['max_bracket_spacing'];
				$step_array[$key]['nobrackets'] = ceil($step_array[$key]['length']/$row['max_bracket_spacing']);
				$step_array[$key]['bracket_spacing'] = round(($step_array[$key]['length']/$step_array[$key]['nobrackets']),2);
				
				if($step['width'] == $canopywidth)
				$step_array['max_post_spacing'] = $row['max_post_spacing'];
			}
			
			//Left Sections for each step
			$step_array[$key]['noofleftsections'] = ceil(($step_array[$key]['width']+$leftsuffitwidth)/$maxleftopeninglength);
			$step_array[$key]['lplength'] = round(($step_array[$key]['width']+$leftsuffitwidth)/$step_array[$key]['noofleftsections'],2);
			$step_array[$key]['lpheight'] = 2400;
			
			if($step_array[$key]['noofleftsections'] ==1)
			$step_array[$key]['left_post_spacing'] = 0;
			else
			$step_array[$key]['left_post_spacing'] = $step_array[$key]['lplength'];
			
			$step_array['maxrightopeninglength']=$maxrightopeninglength;
			
			
			//Right Sections for each step
			$step_array[$key]['noofrightsections'] = ceil(($step_array[$key]['width']+$rightsuffitwidth)/$maxrightopeninglength);
			$step_array[$key]['rplength'] = round(($step_array[$key]['width']+$rightsuffitwidth)/$step_array[$key]['noofrightsections'],2);
			$step_array[$key]['rpheight'] = 2400;
			
			if($step_array[$key]['noofrightsections'] ==1)
			$step_array[$key]['right_post_spacing'] = 0;
			else
			$step_array[$key]['right_post_spacing'] = $step_array[$key]['rplength'];
		}
		
		//Front Section
		if($step_array['max_post_spacing']>$maxleftopeninglength)
		$step_array['max_post_spacing']=$maxleftopeninglength;
		$nopost = ceil($canopylength/$step_array['max_post_spacing'])+1;
		$nooffrontsections = $nopost-1;
		$step_array['nofrontpost'] = $nopost;
		$step_array['nooffrontsections'] = $nooffrontsections;
		
		//Front Section length and height
		$step_array['fplength'] = round($canopylength/$nooffrontsections,2);
		$step_array['fpheight'] = 2400;
		
		// Rear Secion - Free Standing Canopies		
		if($freestanding=='1') {
			if($canopytype=='0') {
				$step_array['nobackpost'] = $step_array['nofrontpost'] + ($noofsteps-1);
				$step_array['nobacksections'] = $step_array['nobackpost']-1;
			} else {				
				$step_array['nobackpost']=$step_array['nofrontpost'];
				$step_array['nobacksections']=$step_array['nobackpost']-1;
			}
		} else {
			$step_array['nobackpost']=0;
			$step_array['nobacksections']=1;
		}

		//Assigning filltype and fabrictype to different sides
		$step_array['filltypeleft']=$filltypeleft;
		$step_array['fabrictypeleft']=$fabrictypeleft;
		$step_array['filltypefront']=$filltypefront;
		$step_array['fabrictypefront']=$fabrictypefront;
		$step_array['filltyperight']=$filltyperight;
		$step_array['fabrictyperight']=$fabrictyperight;
		$step_array['filltyperear']=$filltyperear;
		$step_array['fabrictyperear']=$fabrictyperear;
		
		//Lookup Post Material based on Frontrail material
		$sqlquery="SELECT `postmaterial` FROM `fresco_costing_frontrail_postmaterial` WHERE `frontrailmaterial`='".$frontrailmaterial."'";
		$step_array['sqlquery'] = $sqlquery;		
		$result = mysql_query($sqlquery);
		
		$row = mysql_fetch_array($result);
		$step_array['postmaterial'] = $row['postmaterial'];

		$step_array['noofsteps'] = $noofsteps;
		$step_array['quoteid'] = $quoteid;

		echo json_encode($step_array);
	}


	//FUNCTION - RETRIEVE ONLOAD VALUES FOR STEP 2	
	function retrieveonloadstep2() {
		
		//Posted Params
		$quoteid = $_REQUEST['quoteid'];

		//Initialization		
		$step_array =array();
		
		//Lookup 
		$sql="SELECT stepno, archqty, archspacing, archbraces, centrebraceqty FROM fresco_costing_archarray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		{
			$stepno = $row['stepno'];
			$step_array[$stepno]['archqty'] = $row['archqty'];
			$step_array[$stepno]['archspacing'] = $row['archspacing'];
			$step_array[$stepno]['centrebraceqty'] = $row['centrebraceqty'];
			$step_array[$stepno]['archbraces'] = $row['archbraces'];
		}
		
		//Lookup 
		$sql="SELECT canopyfabrictype FROM fresco_costing_quotetable WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		  
		$step_array['canopyfabrictype'] = $row['canopyfabrictype'];

		echo json_encode($step_array);
	}

	//FUNCTION - RETRIEVE ONLOAD VALUES FOR STEP 3	
	function retrieveonloadstep3() {
		
		//Posted Params
		$quoteid = $_REQUEST['quoteid'];
		
		//Initialization		
		$step_array =array();
		
		//Lookup data from endfill array Table
		$sql="SELECT `quoteid`,`stepno`,`endfillrequiredleft`,`endfillrequiredmiddle`,`endfillrequiredright`,`endfilllengthleft`,`endfilllengthmiddle`,`endfilllengthright`,`endfillheightleft`,`endfillheightmiddle`,`endfillheightright`,`endfillfabricleft`,`endfillfabricmiddle`,`endfillfabricright`,`endfilltypeleft`,`endfilltypemiddle`,`endfilltyperight`,`endfillstubleft`,`endfillstubmiddle`,`endfillstubright` FROM `fresco_costing_endfillarray` WHERE `quoteid` = '".$quoteid."'";		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result))
		{
			$row_array['stepno'] = $row['stepno'];
			$row_array['endfillrequiredleft'] = $row['endfillrequiredleft'];
			$row_array['endfillrequiredmiddle'] = $row['endfillrequiredmiddle'];
			$row_array['endfillrequiredright'] = $row['endfillrequiredright'];
			$row_array['endfilllengthleft'] = $row['endfilllengthleft'];
			$row_array['endfilllengthmiddle'] = $row['endfilllengthmiddle'];
			$row_array['endfilllengthright'] = $row['endfilllengthright'];
			$row_array['endfillheightleft'] = $row['endfillheightleft'];
			$row_array['endfillheightmiddle'] = $row['endfillheightmiddle'];
			$row_array['endfillheightright'] = $row['endfillheightright'];
			$row_array['endfillfabricleft'] = $row['endfillfabricleft'];
			$row_array['endfillfabricmiddle'] = $row['endfillfabricmiddle'];
			$row_array['endfillfabricright'] = $row['endfillfabricright'];
			$row_array['endfilltypeleft'] = $row['endfilltypeleft'];
			$row_array['endfilltypemiddle'] = $row['endfilltypemiddle'];
			$row_array['endfilltyperight'] = $row['endfilltyperight'];
			$row_array['endfillstubleft'] = $row['endfillstubleft'];
			$row_array['endfillstubmiddle'] = $row['endfillstubmiddle'];
			$row_array['endfillstubright'] = $row['endfillstubright'];
			
			array_push($step_array,$row_array);	
		}

		
		$step_array['quoteid'] = $quoteid;
		echo json_encode($step_array);
	}
	
	//FUNCTION - RETRIEVE ONLOAD VALUES FOR STEP 4	
	function retrieveonloadstep4() {
		
		//Posted Params
		$quoteid = $_REQUEST['quoteid'];
		
		//Initialization		
		$step_array =array();
		
		//Lookup data from endfill array Table
		$sql="SELECT `quoteid`,`side`,`scolloped`,`gutter`,`downpipe`,`timber` FROM `fresco_costing_additionalconfigarray` WHERE `quoteid` = '".$quoteid."'";		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result))
		{
			$row_array['side'] = $row['side'];
			$row_array['scolloped'] = $row['scolloped'];
			$row_array['gutter'] = $row['gutter'];
			$row_array['downpipe'] = $row['downpipe'];
			$row_array['timber'] = $row['timber'];
			
			array_push($step_array,$row_array);	
		}

		
		$step_array['quoteid'] = $quoteid;
		echo json_encode($step_array);
	}

	//FUNCTION - RETRIEVE ONLOAD VALUES FOR STEP 5	
	function retrieveonloadstep5() {
		
		//Posted Params
		$quoteid = $_REQUEST['quoteid'];
		
		//Initialization		
		$step_array =array();
		
		//Lookup data from endfill array Table
		$sql="SELECT `quoteid`,`adf`,`wq`,`fio`,`buildingconsent`,`gst`,`hotdipgalv`,`epoxy`,`polyester`,`abcite`,`leanto` FROM `fresco_costing_otherconfigarray` WHERE `quoteid` = '".$quoteid."'";		
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result))
		{
			$row_array['adf'] = $row['adf'];
			$row_array['wq'] = $row['wq'];
			$row_array['fio'] = $row['fio'];
			$row_array['buildingconsent'] = $row['buildingconsent'];
			$row_array['gst'] = $row['gst'];
			$row_array['hotdipgalv'] = $row['hotdipgalv'];
			$row_array['epoxy'] = $row['epoxy'];
			$row_array['polyester'] = $row['polyester'];
			$row_array['abcite'] = $row['abcite'];
			$row_array['leanto'] = $row['leanto'];
		}

		
		$step_array['quoteid'] = $quoteid;
		echo json_encode($row_array);
	}
	
	//FUNCTION - RETRIEVE ONLOAD VALUES FOR STEP 3	
	function lookupsteparray() {

		//Posted Params
		$quoteid = $_REQUEST['quoteid'];
		
		//Initialization		
		$step_array =array();
		
		//Lookup length and width from StepArray Table
		$sql="SELECT steptag, length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		{
			$row_array['steptag'] = $row['steptag'];
			$row_array['length'] = $row['length'];
			$row_array['width'] = $row['width'];
			array_push($step_array,$row_array);	
			
			$noofsteps++;
			
			$canopylength += $row['length'];
			if($row['width'] > $canopywidth)
			$canopywidth = $row['width'];
		}

		$step_array['canopylength'] = $canopylength;
		$step_array['canopywidth'] = $canopywidth;
		$step_array['noofsteps'] = $noofsteps;
		echo json_encode($step_array);
	}

	
?>
