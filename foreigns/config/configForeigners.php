<?php
ini_set('display_errors','1');
//
// переменные подключения к базе
//
//

$dbhost = "localhost";
$dbuname = "root";
$dbpass = "";
$dbname = "foreign";

//
// переменные настроек сайта
//
//

$hostName = "http://".$_SERVER['HTTP_HOST']; //или же жестко прописать например http://www.microsoft.com
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$title = 'Регистрация иностранцев';
$manual = 'Инструкция';
$go=$_GET['gofor'];
//$go=$_GET['csvexport'];
?>