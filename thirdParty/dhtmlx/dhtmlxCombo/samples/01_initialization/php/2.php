<?php
header("Content-type:text/xml");
print("<?xml version=\"1.0\"?>");
echo "<complete>";
echo '<option value="1">b1_'.$_GET["parent"].'</option>
	<option value="2">b2_'.$_GET["parent"].'</option>
	<option value="3">b3_'.$_GET["parent"].'</option>
	<option value="4">b4_'.$_GET["parent"].'</option>
	<option value="5">b5_'.$_GET["parent"].'</option>
	<option value="6">b6_'.$_GET["parent"].'</option>
	<option value="7">b7_'.$_GET["parent"].'</option>
	<option value="8">b8_'.$_GET["parent"].'</option>
	<option value="9">b9_'.$_GET["parent"].'</option>';
echo "</complete>";

?>
