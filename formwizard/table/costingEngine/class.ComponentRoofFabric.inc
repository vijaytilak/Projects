<?php

/**
 * Component - Frame. 
 */
class ComponentRoofFabric extends Component {
  /**
   * Initialization. 
   */
  protected function _init() {
    //Set Component Type based on the object class which called the method
	$this->_setComponentTypeId(Component::COMPONENT_TYPE_ROOF_FABRIC);
	
	//Set QuoteId 
	global $quoteid;
	$this->_quote_id=$quoteid;
  }
  
// ************************ Roof Fabric Rope Channel *******************************

  /**
   * SideRail - Metal Calculation. 
   * FrontRail + BackRail
   */
  public function calculateRopeChannelMetal() {
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
  public function calculateRopeChannelPowderCoating() {
	global $materiallist, $siderailparams;
	$frontrailpowdercoatingcost = lookupMaterialPowderCoatingCost($siderailparams['frontrailmaterial'],$siderailparams['totallength']);
	$backrailpowdercoatingcost = lookupMaterialPowderCoatingCost($siderailparams['backrailmaterial'],$siderailparams['totallength']);
	$this->powdercoating = $frontrailpowdercoatingcost + $backrailpowdercoatingcost;
	return;
  }


  /**
   * SideRail - Fabric Calculation. 
   */
  public function calculateRopeChannelFabric() {
	$this->fabric = '0';
	return;
  }


  /**
   * SideRail - Processing Calculation. 
   */
  public function calculateRopeChannelProcessAllowance() {
	global $siderailparams;
	$frontrailprocessingcost = lookupMaterialProcessingCost($siderailparams['frontrailmaterial'],$siderailparams['totallength']);
	$backrailprocessingcost = lookupMaterialProcessingCost($siderailparams['backrailmaterial'],$siderailparams['totallength']);
	$this->processing =$frontrailprocessingcost + $backrailprocessingcost;
	return;
  }


  /**
   * SideRail - Wastage Calculation. 
   */
  public function calculateRopeChannelWasteAllowance() {
	global $siderailparams;
	$frontrailwastage = lookupMaterialWastageCost($siderailparams['frontrailmaterial'],$this->metal);
	$backrailwastage = lookupMaterialWastageCost($siderailparams['backrailmaterial'],$this->metal);
	$this->wastage = $frontrailwastage + $backrailwastage;
	return;
  }


  /**
   * SideRail - Installation Calculation. 
   */
  public function calculateRopeChannelInstallAllowance() {
	global $siderailparams;
	$this->installation = lookupMaterialInstallationCost("installcostperrail",$siderailparams['totallength'],$siderailparams['totalqty']);
	return;
  }

  /**
   * SideRail - Other. 
   */
  public function calculateRopeChannelOther() {
	$this->other = 0;
	return;
  }


  /**
   * SideRail - Margin. 
   */
  public function calculateRopeChannelMargin() {
	global $siderailparams;
	$frontrailmargin = lookupMaterialMargin($siderailparams['frontrailmaterial']);
	$backrailmargin = lookupMaterialMargin($siderailparams['backrailmaterial']);
	$percentagemargin = round(($frontrailmargin + $backrailmargin)/2,2);
	
	//Excluding Installation Margin
	$this->margin = $percentagemargin * ($this->metal + $this->powdercoating + $this->fabric + $this->processing + $this->wastage + $this->other);
	return;
  }
  
// ************************ End Roof Fabric Rope Channel *******************************
  
  
}