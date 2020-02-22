<?php
function sql(){
    $sql=new database();
	return $sql;
}
class database{
	public $databaseName;
	public function connect($name){
		$this->databaseName=$name;
		@$arr=explode("://", $name);
		@mysql_connect($arr[0],$arr[1],"") or die('error connecting to database');
		@mysql_select_db($arr[2]) or die('error connecting to database');
	}
	public function queryNumRow($query){
		if(@$queryResult=mysql_query($query))
			return mysql_num_rows($queryResult);
		else
			die("some error occured");
	}
	public function queryInsert($query){
		if(@$queryResult=mysql_query($query))
			return 1;
		else
			die("some error occured");

	}
}
?>  