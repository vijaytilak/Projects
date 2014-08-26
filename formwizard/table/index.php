<?php $quoteid = 'abc'; ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  	<title>JQuery Form Wizard</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>
        <link rel="stylesheet" type="text/css" href="themes/metro/blue/jtable.css"  />
        <link rel="stylesheet" type="text/css" href="select2/select2.css" />
 		<link rel="stylesheet" type="text/css" href="codemine/css/general.css" /> 
    	<link href="themes/jMetro/css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="validationEngine/css/validationEngine.jquery.css" />
        
		<script type="text/javascript" src="jquery-2.0.3.min.js"></script>	
        <script type="text/javascript" src="jquery-ui.js"></script>
        <script type="text/javascript" src="codemine/js/jquery.form.js"></script>
        <script type="text/javascript" src="codemine/js/jquery.form.wizard.js"></script>
        <script type="text/javascript" src="codemine/js/jquery.validate.js"></script>
		<script type="text/javascript" src="jquery.jtable.js" ></script>
        <script type="text/javascript" src="select2/select2.min.js"></script>
        <script type="text/javascript" src="codemine/js/general.js"></script>
		<script type="text/javascript" src="validationEngine/js/jquery.validationEngine.js"></script>
        <script type="text/javascript" src="validationEngine/js/languages/jquery.validationEngine-en.js"></script>

 
 	<script>
	var quoteid = "<?php echo $quoteid; ?>";
	</script>
   
        
	</head>
  <body>
		<div id="demoWrapper">
			<h3>Fresco Quote Wizard</h3>
			<hr />
			<h5 id="status"></h5>
			<form id="demoForm" method="post" action="json.html" class="bbq">
				<div id="state"></div>
				<div id="fieldWrapper">
                <div id="table_message"></div>
				<div class="step" id="step0">
                	<?php include('step0.php'); ?>
				</div>
				<div class="step" id="step1">
                	<?php include('step1.php'); ?>
				</div>
				<div class="step" id="step2">
                	<?php include('step2.php'); ?>
				</div>				
				<div class="step" id="step3">
                	<?php include('step3.php'); ?>
				</div>				
				<div class="step" id="step4">
                	<?php include('step4.php'); ?>
				</div>				
				<div class="step" id="step5">
                	<?php include('step5.php'); ?>
				</div>				
                <div id="finland" class="step">
					<span class="font_normal_07em_black">Step 2 - Personal information</span><br />
					<label for="day_fi">Social Security Number</label><br />
					<input class="input_field_25em" name="day" id="day_fi" value="DD" />
					<input class="input_field_25em" name="month" id="month_fi" value="MM" />
					<input class="input_field_3em" name="year" id="year_fi" value="YYYY" /> - 
					<input class="input_field_3em" name="lastFour" id="lastFour_fi" value="XXXX" /><br />
					<label for="countryPrefix_fi">Phone number</label><br />
					<input class="input_field_35em" name="countryPrefix" id="countryPrefix_fi" value="+358" /> - 
					<input class="input_field_3em" name="areaCode" id="areaCode_fi" /> - 
					<input class="input_field_12em" name="phoneNumber" id="phoneNumber_fi" /><br />
					<label for="email">*Email</label><br />
					<input class="input_field_12em email required" name="myemail" id="myemail" /><br />	 						
				</div>
				<div id="confirmation" class="step submit_step">
					<span class="font_normal_07em_black">Last step - Username</span><br />
					<label for="username">User name</label><br />
					<input class="input_field_12em" name="username" id="username" /><br />
					<label for="password">Password</label><br />
					<input class="input_field_12em" name="password" id="password" type="password" /><br />
					<label for="retypePassword">Retype password</label><br />
					<input class="input_field_12em" name="retypePassword" id="retypePassword" type="password" /><br />
				</div>
				<div class="step" id="details">
					<span class="font_normal_07em_black">Hidden step</span><br />
					<span>This step is not possible to see without using the show method</span>
				</div>
				</div>
				<div id="demoNavigation"> 							
					<input class="navigation_button" id="back" value="Reset" type="reset" />
					<input class="navigation_button" id="next" value="Submit" type="submit" />
				</div>
			</form>
			<hr />
			
			<p id="data"></p>
		</div>

	</body>
<div class="modal" style="border:1px solid #000"><!-- Place at bottom of page --></div>
</html>
