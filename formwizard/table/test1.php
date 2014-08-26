<html>
<head>
<title>- jsFiddle demo</title>
<link rel="stylesheet" type="text/css" href="select2/select2.css" />
<script type="text/javascript" src="jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="select2/select2.min.js"></script>
<style type='text/css'></style>
<script type='text/javascript'>//<![CDATA[
$(document).ready(function () {
		var defaultchoice = 2; // save current value
		var el = $('#archmaterial');
		var newOptions = '';

		
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=getArchMaterialDropdown', // JQuery loads serverside.php
		  data: '',
		  async: false,
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
			
					var sdata = [
					[{"id":"1","text":"51R-1.6"},{"id":"2","text":"51R-1.6-RF"}],
					[{"id":"3","text":"52R-1.6"},{"id":"4","text":"51R-1.6-RF"}],
					[{"id":"5","text":"69R-2.0"}]
					]
			
			$(function() {
				el.select2().on('change', function() {
					$('#frontrailmaterial').removeClass('select2-offscreen').select2({
						data:sdata[$(this).val()-1],
						width:'140'
					});
				}).trigger('change');
			});
			
			
		  }
	  });
		
			el.select2('val',defaultchoice);
			alert(defaultchoice);

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

										$("#frontrailmaterial").select2("val", "1");
										$("#backrailmaterial").select2("val", "1");
										$("#archmaterial").select2("val",($('#archmaterial option:first-child').val()));

