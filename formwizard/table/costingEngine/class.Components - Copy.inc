<!-- begin of generated class -->
<?php
/*
*
* -------------------------------------------------------
* CLASSNAME:        Components
* GENERATION DATE:  25.06.2014
* CLASS FILE:       C:\xampp\htdocs\sqlclassgenerator/generated_classes/class.costing.php
* FOR MYSQL TABLE:  fresco_costing_array_primary
* FOR MYSQL DB:     vtigercrm
* -------------------------------------------------------
* CODE GENERATED BY:
* MY PHP-MYSQL-CLASS GENERATOR
* from: >> www.voegeli.li >> (download for free!)
* -------------------------------------------------------
*
*/


// **********************
// CLASS DECLARATION
// **********************

abstract class Components implements Model {
	
	// Define Component Type id : Primary Canopy Components
	
	const COMPONENT_TYPE_FRAME = 1;
	const COMPONENT_TYPE_ROOF_FABRIC = 2;
	const COMPONENT_TYPE_ENDFILLS = 3;
	const COMPONENT_TYPE_CURTAIN_STATIC = 4;
	const COMPONENT_TYPE_CURTAIN_GLIDETRAK = 5;
	const COMPONENT_TYPE_CURTAIN_ZIP = 6;
	const COMPONENT_TYPE_DOOR = 7;
	const COMPONENT_TYPE_LOUVRE = 8;
	const COMPONENT_TYPE_BLINDS = 9;

	
	// Setting Valid Component Type Array : Key - Value Pair.
	static public $valid_address_types = array(
		Components::COMPONENT_TYPE_FRAME => 'Frame',
		Components::COMPONENT_TYPE_ROOF_FABRIC => 'Roof Fabric',
		Components::COMPONENT_TYPE_ENDFILLS => 'EndFills',
		Components::COMPONENT_TYPE_CURTAIN_STATIC => 'Static Curtain',
		Components::COMPONENT_TYPE_CURTAIN_GLIDETRAK => 'GlideTrak Curtain',
		Components::COMPONENT_TYPE_CURTAIN_ZIP => 'Zip Curtain',
		Components::COMPONENT_TYPE_DOOR => 'Door',
		Components::COMPONENT_TYPE_LOUVRE => 'Louvre',
		Components::COMPONENT_TYPE_BLINDS => 'Blinds',
	);


// **********************
// ATTRIBUTE DECLARATION
// **********************

var $id;   // KEY ATTR. WITH AUTOINCREMENT

var $quoteid;   // (normal Attribute)
var $quoteid;   // (normal Attribute)

var $metal;   // (normal Attribute)
var $powdercoating;   // (normal Attribute)
var $fabric;   // (normal Attribute)
var $processing;   // (normal Attribute)
var $wastage;   // (normal Attribute)
var $installation;   // (normal Attribute)
var $margin;   // (normal Attribute)
var $other;   // (normal Attribute)

var $database; // Instance of class database


// **********************
// CONSTRUCTOR METHOD
// **********************

function costing()
{

$this->database = new Database();

}


// **********************
// GETTER METHODS
// **********************


function getid()
{
return $this->id;
}

function getquoteid()
{
return $this->quoteid;
}

function getmetal()
{
return $this->metal;
}

function getpowdercoating()
{
return $this->powdercoating;
}

function getfabric()
{
return $this->fabric;
}

function getprocessing()
{
return $this->processing;
}

function getwastage()
{
return $this->wastage;
}

function getinstallation()
{
return $this->installation;
}

function getmargin()
{
return $this->margin;
}

function getother()
{
return $this->other;
}

// **********************
// SETTER METHODS
// **********************


function setid($val)
{
$this->id =  $val;
}

function setquoteid($val)
{
$this->quoteid =  $val;
}

function setmetal($val)
{
$this->metal =  $val;
}

function setpowdercoating($val)
{
$this->powdercoating =  $val;
}

function setfabric($val)
{
$this->fabric =  $val;
}

function setprocessing($val)
{
$this->processing =  $val;
}

function setwastage($val)
{
$this->wastage =  $val;
}

function setinstallation($val)
{
$this->installation =  $val;
}

function setmargin($val)
{
$this->margin =  $val;
}

function setother($val)
{
$this->other =  $val;
}

// **********************
// SELECT METHOD / LOAD
// **********************

function select($id)
{

$sql =  "SELECT * FROM fresco_costing_array_primary WHERE id = $id;";
$result =  $this->database->query($sql);
$result = $this->database->result;
$row = mysql_fetch_object($result);


$this->id = $row->id;

$this->quoteid = $row->quoteid;

$this->metal = $row->metal;

$this->powdercoating = $row->powdercoating;

$this->fabric = $row->fabric;

$this->processing = $row->processing;

$this->wastage = $row->wastage;

$this->installation = $row->installation;

$this->margin = $row->margin;

$this->other = $row->other;

}

// **********************
// DELETE
// **********************

function delete($id)
{
$sql = "DELETE FROM fresco_costing_array_primary WHERE id = $id;";
$result = $this->database->query($sql);

}

// **********************
// INSERT
// **********************

function insert()
{
$this->id = ""; // clear key for autoincrement

$sql = "INSERT INTO fresco_costing_array_primary ( quoteid,metal,powdercoating,fabric,processing,wastage,installation,margin,other ) VALUES ( '$this->quoteid','$this->metal','$this->powdercoating','$this->fabric','$this->processing','$this->wastage','$this->installation','$this->margin','$this->other' )";
$result = $this->database->query($sql);
$this->id = mysql_insert_id($this->database->link);

}

// **********************
// UPDATE
// **********************

function update($id)
{



$sql = " UPDATE fresco_costing_array_primary SET  quoteid = '$this->quoteid',metal = '$this->metal',powdercoating = '$this->powdercoating',fabric = '$this->fabric',processing = '$this->processing',wastage = '$this->wastage',installation = '$this->installation',margin = '$this->margin',other = '$this->other' WHERE id = $id ";

$result = $this->database->query($sql);



}


} // class : end

?>
<!-- end of generated class -->