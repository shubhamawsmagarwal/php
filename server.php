<?php
function req_uire($var){
	require_once 'packages/'.$var.'.php';
	return true;
}
?>