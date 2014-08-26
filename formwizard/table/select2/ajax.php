<?php

			
/*		$searchid = $_POST['searchid'];
		$con = mysql_connect('localhost', 'root', 'passw0rd');
		if (!$con)
		  {
		  die('Could not connect: ' . mysql_error());
		  }
		
		mysql_select_db("vtigercrm", $con);
		
		$sql="SELECT max_arch_spacing,max_post_spacing,centre_brace_qty,arch_material FROM fresco_archtable where `archlength`='".$length."'";
		
		$result = mysql_query($sql);
		
		while($row = mysql_fetch_array($result))
		  {
		 
		  $max_arch_spacing=$row['max_arch_spacing'];
		  $max_post_spacing=$row['max_post_spacing'];
		  $centre_brace_qty=$row['centre_brace_qty'];
		  $arch_material=$row['arch_material'];
		
		  }
		
		mysql_close($con);
*/	
	$return_arr =array();	
	$searchid = $_GET['searchid'];
	if($searchid=='0') {	
	$row_array['id'] = 0;
    $row_array['text'] = 'vijay';
    array_push($return_arr,$row_array);
	}
	else {
	$row_array['id'] = 1;
    $row_array['text'] = 'me';
    array_push($return_arr,$row_array);
	}

echo json_encode($return_arr);



	?>
