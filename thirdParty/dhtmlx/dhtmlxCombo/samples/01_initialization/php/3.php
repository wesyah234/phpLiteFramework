<?php
header("Content-type:text/xml");
print("<?xml version=\"1.0\"?>");
echo "<complete>";
echo '<option value="1">c1_'.$_GET["parent"].'</option>
	<option value="2">c2_'.$_GET["parent"].'</option>
	<option value="3">c3_'.$_GET["parent"].'</option>
	<option value="4">c4_'.$_GET["parent"].'</option>
	<option value="5">c5_'.$_GET["parent"].'</option>
	<option value="6">c6_'.$_GET["parent"].'</option>
	<option value="7">c7_'.$_GET["parent"].'</option>
	<option value="8">c8_'.$_GET["parent"].'</option>
	<option value="9">c9_'.$_GET["parent"].'</option>';
echo "</complete>";

?>