<?php

/**
 * Component - Frame. 
 */
class ComponentFrame extends Component {
  /**
   * Initialization. 
   */
  protected function _init() {
    //Set Component Type based on the object class which called the method
	$this->_setComponentTypeId(Component::COMPONENT_TYPE_FRAME);
	
	//Set QuoteId if it doesnt exist
	if(!$this->_quote_id)
	{
		global $quoteid;
		$this->_quote_id=$quoteid;
	}
  }
  
// ************************ Arches *******************************

  /**
   * Arches - Metal Calculation. 
   */
  public function calculateArchesMetal() {
	$return_array = array();
	global $materiallist, $archparams;
	
	//LOOKUP Total Arch Quantity
	$return_array = $this->dbLookup("SUM(archqty)","fresco_costing_archarray","");
	$totalarchqty = $return_array[0]['SUM(archqty)'];

	//LOOKUP Total Arch Length
	$return_array = $this->dbLookupFull('SELECT SUM(width) as totalarchlength FROM fresco_costing_steparray WHERE quoteid = "'.$this->_quote_id.'"',"totalarchlength");
	$totalarchlength = round($return_array[0]['totalarchlength']/1000,2);

	//LOOKUP Total Cost Per metre - 
	//Lookup (1) Archmaterial first in fresco_costing_quotetable - returns id
	//Lookup (2) Archmaterialid in fresco_costing_archmaterial - returns materialname
	//Lookup (3) Archmaterialname in fresco_costing_table_materials - returns costpermetre
	
	$return_array = $this->dbLookup("archmaterial","fresco_costing_quotetable","");
	$archmaterialid = $return_array[0]['archmaterial'];
	
	$return_array = $this->dbLookup("archmaterial","fresco_costing_archmaterial","id = ".$archmaterialid);
	$archmaterialname = $return_array[0]['archmaterial'];
	
	$return_array = $this->dbLookup("costpermetre","fresco_costing_table_materials","material = '".$archmaterialname."'");
	$costpermetre = $return_array[0]['costpermetre'];
	
	//Formula for Arch Metal Calc
	$this->metal = round($totalarchlength*$costpermetre,2);
	echo "total arch qty = ".$totalarchqty;
	echo "<br> total arch length = ".$totalarchlength;
	echo "<br> costpermetre = ".$costpermetre;
	echo "<br> Metal Cost = ".$this->metal;
	
	$archparams['archmaterialname'] = $archmaterialname;
	$archparams['totalarchlength'] = $totalarchlength;
	$archparams['totalarchqty'] = $totalarchqty;
	$archparams['costpermetre'] = $costpermetre;
	$archparams['totalcost'] = $this->metal;
	
	//Adding to material list
	$materiallist['arches'] = $archparams;
	
	return;
  }


  /**
   * Arches - Powder Coating Calculation. 
   */
  public function calculateArchesPowderCoating() {
	$input_array = array();
	$cost_array = array();
	$total_array = array();
	$return_array = array();
	
	global $archparams;
	
	//LOOKUP Total Cost Per metre - 
	//Lookup (1) Hot Dip Galv
	//Lookup (2) Epoxy Primer
	//Lookup (3) Polyester
	//Lookup (4) Abcite

	//Lookup stored inputs
	$input_array = $this->dbLookup("hotdipgalv,epoxy,polyester,abcite","fresco_costing_otherconfigarray","");
	
	if($input_array[0]['hotdipgalv'])
	$where = "material = '".$archparams['archmaterialname']."' AND `type` = 'S' AND `coatingtype` = 'N'";
	else
	$where = "material = '".$archparams['archmaterialname']."' AND `type` = 'S' AND `coatingtype` = 'G'";
	
	//Looksup config codes for each powder coating type
	$cost_array = dbLookup("epoxy,polyester,abcite","fresco_costing_table_materials",$where);
	//Looks up values for those config codes and replaces in the same array
	foreach ($cost_array[0] as $keyid => $valueid)
	{
		$cost_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'powdercoating'");
		//replace the config codes with values in the same array
		$cost_array[0][$keyid] = $cost_subarray[0]['value'];
	}
		//get costperkg of hotdipgalv from fresco_costing_config
		$cost_subarray = $this->dbLookup("value","fresco_costing_config","`key` = 'hotdipgalv'");
		$cost_array[0]['hotdipgalv'] = $cost_subarray[0]['value'];
	
echo '<tt><pre>' . var_export($cost_array, TRUE) . '</pre></tt>';

	//Looksup weightpermetre for archmaterial - for calc(hotdipgalv)
	$return_array = dbLookup("weightpermetre","fresco_costing_table_materials",$where);
	if($return_array[0]['weightpermetre'])
	$weightpermetre =  $return_array[0]['weightpermetre'];
	else
	$weightpermetre =  '0';
	echo "<br>weightpermetre=".$weightpermetre;
	$totalarchweight = $weightpermetre * $archparams['totalarchlength'];
	
	echo "<br>totalarchweight=".$totalarchweight;
	
	//Lookup stored inputs
	$input_array = $this->dbLookup("hotdipgalv,epoxy,polyester,abcite","fresco_costing_otherconfigarray","");

echo '<tt><pre>' . var_export($input_array, TRUE) . '</pre></tt>';
	
	//Calculate total Powder Coating cost
	foreach ($input_array[0] as $key=>$value) {
		if($key == 'hotdipgalv')
		$total_array[0][$key] = $input_array[0][$key] * ($cost_array[0][$key] * $totalarchweight );
		else
		$total_array[0][$key] = $input_array[0][$key] * ($cost_array[0][$key] * $archparams['totalarchlength']);
	}
	
echo '<tt><pre>' . var_export($total_array, TRUE) . '</pre></tt>';
echo "total powdercoaitng cost = ".$this->powdercoating = array_sum($total_array[0]);	
  }


  /**
   * Arches - Fabric Calculation. 
   */
  public function calculateArchesFabric() {
	$this->fabric = '0';
	
	return;
  }


  /**
   * Arches - Processing Calculation. 
   */
  public function calculateArchesProcessAllowance() {
	global $archparams;
	$return_array = array();

	// Total Processing Allowance = Bending Cost + Generic Processing Cost
	// Bending cost = Setup Cost + Cost per Arch + Cost per Metre
	$return_array = dbLookup("value","fresco_costing_config","`key`='setupcost' AND `category` = 'bending'");
	echo "<br>setupcost=".$setupcost = $return_array[0]['value'];
	$return_array = dbLookup("value","fresco_costing_config","`key`='costperarch' AND `category` = 'bending'");
	echo "<br>costperarch=".$costperarch = $return_array[0]['value'];
	$return_array = dbLookup("value","fresco_costing_config","`key`='costpermetre' AND `category` = 'bending'");
	echo "<br>costpermetre=".$costpermetre = $return_array[0]['value'];
	
	//Total Bending Cost
	echo "<br>totalbendingcost=".$totalbendingcost = $setupcost + ($costperarch*$totalarchqty) + ($costpermetre*$archparams['totalarchlength']);
	
	//Material Processing Cost
	$return_array = dbLookup("processallowance","fresco_costing_table_materials","material = '".$archparams['archmaterialname']."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'processallowance'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	$processallowance = $return_array[0]['processallowance'];
	
	echo "<br>totalprocessingallowance=".$totalprocessingallowance = $processallowance + $totalbendingcost;
	$this->processing = $totalprocessingallowance;
	return;
  }


  /**
   * Arches - Wastage Calculation. 
   */
  public function calculateArchesWasteAllowance() {
	global $materiallist, $archparams;
	$return_array = array();

	//Material Wastage Allowance lookup in %
	$return_array = dbLookup("wasteallowance","fresco_costing_table_materials","material = '".$archparams['archmaterialname']."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'wasteallowance'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	echo "<br>wasteallowance=".$wasteallowance = $return_array[0]['wasteallowance'];
	$totalwastageallowance = ($wasteallowance/100) * $archparams['totalarchlength'] * $archparams['costpermetre'];
	echo "<br>totalwastage=".$totalwastageallowance;
	$this->wastage = $totalwastageallowance;
	
	$materiallist['arches']['wastematerialallowanceinmetre'] = ($wasteallowance/100) * $archparams['totalarchlength'];
	return;
  }


  /**
   * Arches - Installation Calculation. 
   */
  public function calculateArchesInstallAllowance() {
	global $archparams;
	$return_array = array();

	//Installation cost = (No of Arches * Costperarch) + (TotalArchLength * Costpermetre)
	$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = 'installcostperarch' AND `category` = 'installation'");
	$installcostperarch = $return_subarray[0]['value'];
	$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = 'installcostpermetre' AND `category` = 'installation'");
	$installcostpermetre = $return_subarray[0]['value'];


	$installallowance = ($archparams['totalarchqty']*$installcostperarch) + ($archparams['totalarchlength']*$installcostpermetre);
	echo "<br>installallowance=".$installallowance;
	$this->installation = $installallowance;
	return;
  }

  /**
   * Arches - Other. 
   */
  public function calculateArchesOther() {
	$this->other = 0;
	return;
  }


  /**
   * Arches - Margin. 
   */
  public function calculateArchesMargin() {
	global $archparams;
	$return_array = array();

	//Lookup
	//Component Margin Allowance lookup in %
	$return_array = dbLookup("margin","fresco_costing_table_materials","material = '".$archparams['archmaterialname']."'");
	//Looks up values for those config codes and replaces in the same array
	foreach ($return_array[0] as $keyid => $valueid)
	{
		$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'margin'");
		//replace the config codes with values in the same array
		$return_array[0][$keyid] = $return_subarray[0]['value'];
	}
	echo "<br>%margin=".$percentagemargin = $return_array[0]['margin'];
	//didnt include installation margin as its kept separate
	$margin = ($percentagemargin/100) * ($this->metal + $this->powdercoating + $this->fabric + $this->processing + $this->wastage + $this->other);

	$this->margin = $margin;
	echo "<br>total margin=".$margin;
	return;
  }
// ************************ End Arches *******************************

// ************************ Frame Brackets *******************************
  /**
   * Brackets - Metal Calculation. 
   */
  public function calculateBracketsMetal() {
	global $materiallist, $bracketparams, $quoteid;
	$return_array = array();

	//Lookup no of Brackets and its Type from Front Array
	$return_array = dbLookup("brackets,brackettype","fresco_costing_frontarray","quoteid = '".$quoteid."' AND brackets > 0");
	foreach ($return_array as $keyid => $valueid)
	{
		//Lookup Bracket cost for that type of Bracket
		$return_subarray = dbLookup("cost","fresco_costing_connectors","`id` = '".$return_array[$keyid]['brackettype']."' AND `category` = 'bracket'");
		//Costpertype * Quantity
		$return_array[$keyid]['subtotal'] = $return_subarray[0]['cost']*$return_array[$keyid]['brackets'];
		$bracketcost += $return_array[$keyid]['subtotal'];
		
		//Lookup bracketconnector name and replace the brackettypeid
		$return_subarray = dbLookup("connector","fresco_costing_connectors","`id` = '".$return_array[$keyid]['brackettype']."' AND `category` = 'bracket'");
		$return_array[$keyid]['brackettype'] = $return_subarray[0]['connector'];

		//Lookup bracket dependancy
		$return_subarray = dbLookup("dependancy,dependancyqty","fresco_costing_connectors","`connector` = '".$return_array[$keyid]['brackettype']."' AND `category` = 'bracket'");
		//Costpertype * Quantity
		$return_array[$keyid]['depandancy'] = $return_subarray[0]['dependancy'];
		$return_array[$keyid]['depandancyqty'] = $return_subarray[0]['dependancyqty'];
	}
	echo "<br>bracketcost=".$bracketcost;
	$materiallist['brackets']=$bracketparams=$return_array;
	$this->metal = $bracketcost;

	return;
  }


  /**
   * Brackets - Powder Coating Calculation. 
   */
  public function calculateBracketsPowderCoating() {
	$input_array = array();
	$cost_array = array();
	$total_array = array();
	$return_array = array();
	
	global $materiallist, $bracketparams, $quoteid;
	
	//LOOKUP Total Cost Per unit - 
	//Lookup (1) Hot Dip Galv - weightperunit * hotdipdgalv * qty
	//Lookup (2) Epoxy Primer - epoxy * qty
	//Lookup (3) Polyester - polyester * qty
	//Lookup (4) Zinc - zinc * qty

	//Lookup stored inputs
	$input_array = $this->dbLookup("hotdipgalv,epoxy,polyester,abcite","fresco_costing_otherconfigarray","");
	
	if($input_array[0]['hotdipgalv'])
		$input_array[0]['zinc'] = '0';
	else
		$input_array[0]['zinc'] = '1';

	//Lookup qty of brackets from bracketparams
	foreach ($bracketparams as $keyid => $valueid)
	{
		//Looksup config codes for each powder coating type
		$cost_array = dbLookup("epoxy,polyester,zinc","fresco_costing_connectors","connector = '".$bracketparams[$keyid]['brackettype']."'");
		//Looks up values for those config codes and replaces in the same array
		foreach ($cost_array[0] as $key => $value)
		{
			$cost_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$value."' AND `category` = 'powdercoating'");
			//replace the config codes with values in the same array
			$cost_array[0][$key] = $cost_subarray[0]['value'];
		}
		//get costperkg of hotdipgalv from fresco_costing_config
		$cost_subarray = $this->dbLookup("value","fresco_costing_config","`key` = 'hotdipgalv'");
		$cost_array[0]['hotdipgalv'] = $cost_subarray[0]['value'];

		//if Aluminum, do not add cost as it can't be hotdipgalv		
		$temp_array = dbLookup("weightperunit,type","fresco_costing_connectors","connector = '".$bracketparams[$keyid]['brackettype']."' AND `type` = 'S'");
		$type = $temp_array[0]['type'];
		if($type=='S')
		$weightperunit = $temp_array[0]['weightperunit'];
		else
		$weightperunit = 0;

		//cost for each powdercoating type * qty
		foreach ($cost_array[0] as $key => $value) {
		  if ($key == 'hotdipgalv')
		  $cost_array[0][$key] = $value * $weightperunit * $bracketparams[$keyid]['brackets'];
		  else
		  $cost_array[0][$key] = $value * $bracketparams[$keyid]['brackets'];
		}

		$subtotalcosthotdipgalv += $cost_array[0]['hotdipgalv'];
		$subtotalcostepoxy += $cost_array[0]['epoxy'];
		$subtotalcostpolyester += $cost_array[0]['polyester'];
		$subtotalcostzinc += $cost_array[0]['zinc'];

	}
	//Results
	echo "<br>totalcosthotdipgalv".$totalcosthotdipgalv = $subtotalcosthotdipgalv * $input_array[0]['hotdipgalv'];
	echo "<br>totalcostepoxy".$totalcostepoxy = $subtotalcostepoxy * $input_array[0]['epoxy'];
	echo "<br>totalcostpolyester".$totalcostpolyester = $subtotalcostpolyester * $input_array[0]['polyester'];
	echo "<br>totalcostzinc".$totalcostzinc = $subtotalcostzinc * $input_array[0]['zinc'];

	echo "<br>totalpowdercoatingcost for brackets=". $totalpowdercoatingcost = $totalcosthotdipgalv+$totalcostepoxy+$totalcostpolyester+$totalcostzinc;
	$this->powdercoating = $totalpowdercoatingcost;
  }

  /**
   * Brackets - Fabric Calculation. 
   */
  public function calculateBracketsFabric() {
	$this->fabric = '0';
	return;
  }


  /**
   * Brackets - Processing Calculation. 
   */
  public function calculateBracketsProcessAllowance() {
	$this->processing = 0;
	return;
  }


  /**
   * Brackets - Wastage Calculation. 
   */
  public function calculateBracketsWasteAllowance() {
	$this->wastage = 0;
	return;
  }


  /**
   * Brackets - Installation Calculation. 
   */
  public function calculateBracketsInstallAllowance() {
	$this->installation = 0;
	return;
  }

  /**
   * Brackets - Other. 
   */
  public function calculateBracketsOther() {
	$this->other = 0;
	return;
  }


  /**
   * Brackets - Margin. 
   */
  public function calculateBracketsMargin() {
	global $bracketparams;
	$return_array = array();

	//Lookup
	foreach ($bracketparams as $bracketkey => $bracketvalue)
	{
		//Component Margin Allowance lookup in %
		$return_array = dbLookup("margin","fresco_costing_connectors","connector = '".$bracketparams[$bracketkey]['brackettype']."'");
		//Looks up values for those config codes and replaces in the same array
		foreach ($return_array[0] as $keyid => $valueid)
		{
			$return_subarray = $this->dbLookup("value","fresco_costing_config","`key` = '".$valueid."' AND `category` = 'margin'");
			//replace the config codes with values in the same array
			$return_array[0][$keyid] = $return_subarray[0]['value'];
		}
		echo "<br>%bracketmargin=".$percentagemargin = $return_array[0]['margin'];
		//didnt include installation margin as its kept separate
		$margin = ($percentagemargin/100) * ($this->metal + $this->powdercoating + $this->fabric + $this->processing + $this->wastage + $this->other);
	}
	$this->margin = $margin;
	echo "<br>total bracket margin=".$margin;
	return;
  }



// ************************ End Frame Brackets *******************************

// ************************ Frame Connectors *******************************
  public function calculateConnectorsMetal() {
	global $materiallist, $archparams,$bracketparams, $connectorparams, $quoteid;
	$return_array = array();
	
	//Connector type * Connector Qty * Cost 
	//Connectors [Centre Brace - Arch Connection] = No of CB * No of Arches
	//Connectors [Arch - Rail Connection] = No of Arches (front rail + backrail)
	//Connectors [Rail - Post Connection] = No of Post ( frontrail + backrail)
	//Connectors [Post - BasePlate Connection] = No of Lookup front Array
	//Connectors [Bracket - Dependant Connection] = lookup material list or bracket params

	//CB - ARCH CONNECTORS
	//For every step => Connection points = Arch Qty * CB Qty
	$steparray = lookupsteparray();	
	
	foreach ($steparray as $key => $value)
	{
		//Lookup Bracket cost for that type of Bracket
		$return_subarray = dbLookup("archqty,centrebraceqty","fresco_costing_archarray","");
		//CBArchConnectors = archqty * centrebraceqty
		echo $CBArchConnectors += $return_subarray[$key]['archqty'] * $return_subarray[$key]['centrebraceqty'];
		
	}
echo "<br>CBArchConnectors=".$CBArchConnectors;
	$cbmaterial = "51R-1.6"; // Default
	$archmaterialname = $archparams['archmaterialname'];
	
	//Lookup connector type based on Arch and CB material
	$return_subsubarray = dbLookup("arch_cb_connector","fresco_costing_connector_mapping","arch='".$archmaterialname."'");
	$connector = $return_subsubarray[0]['arch_cb_connector'];
	
	$con_params = lookupconnectorparams($connector);
	$temp_array['connectortype']= "CB-Arch";
	$temp_array['connector']= $connector;
	$temp_array['connectorqty']= $CBArchConnectors;
	$temp_array['connectorcost']= $temp_array['connectorqty']*$con_params['costperconnector'];
	$temp_array['dependantconnector']= $con_params['dependancy'];
	$temp_array['dependantconnectorqty']= $con_params['dependancyqty'];
	$temp_array['dependantconnectorcost']= $con_params['dependantconnectorcost'];
	$temp_array['fastener']= $con_params['fastener'];
	$temp_array['fastenerqty']= $con_params['fastenerqty'] * ($temp_array['connectorqty'] + $temp_array['dependantconnectorqty']);
	$temp_array['fastenercost']= $con_params['fastenercost'] * $temp_array['fastenerqty'];
	$temp_array['subtotalconnectorcost']= $temp_array['connectorcost']+$temp_array['dependantconnectorcost']+$temp_array['fastenercost'];
	
	$connectorparams[] = $temp_array;
	
	
	//ARCH - RAIL CONNECTORS
	//Lookup Each side to determine the no and type of connector based on - Scolloped or not
	$return_subarray = dbLookup("side,scolloped","fresco_costing_additionalconfigarray","");
	foreach ($return_subarray as $key => $value)
	{
		$temp_array = array();
		echo "<br>".$return_subarray[$key]['side'];
		echo "<br>".$return_subarray[$key]['scolloped'];

		if($return_subarray[$key]['side']=="Front")
		{
			if($return_subarray[$key]['scolloped']=='on')
				$column = "scolloped_arch_rail_connector";
			else
				$column = "straight_arch_rail_connector";
			
			//Connectors [Arch - Rail Connection] = No of Arches (front rail)
			//Lookup input for frontrail material
			$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_quotetable","");
			$frontrailmaterialid = $return_subsubarray[0]['frontrailmaterial'];
			$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_arch_siderail","id='".$frontrailmaterialid."'");
			$frontrailmaterial = $return_subsubarray[0]['frontrailmaterial'];
			
			//Lookup connector type based on Arch and Frontrail material
			$return_subsubarray = dbLookup($column,"fresco_costing_connector_mapping","arch='".$archmaterialname."' AND rail = '".$frontrailmaterial."'");
			$connector = $return_subsubarray[0][$column];

			$con_params = lookupconnectorparams($connector);
			$temp_array['connectortype']= "Arch-FrontRail";
			$temp_array['connector']= $connector;
			$temp_array['connectorqty']= $archparams['totalarchqty'];
			$temp_array['connectorcost']= $temp_array['connectorqty']*$con_params['costperconnector'];
			$temp_array['dependantconnector']= $con_params['dependancy'];
			$temp_array['dependantconnectorqty']= $con_params['dependancyqty'];
			$temp_array['dependantconnectorcost']= $con_params['dependantconnectorcost'];
			$temp_array['fastener']= $con_params['fastener'];
			$temp_array['fastenerqty']= $con_params['fastenerqty'] * ($temp_array['connectorqty'] + $temp_array['dependantconnectorqty']);
			$temp_array['fastenercost']= $con_params['fastenercost'] * $temp_array['fastenerqty'];
			$temp_array['subtotalconnectorcost']= $temp_array['connectorcost']+$temp_array['dependantconnectorcost']+$temp_array['fastenercost'];
			
			$connectorparams[] = $temp_array;
		}
		else if($return_subarray[$key]['side']=="sidebackstep".($key-1))
		{
			if($return_subarray[$key]['scolloped']=='on')
				$column = "scolloped_arch_rail_connector";
			else
				$column = "straight_arch_rail_connector";
			
			//Lookup if canopy is freestanding or not- if yes, connectors on backrail should be calc
			$return_subsubarray = dbLookup("freestanding","fresco_costing_quotetable","");
			$freestanding = $return_subsubarray[0]['freestanding'];
			if(!$freestanding)
			{
				//Lookup input for Backrail material 
				$return_subsubarray = dbLookup("backrailmaterial","fresco_costing_quotetable","");
				$backrailmaterialid = $return_subsubarray[0]['backrailmaterial'];
				$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_arch_siderail","id='".$backrailmaterialid."'");
				$backrailmaterial = $return_subsubarray[0]['frontrailmaterial'];//( In the fresco_costing_arch_siderail table frontrail id = backrail id )
				
				//Lookup connector type based on Arch and Frontrail material
				$return_subsubarray = dbLookup($column,"fresco_costing_connector_mapping","arch='".$archmaterialname."' AND rail = '".$backrailmaterial."'");
				$connector = $return_subsubarray[0][$column];
				
				//LOOKUP Total Arch Quantity for current step
				$return_array = dbLookup("archqty","fresco_costing_archarray","stepno='".($key-1)."'");
				$archqty = $return_array[0]['archqty'];

				$con_params = lookupconnectorparams($connector);
				$temp_array['connectortype']= "Arch-BackRail";
				$temp_array['connector']= $connector;
				$temp_array['connectorqty']= $archqty;
				$temp_array['connectorcost']= $temp_array['connectorqty']*$con_params['costperconnector'];
				$temp_array['dependantconnector']= $con_params['dependancy'];
				$temp_array['dependantconnectorqty']= $con_params['dependancyqty'];
				$temp_array['dependantconnectorcost']= $con_params['dependantconnectorcost'];
				$temp_array['fastener']= $con_params['fastener'];
				$temp_array['fastenerqty']= $con_params['fastenerqty'] * ($temp_array['connectorqty'] + $temp_array['dependantconnectorqty']);
				$temp_array['fastenercost']= $con_params['fastenercost'] * $temp_array['fastenerqty'];
				$temp_array['subtotalconnectorcost']= $temp_array['connectorcost']+$temp_array['dependantconnectorcost']+$temp_array['fastenercost'];
				
				$connectorparams[] = $temp_array;
			}
		}
	}
	
	//RAIL - POST CONNECTORS & 
	//Lookup each post from the Front Array and lookup appropriate connectors for it.
	//Sides : Front-1 ; Left-2; Right-3; Rear-4;
	//PostPosition : Left-1; Right-2; Both-3; None-4;
	
	//Lookup FRONT ARRAY
	$section_array = dbLookup("side,support,postposition,postmaterial,postbaseplate","fresco_costing_frontarray","");
	foreach ($section_array as $key => $value)
	{
		//For each section identify no of posts/connectors/baseplates
		if($value['side'] == "1") //FRONT
		{
			if(($value['postposition'] == "1") || ($value['postposition'] == "2")) // Left or Right
			$connectorqty=1;
			else if($value['postposition'] == "3") // Both
			$connectorqty=2;
			else if($value['postposition'] == "4") // None
			$connectorqty=0;
			
			//Lookup connector type based on Post and Frontrail material
			$return_subsubarray = dbLookup("rail_post_connector","fresco_costing_connector_mapping","rail='".$frontrailmaterial."' AND post = '".$value['postmaterial']."'");
			$connector = $return_subsubarray[0]['rail_post_connector'];
			
			$con_params = lookupconnectorparams($connector);
			$temp_array['connectortype']= "Rail-FrontPost";
			$temp_array['connector']= $connector;
			$temp_array['connectorqty']= $connectorqty;
			$temp_array['connectorcost']= $temp_array['connectorqty']*$con_params['costperconnector'];
			$temp_array['dependantconnector']= $con_params['dependancy'];
			$temp_array['dependantconnectorqty']= $con_params['dependancyqty'];
			$temp_array['dependantconnectorcost']= $con_params['dependantconnectorcost'];
			$temp_array['fastener']= $con_params['fastener'];
			$temp_array['fastenerqty']= $con_params['fastenerqty'] * ($temp_array['connectorqty'] + $temp_array['dependantconnectorqty']);
			$temp_array['fastenercost']= $con_params['fastenercost'] * $temp_array['fastenerqty'];
			$temp_array['subtotalconnectorcost']= $temp_array['connectorcost']+$temp_array['dependantconnectorcost']+$temp_array['fastenercost'];
			$connectorparams[] = $temp_array;
		} else
		if($value['side'] == "4")
		{
			if(($value['postposition'] == "1") || ($value['postposition'] == "2")) // Left or Right
			$connectorqty=1;
			else if($value['postposition'] == "3") // Both
			$connectorqty=2;
			else if($value['postposition'] == "4") // None
			$connectorqty=0;
			
			//Lookup connector type based on Post and Frontrail material
			$return_subsubarray = dbLookup("rail_post_connector","fresco_costing_connector_mapping","rail='".$backrailmaterial."' AND post = '".$value['postmaterial']."'");
			$connector = $return_subsubarray[0]['rail_post_connector'];
			
			$con_params = lookupconnectorparams($connector);
			$temp_array['connectortype']= "Rail-BackPost";
			$temp_array['connector']= $connector;
			$temp_array['connectorqty']= $connectorqty;
			$temp_array['connectorcost']= $temp_array['connectorqty']*$con_params['costperconnector'];
			$temp_array['dependantconnector']= $con_params['dependancy'];
			$temp_array['dependantconnectorqty']= $con_params['dependancyqty'];
			$temp_array['dependantconnectorcost']= $con_params['dependantconnectorcost'];
			$temp_array['fastener']= $con_params['fastener'];
			$temp_array['fastenerqty']= $con_params['fastenerqty'] * ($temp_array['connectorqty'] + $temp_array['dependantconnectorqty']);
			$temp_array['fastenercost']= $con_params['fastenercost'] * $temp_array['fastenerqty'];
			$temp_array['subtotalconnectorcost']= $temp_array['connectorcost']+$temp_array['dependantconnectorcost']+$temp_array['fastenercost'];
			$connectorparams[] = $temp_array;
		}
		
		//echo array2table($value);
	}

echo "<br>";
echo array2table($connectorparams);



$sumArray = array();
$connectorArray = array();
foreach ($connectorparams as $k=>$subArray) {
  foreach ($subArray as $id=>$value) {
	if($id == "connector")
    {
		if($value)
		$connectorArray[$value] += $connectorparams[$k]["connectorqty"];
	}
	
	if($id == "dependantconnector")
    {
		if($value)
		$connectorArray[$value] += $connectorparams[$k]["dependantconnectorqty"];
	}
	
	if($id == "fastener")
    {
		if($value)
		$connectorArray[$value] += $connectorparams[$k]["fastenerqty"];
	}
    $sumArray[$id]+=$value;
	
  }
}



echo '<tt><pre>' . var_export($connectorArray, TRUE) . '</pre></tt>';
echo '<tt><pre>' . var_export($sumArray, TRUE) . '</pre></tt>';

	return;
  }



// ************************ End Frame Connectors *******************************

// ************************ Frame Centre Braces *******************************

  /**
   * CB - Metal Calculation. 
   */
  public function calculateCBMetal() {
	$return_array = array();
	global $materiallist, $cbparams;
	
	$steparray = lookupsteparray();
	$return_array = dbLookup("centrebraceqty","fresco_costing_archarray","");
	foreach ($return_array as $key => $value)
	{
		$totalCBqty += $return_array[$key]['centrebraceqty'];
		$totalCBlength += $return_array[$key]['centrebraceqty'] * $steparray[$key]['length'];
		
	}
	$cbparams['totallength'] = round($totalCBlength/1000,2);
	$cbparams['totalqty'] = $totalCBqty;
	
	$cbparams['material'] = "51R-1.6"; // Default
	$this->metal = lookupMaterialCost($cbparams['material'],$cbparams['totallength']);
  }


  /**
   * CB - Powder Coating Calculation. 
   */
  public function calculateCBPowderCoating() {
	global $materiallist, $cbparams;
	$this->powdercoating = lookupMaterialPowderCoatingCost($cbparams['material'],$cbparams['totallength']);
	return;
  }


  /**
   * CB - Fabric Calculation. 
   */
  public function calculateCBFabric() {
	$this->fabric = '0';
	return;
  }


  /**
   * CB - Processing Calculation. 
   */
  public function calculateCBProcessAllowance() {
	global $cbparams;
	$this->processing = lookupMaterialProcessingCost($cbparams['material'],$cbparams['totallength']);
	return;
  }


  /**
   * CB - Wastage Calculation. 
   */
  public function calculateCBWasteAllowance() {
	global $cbparams;
	$this->wastage = lookupMaterialWastageCost($cbparams['material'],$this->metal);
	return;
  }


  /**
   * CB - Installation Calculation. 
   */
  public function calculateCBInstallAllowance() {
	global $cbparams;
	$this->installation = lookupMaterialInstallationCost("installcostpercb",$cbparams['totallength'],$cbparams['totalqty']);
	return;
  }

  /**
   * CB - Other. 
   */
  public function calculateCBOther() {
	$this->other = 0;
	return;
  }


  /**
   * Arches - Margin. 
   */
  public function calculateCBMargin() {
	global $cbparams;
	//Excluding Installation Margin
	$this->margin = lookupMaterialMargin($cbparams['material']) * ($this->metal + $this->powdercoating + $this->fabric + $this->processing + $this->wastage + $this->other);
	return;
  }
  
// ************************ End Frame Centre Braces *******************************

// ************************ Frame SideRail *******************************

  /**
   * SideRail - Metal Calculation. 
   * FrontRail + BackRail
   */
  public function calculateSideRailMetal() {
	$return_array = array();
	global $materiallist, $siderailparams,$backrailparams;
	
	//Lookup input for frontrail material
	$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_quotetable","");
	$frontrailmaterialid = $return_subsubarray[0]['frontrailmaterial'];
	$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_arch_siderail","id='".$frontrailmaterialid."'");
	$siderailparams['frontrailmaterial'] = $return_subsubarray[0]['frontrailmaterial'];
	
	//Lookup input for backrail material
	$return_subsubarray = dbLookup("backrailmaterial","fresco_costing_quotetable","");
	$backrailmaterialid = $return_subsubarray[0]['backrailmaterial'];
	$return_subsubarray = dbLookup("frontrailmaterial","fresco_costing_arch_siderail","id='".$backrailmaterialid."'");
	$siderailparams['backrailmaterial'] = $return_subsubarray[0]['frontrailmaterial'];

	$steparray = lookupsteparray();
	
	//Set Other Side Rail Params
	$siderailparams['totallength'] = round($steparray['canopylength']/1000,2);
	$siderailparams['totalqty'] = $steparray['noofsteps'];
	
	$frontrailmetalcost = lookupMaterialCost($siderailparams['frontrailmaterial'],$siderailparams['totallength']);
	$backrailmetalcost = lookupMaterialCost($siderailparams['backrailmaterial'],$siderailparams['totallength']);
	
	$this->metal = $frontrailmetalcost + $backrailmetalcost;
	return;
  }


  /**
   * SideRail - Powder Coating Calculation. 
   */
  public function calculateSideRailPowderCoating() {
	global $materiallist, $siderailparams;
	$frontrailpowdercoatingcost = lookupMaterialPowderCoatingCost($siderailparams['frontrailmaterial'],$siderailparams['totallength']);
	$backrailpowdercoatingcost = lookupMaterialPowderCoatingCost($siderailparams['backrailmaterial'],$siderailparams['totallength']);
	$this->powdercoating = $frontrailpowdercoatingcost + $backrailpowdercoatingcost;
	return;
  }


  /**
   * SideRail - Fabric Calculation. 
   */
  public function calculateSideRailFabric() {
	$this->fabric = '0';
	return;
  }


  /**
   * SideRail - Processing Calculation. 
   */
  public function calculateSideRailProcessAllowance() {
	global $siderailparams;
	$frontrailprocessingcost = lookupMaterialProcessingCost($siderailparams['frontrailmaterial'],$siderailparams['totallength']);
	$backrailprocessingcost = lookupMaterialProcessingCost($siderailparams['backrailmaterial'],$siderailparams['totallength']);
	$this->processing =$frontrailprocessingcost + $backrailprocessingcost;
	return;
  }


  /**
   * SideRail - Wastage Calculation. 
   */
  public function calculateSideRailWasteAllowance() {
	global $siderailparams;
	$frontrailwastage = lookupMaterialWastageCost($siderailparams['frontrailmaterial'],$this->metal);
	$backrailwastage = lookupMaterialWastageCost($siderailparams['backrailmaterial'],$this->metal);
	$this->wastage = $frontrailwastage + $backrailwastage;
	return;
  }


  /**
   * SideRail - Installation Calculation. 
   */
  public function calculateSideRailInstallAllowance() {
	global $siderailparams;
	$this->installation = lookupMaterialInstallationCost("installcostperrail",$siderailparams['totallength'],$siderailparams['totalqty']);
	return;
  }

  /**
   * SideRail - Other. 
   */
  public function calculateSideRailOther() {
	$this->other = 0;
	return;
  }


  /**
   * SideRail - Margin. 
   */
  public function calculateSideRailMargin() {
	global $siderailparams;
	$frontrailmargin = lookupMaterialMargin($siderailparams['frontrailmaterial']);
	$backrailmargin = lookupMaterialMargin($siderailparams['backrailmaterial']);
	$percentagemargin = round(($frontrailmargin + $backrailmargin)/2,2);
	
	//Excluding Installation Margin
	$this->margin = $percentagemargin * ($this->metal + $this->powdercoating + $this->fabric + $this->processing + $this->wastage + $this->other);
	return;
  }
  
// ************************ End Frame SideRail *******************************

// ************************ Frame Posts *******************************

  /**
   * Post - Metal Calculation. 
   */
  public function calculatePostMetal() {
	$return_array = array();
	global $materiallist, $postparams;
	
	$return_array = dbLookup("sectiontag,side,pheight,support,postposition,postmaterial","fresco_costing_frontarray","");
	foreach ($return_array as $key => $value)
	{
		if (($return_array[$key]['postposition']<>'4') && ($return_array[$key]['side']<>'2') && ($return_array[$key]['side']<>'3'))
		{
			$postparams[$key]['sectiontag'] = $return_array[$key]['sectiontag'];
			$postparams[$key]['material'] = $return_array[$key]['postmaterial'];
			if($return_array[$key]['postposition']=='3')
			$postparams[$key]['postqty'] = '2';
			else
			$postparams[$key]['postqty'] = '1';
			$postparams[$key]['materiallength'] = round($return_array[$key]['pheight']/1000,2) * $postparams[$key]['postqty'];
			$postparams[$key]['cost'] = lookupMaterialCost($postparams[$key]['material'],$postparams[$key]['materiallength']);
			
		}
	}
	$sumArray = arraySumKey($postparams);
	$material_materiallength_Array = arrayGroupKey($postparams,'material','materiallength');
	$material_materialcost_Array = arrayGroupKey($postparams,'material','cost');

	$postparams['totalmateriallength']=$sumArray['materiallength'];
	$postparams['totalpostqty']=$sumArray['postqty'];
	$postparams['material_materiallength_Array']=$material_materiallength_Array;
	$postparams['material_materialcost_Array']=$material_materialcost_Array;
	
	$this->metal = $sumArray['cost'];

	return;
  }


  /**
   * Post - Powder Coating Calculation. 
   */
  public function calculatePostPowderCoating() {
	global $materiallist, $postparams;
	$material_powdercost_Array = array();
	
	// Loop by each material and its respective total length
	$material_materiallength_Array = $postparams['material_materiallength_Array']; 
	foreach ($material_materiallength_Array as $material => $materiallength)
	{
		// key = material; value = total material length
		$material_powdercost_Array[$material] = lookupMaterialPowderCoatingCost($material,$materiallength);
		$this->powdercoating += $material_powdercost_Array[$material]; 
	}
	$postparams['material_powdercost_Array'] = $material_powdercost_Array;
	return;
  }


  /**
   * Post - Fabric Calculation. 
   */
  public function calculatePostFabric() {
	$this->fabric = '0';
	return;
  }


  /**
   * Post - Processing Calculation. 
   */
  public function calculatePostProcessAllowance() {
	global $postparams;
	$material_processcost_Array = array();
	
	// Loop by each material and its respective total length
	$material_materiallength_Array = $postparams['material_materiallength_Array']; 
	foreach ($material_materiallength_Array as $material => $materiallength)
	{
		// key = material; value = total material length
		$material_processcost_Array[$material] = lookupMaterialProcessingCost($material,$materiallength);
		$this->processing += $material_processcost_Array[$material]; 
	}
	$postparams['material_processcost_Array'] = $material_processcost_Array;
	return;
  }


  /**
   * Post - Wastage Calculation. 
   */
  public function calculatePostWasteAllowance() {
	global $postparams;
	$material_wasteagecost_Array = array();
	
	$material_materialcost_Array = $postparams['material_materialcost_Array']; 
	foreach ($material_materialcost_Array as $material => $materialcost)
	{
		// key = material; value = total material length
		$material_wasteagecost_Array[$material] = lookupMaterialWastageCost($material,$materialcost);
		$this->wastage += $material_wasteagecost_Array[$material]; 
	}
	$postparams['material_wasteagecost_Array'] = $material_wasteagecost_Array;
	return;
  }


  /**
   * Post - Installation Calculation. 
   */
  public function calculatePostInstallAllowance() {
	global $postparams;
	
	$this->installation = lookupMaterialInstallationCost("installcostperpost",$postparams['totalmateriallength'],$postparams['totalpostqty']);
	return;
  }

  /**
   * Post - Other. 
   */
  public function calculatePostOther() {
	$this->other = 0;
	return;
  }


  /**
   * Post - Margin. 
   */
  public function calculatePostMargin() {
	global $postparams;
	$material_margin_Array = array();
	
	//Excluding Installation Margin
	$material_materialcost_Array = $postparams['material_materialcost_Array']; 
	$material_powdercost_Array = $postparams['material_powdercost_Array']; 
	$material_processcost_Array = $postparams['material_processcost_Array']; 
	$material_wasteagecost_Array = $postparams['material_wasteagecost_Array']; 
	
	
	foreach ($material_materialcost_Array as $material => $materialcost)
	{
		// key = material; value = total material length
		$material_margin_Array[$material] = lookupMaterialMargin($material) * ($materialcost + $material_powdercost_Array[$material] + $material_processcost_Array[$material] + $material_wasteagecost_Array[$material] + $this->other);
		$this->margin += $material_margin_Array[$material]; 
	}
	
	$postparams['material_margin_Array'] = $material_margin_Array;
	
	echo "<br>==================<br>";
		echo '<tt><pre>' . var_export($postparams, TRUE) . '</pre></tt>';
	echo "<br>==================<br>";
	return;
  }
  
// ************************ End Frame Posts *******************************


}