<?php
ini_set('display_errors','1');
//
// ���������� ����������� � ����
//
//

$dbhost = "localhost";
$dbuname = "root";
$dbpass = "";
$dbname = "foreign";

//
// ���������� �������� �����
//
//

$hostName = "http://".$_SERVER['HTTP_HOST']; //��� �� ������ ��������� �������� http://www.microsoft.com
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$title = '����������� �����������';
$manual = '����������';
$go=$_GET['gofor'];
//$go=$_GET['csvexport'];
?>