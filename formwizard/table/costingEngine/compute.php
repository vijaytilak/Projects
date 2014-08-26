<link rel="stylesheet" type="text/css" href="../codemine/css/general.css" /> 

<?php
	//Global Variables
	$quoteid = "abc";
	$materiallist = array();
	$archparams = array();
	$bracketparams = array();
	$connectorparams = array();
	$cbparams = array();
	
	//Helper Functions
	include 'helper.php';
	
	//Display Error
	ini_set('display_errors', 1);
	
	//Instantiating Components
	$arch = new ComponentFrame(array(
	   'component_id' => '1',
	   'component_name' => 'Arch',
	));
	$bracket = new ComponentFrame(array(
	   'component_id' => '2',
	   'component_name' => 'Bracket',
	));
	$connector = new ComponentFrame(array(
	   'component_id' => '3',
	   'component_name' => 'Connector',
	));
	$cb = new ComponentFrame(array(
	   'component_id' => '4',
	   'component_name' => 'Centre Brace',
	));
	$siderail = new ComponentFrame(array(
	   'component_id' => '5',
	   'component_name' => 'SideRails',
	));
	$post = new ComponentFrame(array(
	   'component_id' => '6',
	   'component_name' => 'Posts',
	));
	
	// Order of function call is important - Brackets depends on arches
	$arch->calculateArchesMetal();
	$arch->calculateArchesPowderCoating();
	$arch->calculateArchesFabric();
	$arch->calculateArchesProcessAllowance();
	$arch->calculateArchesWasteAllowance();
	$arch->calculateArchesInstallAllowance();
	$arch->calculateArchesOther();
	$arch->calculateArchesMargin();
	echo '<tt><pre>' . var_export($arch, TRUE) . '</pre></tt>';
	
	$bracket->calculateBracketsMetal();
	$bracket->calculateBracketsPowderCoating();
	$bracket->calculateBracketsFabric();
	$bracket->calculateBracketsProcessAllowance();
	$bracket->calculateBracketsWasteAllowance();
	$bracket->calculateBracketsInstallAllowance();
	$bracket->calculateBracketsOther();
	$bracket->calculateBracketsMargin();
	echo '<tt><pre>' . var_export($bracket, TRUE) . '</pre></tt>';
	
	$connector->calculateConnectorsMetal();
	
	$cb->calculateCBMetal();
	$cb->calculateCBPowderCoating();
	$cb->calculateCBFabric();
	$cb->calculateCBProcessAllowance();
	$cb->calculateCBWasteAllowance();
	$cb->calculateCBInstallAllowance();
	$cb->calculateCBOther();
	$cb->calculateCBMargin();
	
	$siderail->calculateSideRailMetal();
	$siderail->calculateSideRailPowderCoating();
	$siderail->calculateSideRailFabric();
	$siderail->calculateSideRailProcessAllowance();
	$siderail->calculateSideRailWasteAllowance();
	$siderail->calculateSideRailInstallAllowance();
	$siderail->calculateSideRailOther();
	$siderail->calculateSideRailMargin();
	
	$post->calculatePostMetal();
	$post->calculatePostPowderCoating();
	$post->calculatePostFabric();
	$post->calculatePostProcessAllowance();
	$post->calculatePostWasteAllowance();
	$post->calculatePostInstallAllowance();
	$post->calculatePostOther();
	$post->calculatePostMargin();
	
	
	
	
	
	
	
	
	
	
	
	echo '<tt><pre>' . var_export($post, TRUE) . '</pre></tt>';
	//echo '<tt><pre>' . var_export($siderail, TRUE) . '</pre></tt>';
	//echo '<tt><pre>' . var_export($bracket, TRUE) . '</pre></tt>';

/*$componentdb = Component::load(1);
echo '<tt><pre>' . var_export($componentdb, TRUE) . '</pre></tt>';
*/



