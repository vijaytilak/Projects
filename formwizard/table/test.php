<html>
<head>
<title>- jsFiddle demo</title>
<link rel="stylesheet" type="text/css" href="select2/select2.css" />
<script type="text/javascript" src="jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="select2/select2.min.js"></script>
<style type='text/css'></style>
<script type='text/javascript'>//<![CDATA[
$(document).ready(function () {
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=getFrontRailMaterialDropdown', // JQuery loads serverside.php
				  data: '',
				  dataType: 'json', // Choosing a JSON datatype
				  async: false, // Synchronous AJAX REQUESTTTTTTTTTTTTTTTTTTTTTTTT
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					sdata = data;
				
				  }
			  });


	
	$(function() {
		var el = $('#archmaterial');
		var defaultchoice = 1; // save current value
		var newOptions = '';
		
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=getArchMaterialDropdown', // JQuery loads serverside.php
		  data: '',
		  dataType: 'json', // Choosing a JSON datatype
		  success: function(data) // Variable data contains the data we get from serverside
		  {
			// ArchMaterial Drop Down
				for (var key in data) {
				  if (data.hasOwnProperty(key)) {
					newOptions += '<option value="'+data[key]['id']+'">'+data[key]['text']+'</option>';
					
				  }
				}
			el.select2('destroy').html(newOptions).select2();
			el.select2('val',defaultchoice);
			
		
			el.select2().on('change', function() {
			
				$('#frontrailmaterial').removeClass('select2-offscreen').select2({
					data:sdata[$(this).val()-1],
					width:'140'
				});
			
			}).trigger('change');
			
		  }
	  });
	});
	
	

		
});//]]>

</script>
</head>
<body>
Attribute:
<select name="archmaterial" id="archmaterial">
  <option value="a">asd</option>
  <option value="s">asd</option>
  <option value="d">sd0</option>
</select>
Value:
<input id="frontrailmaterial" type="hidden" style="width:300px"/>
</body>
</html>
