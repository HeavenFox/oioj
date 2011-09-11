<?php
require_once 'classes/JudgeRecord.php';

$record = new JudgeRecord;
$record->lang = $_POST['lang'];
$record->problemID = intval($_POST['pid']);
$record->recordID = 1;
$record->setSubmission($_POST['code']);

define('SOCKET_ADDRESS', 'tcp://192.168.1.102:9458');

$fp = stream_socket_client(SOCKET_ADDRESS, $errono, $errorstr, 30);

if (!$fp)
{
    die("Error ". $errorstr);
}

echo strval($record);

fwrite($fp, strval($record));
fclose($fp);
?>