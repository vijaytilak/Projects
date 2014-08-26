<?php

try
{
	//Open database connection
	$con = mysql_connect("localhost","root","");
	mysql_select_db("vtigercrm", $con);

	//Patch for Checkbox 'false' value bug - Vijay
	if($_POST["rollerblinds"]=="")
	$_POST["rollerblinds"]="false";

	//Getting records (listAction)
	if($_GET["action"] == "list")
	{
		
		//Get record count
		$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM fresco_costing_frontarray;");
		$row = mysql_fetch_array($result);
		$recordCount = $row['RecordCount'];

		//Get records from database
		$result = mysql_query("SELECT * FROM fresco_costing_frontarray ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"] . ";");
		
		//Add all records to an array
		$rows = array();
		while($row = mysql_fetch_array($result))
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
	else if($_GET["action"] == "create")
	{
		//Insert record into database
		$result = mysql_query("INSERT INTO fresco_costing_frontarray( quoteid, sectiontag, side, plength, pheight, support, postposition, postmaterial, postbaseplate, brackets, brackettype, bracketspacing, sectiontype, filltype, fabrictype, rollerblinds, rollerfabric) VALUES('" . $_POST["quoteid"]. "', '" . $_POST["sectiontag"]. "','" .$_POST["side"]. "', '" .$_POST["plength"]. "','" .$_POST["pheight"]. "','" .$_POST["support"]. "', '" .$_POST["postposition"]. "', '" .$_POST["postmaterial"]. "', '" .$_POST["postbaseplate"]. "', '" .$_POST["brackets"]. "', '" .$_POST["brackettype"]. "','" .$_POST["bracketspacing"]. "', '" .$_POST["sectiontype"]. "', '" .$_POST["filltype"]. "','" .$_POST["fabrictype"]. "','" . $_POST["rollerblinds"] . "','" . $_POST["rollerfabric"] . "')");
	
		//Get last inserted record (to return to jTable)
		$result = mysql_query("SELECT * FROM fresco_costing_frontarray WHERE id = LAST_INSERT_ID();");
		$row = mysql_fetch_array($result);

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;
		print json_encode($jTableResult);
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{
		//Update record in database
		$result = mysql_query("UPDATE fresco_costing_frontarray SET quoteid = '" . $_POST["quoteid"] ."',sectiontag = '" . $_POST["sectiontag"] . "',side = '" . $_POST["side"] . "',plength = '" . $_POST["plength"] . "',pheight = '" . $_POST["pheight"] . "',support = '" . $_POST["support"] . "',postposition = '" . $_POST["postposition"] ."',postmaterial = '" . $_POST["postmaterial"]."',postbaseplate = '" . $_POST["postbaseplate"]."',brackets = '" . $_POST["brackets"] . "',brackettype = '" . $_POST["brackettype"] . "',bracketspacing = '" . $_POST["bracketspacing"] . "',sectiontype = '" . $_POST["sectiontype"] . "',filltype = '" . $_POST["filltype"] . "', fabrictype = '" . $_POST["fabrictype"] ."',rollerblinds = '" . $_POST["rollerblinds"] ."',rollerfabric = '" . $_POST["rollerfabric"] . "' WHERE id = " . $_POST["id"] . ";");


		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
		//Delete from database
		$result = mysql_query("DELETE FROM fresco_costing_frontarray WHERE id = " . $_POST["id"] . ";");

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}

	//Close database connection
	mysql_close($con);

}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $ex->getMessage();
	print json_encode($jTableResult);
}
	
?>