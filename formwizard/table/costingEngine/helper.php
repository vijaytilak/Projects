<?php
/**
* Define autoloader.
* @param string $class_name 
*/
function __autoload($class_name) {
	include 'class.' . $class_name . '.inc';
}

/**
* Dynamically lookup from database.
* @return array 
*/
function dbLookup($select, $from, $where) {
	global $quoteid;
	$return_array = array();
	$db = Database::getInstance();
	$mysqli = $db->getConnection();
	
	if(!$where)
	$where = 'quoteid = "'.$quoteid.'"';

	//Build Query
	$sql_query = 'SELECT '.$select.' FROM '.$from.' WHERE '.$where;

	//RUN SQL
	$result = $mysqli->query($sql_query);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	  {
		$variable_array = explode(",",$select);
		foreach ($variable_array as $variable) {
		  $temp_array[$variable] = $row[$variable];
		}
		array_push($return_array,$temp_array);	
	  }
	  
	if (!$result) {
	  trigger_error('Unable to query from database : SQL query :  ' . $sql_query);
	}

	return ($return_array);
  }

/**
* Lookup Step Array.
* @return array 
*/

function lookupsteparray() {

	//Initialization
	global $quoteid;		
	$step_array =array();
	$db = Database::getInstance();
	$mysqli = $db->getConnection();

	//Lookup length and width from StepArray Table
	$sql_query="SELECT steptag, length, width FROM fresco_costing_steparray WHERE quoteid = '".$quoteid."'";		
	$result = $mysqli->query($sql_query);

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
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
	return($step_array);
  }


/**
* Lookup Connector Cost.
* @return array 
*/

function lookupconnectorcost($connector) {
	//Lookup cost per connector
	$return_subsubarray = dbLookup("cost","fresco_costing_connectors","connector='".$connector."'");
	$costperconnector = $return_subsubarray[0]['cost'];
	return($costperconnector);
  }

/**
* Lookup Connector Cost.
* @return array 
*/

function lookupconnectorparams($connector) {
	$returnarray= array();
	//Lookup cost per connector
	$returnarray['costperconnector'] = lookupconnectorcost($connector);
	//Lookup dependant connector
	$return_subsubarray = dbLookup("dependancy,dependancyqty","fresco_costing_connectors","connector='".$connector."'");
	$returnarray['dependancy'] = $return_subsubarray[0]['dependancy'];
	$returnarray['dependancyqty'] = $return_subsubarray[0]['dependancyqty'];
	if($returnarray['dependancy'])
	$returnarray['dependantconnectorcost'] = lookupconnectorcost($returnarray['dependancy']) * $returnarray['dependancyqty'];
	
	//Lookup Fasteners
	$return_subsubarray = dbLookup("fastener,fastenerqty","fresco_costing_connectors","connector='".$connector."'");
	$returnarray['fastener'] = $return_subsubarray[0]['fastener'];
	$returnarray['fastenerqty'] = $return_subsubarray[0]['fastenerqty'];
	if($returnarray['fastener'])
	$returnarray['fastenercost'] = lookupconnectorcost($returnarray['fastener']);
	
	return($returnarray);
}


/**
* Lookup Material Cost.
* @return cost 
*/

function lookupMaterialCost($material,$totalmateriallength) {
	//Lookup materialname in fresco_costing_table_materials - returns costpermetre
	$return_array = dbLookup("costpermetre","fresco_costing_table_materials","material = '".$material."'");
	$costpermetre = $return_array[0]['costpermetre'];
	$totalcost = $totalmateriallength * $costpermetre;
	
	return ($totalcost);
}

/**
* Lookup Material Powder Coating Cost.
* @return cost 
*/

function lookupMaterialPowderCoatingCost($material,$totalmateriallength) {
	$input_array = array();
	$cost_array = array();
	$total_array = array();
	$return_array = array();

	//LOOKUP Total Cost Per metre - 
	//Lookup (1) Hot Dip Galv
	//Lookup (2) Epoxy Primer
	//Lookup (3) Polyester
	//Lookup (4) Abcite

	//Lookup stored inputs
	$input_array = dbLookup("hotdipgalv,epoxy,polyester,abcite","fresco_costing_otherconfigarray","");

	//Select Noncoated material from the table if hotdipgalv = yes	
	if($input_array[0]['hotdipgalv'])
	$where = "material = '".$material."' AND `type` = 'S' AND `coatingtype` = 'N'";
	else
	$where = "material = '".$material."' AND `type` = 'S' AND `coatingtype` = 'G'";
	
	//Looksup config codes for each powder coating type
	$cost_array = dbLookup("epoxy,polyester,abcite","fresco_costing_table_materials",$where);
	//Looks up values for those config codes and replaces in the same array
	foreach ($cost_array[0] as $keyid => $valueid)
	{
		$cost_subarray = dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'powdercoating'");
		//replace the config codes with values in the same array
		$cost_array[0][$keyid] = $cost_subarray[0]['value'];
	}
		//get costperkg of hotdipgalv from fresco_costing_config
		$cost_subarray = dbLookup("value","fresco_costing_config","`key` = 'hotdipgalv'");
		$cost_array[0]['hotdipgalv'] = $cost_subarray[0]['value'];
	

	//Looksup weightpermetre for material - for calc(hotdipgalv)
	$return_array = dbLookup("weightpermetre","fresco_costing_table_materials",$where);
	if($return_array[0]['weightpermetre'])
		$weightpermetre =  $return_array[0]['weightpermetre'];
	else
		$weightpermetre =  '0';
	$totalmaterialweight = $weightpermetre * $totalmateriallength;
	
	//Calculate total Powder Coating cost
	foreach ($input_array[0] as $key=>$value) {
		if($key == 'hotdipgalv')
		$total_array[0][$key] = $input_array[0][$key] * ($cost_array[0][$key] * $totalmaterialweight);
		else
		$total_array[0][$key] = $input_array[0][$key] * ($cost_array[0][$key] * $totalmateriallength);
	}
	$totalpowdercoatingcost = array_sum($total_array[0]);
	return ($totalpowdercoatingcost);
}


/**
* Lookup Material General Processing Cost.
* @return cost 
*/

function lookupMaterialProcessingCost($material,$totalmateriallength) {
	//Material Processing Cost
	$return_array = dbLookup("processallowance","fresco_costing_table_materials","material = '".$material."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'processallowance'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	$totalprocessallowance = ($return_array[0]['processallowance'] * $totalmateriallength);
	return ($totalprocessallowance);
}

/**
* Lookup Material Wastage Cost.
* @return cost 
*/

function lookupMaterialWastageCost($material,$metalcost) {
	//Material Wastage Cost
	$return_array = dbLookup("wasteallowance","fresco_costing_table_materials","material = '".$material."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'wasteallowance'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	$totalwasteallowance = ($return_array[0]['wasteallowance'] * $metalcost);
	//echo "<br>wasteallowance(".$return_array[0]['wasteallowance'].") * metalcost(".$metalcost.") = ".$totalwasteallowance;
	return ($totalwasteallowance);
}

/**
* Lookup Material Installation Cost.
* @return cost 
*/

function lookupMaterialInstallationCost($installcostpermaterialkey,$totalmateriallength,$totalmaterialqty) {
	//Material Install Cost
	$return_subarray = dbLookup("value","fresco_costing_config","`key` = '".$installcostpermaterialkey."' AND `category` = 'installation'");
	$installcostpermaterial = $return_subarray[0]['value'];
	$return_subarray = dbLookup("value","fresco_costing_config","`key` = 'installcostpermetre' AND `category` = 'installation'");
	$installcostpermetre = $return_subarray[0]['value'];
	
	$totalinstallallowance = ($totalmaterialqty*$installcostpermaterial) + ($totalmateriallength*$installcostpermetre);
	return ($totalinstallallowance);
}

/**
* Lookup Material Margin.
* @return margin 
*/

function lookupMaterialMargin($material) {
	//Lookup Component Margin Allowance lookup in %
	$return_array = dbLookup("margin","fresco_costing_table_materials","material = '".$material."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'margin'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	$percentagemargin = $return_array[0]['margin'];

	return ($percentagemargin);
}


/**
* Sum of all array elements with the same key
* @return sumArray 
*/
function arraySumKey($myArray) {
	$sumArray = array();
	
	foreach ($myArray as $k=>$subArray) {
	  foreach ($subArray as $id=>$value) {
		$sumArray[$id]+=$value;
	  }
	}
	return ($sumArray);
}

/**
* Group and add similar elements with the same key
* groupby = variable that needs to be grouped 
* valuekey = value key of the variable that needs to be summed and grouped 
* @return groupArray 
*/
function arrayGroupKey($myArray, $groupby, $valuekey) {
	$returnArray = array();
	$groupArray = array();
	
	echo '<tt><pre>' . var_export($myArray, TRUE) . '</pre></tt>';
	
	foreach ($myArray as $k=>$subArray) {
	  foreach ($subArray as $id=>$value) {
		$sumArray[$id]+=$value;
		if($id == $groupby)
		{
			if($value)
			{
				$groupArray[$value] += $myArray[$k][$valuekey];
			}
		}
	  }
	}
/*	//Converting it into an Associative Array
	$count=0;
	foreach ($groupArray as $key => $value)
	{
		$returnArray[$count]['key'] = $key;
		$returnArray[$count]['value'] = $value;
		$count++;
	}
		
	echo '<tt><pre>' . var_export($returnArray, TRUE) . '</pre></tt>';
*/
	return ($groupArray);
}



/**
 * Translate a result array into a HTML table
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.3.2
 * @link        http://aidanlister.com/2004/04/converting-arrays-to-human-readable-tables/
 * @param       array  $array      The result (numericaly keyed, associative inner) array.
 * @param       bool   $recursive  Recursively generate tables for multi-dimensional arrays
 * @param       string $null       String to output for blank cells
 */
function array2table($array, $recursive = false, $null = '&nbsp;')
{
    // Sanity check
    if (empty($array) || !is_array($array)) {
        return false;
    }
 
    if (!isset($array[0]) || !is_array($array[0])) {
        $array = array($array);
    }
 
    // Start the table
    $table = "<table class='pure-table'>\n";
 
    // The header
    $table .= "\t<tr>";
    // Take the keys from the first row as the headings
    foreach (array_keys($array[0]) as $heading) {
        $table .= '<th>' . $heading . '</th>';
    }
    $table .= "</tr>\n";
 
    // The body
    foreach ($array as $row) {
        $table .= "\t<tr>" ;
        foreach ($row as $cell) {
            $table .= '<td>';
 
            // Cast objects
            if (is_object($cell)) { $cell = (array) $cell; }
             
            if ($recursive === true && is_array($cell) && !empty($cell)) {
                // Recursive mode
                $table .= "\n" . array2table($cell, true, true) . "\n";
            } else {
                $table .= (strlen($cell) > 0) ?
                    htmlspecialchars((string) $cell) :
                    $null;
            }
 
            $table .= '</td>';
        }
 
        $table .= "</tr>\n";
    }
 
    $table .= '</table>';
    return $table;
}
  
?>
