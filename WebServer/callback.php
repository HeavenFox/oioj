<?php
ob_start();
var_dump($_POST);
file_put_contents("test.txt",ob_get_contents());

?>