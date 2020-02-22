<?php
foreach ($_POST as $key => $value) {
	$_SESSION['req']->body[$key]=$value;
}
?>