<?php

try
{
	//Open database connection
	$con = mysql_connect("localhost","root","");
	mysql_select_db("vtigercrm", $con);


	//Getting records (listAction)
	if($_GET["action"] == "list")
	{
		
		//Get record count
		$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM fresco_costing_additionalitemsarray;");
		$row = mysql_fetch_array($result);
		$recordCount = $row['RecordCount'];

		//Get records from database
		$result = mysql_query("SELECT * FROM fresco_costing_additionalitemsarray LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"] . ";");
		
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
		$result = mysql_query("INSERT INTO fresco_costing_additionalitemsarray( quoteid, item, quantity, specification, cost) VALUES('" . $_POST["quoteid"]. "', '" . $_POST["item"]. "', '" .$_POST["quantity"]. "','" . $_POST["specification"]. "','" .$_POST["cost"]. "')");
	
		//Get last inserted record (to return to jTable)
		$result = mysql_query("SELECT * FROM fresco_costing_additionalitemsarray WHERE id = LAST_INSERT_ID();");
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
		$result = mysql_query("UPDATE fresco_costing_additionalitemsarray SET quoteid = '" . $_POST["quoteid"] ."',item = '" . $_POST["item"] . "',quantity = '" . $_POST["quantity"] . "',specification = '" . $_POST["specification"] . "',cost = '" . $_POST["cost"] . "' WHERE id = " . $_POST["id"] . ";");


		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
		//Delete from database
		$result = mysql_query("DELETE FROM fresco_costing_additionalitemsarray WHERE id = " . $_POST["id"] . ";");

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