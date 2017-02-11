<?php
ini_set('display_errors','1');
//$go="gofor";
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = 1;
}
require_once("config/configForeigners.php");

$dblink = mysql_connect($dbhost, $dbuname, $dbpass) or die("Не могу подключиться к базе");
@mysql_select_db($dbname) or die("Не могу выбрать базу");
mysql_query ("SET NAMES 'cp1251'");
$content = null;
$search = null;
//echo "sdcsdc $go";
//$go="foreignersList";

switch($go){
		
//-------------------------------------------------------------------------------------begin foreigners
    case "manual":
        $content .= file_get_contents('tpl/manual.tpl');
    break;
	
    case "foreigners":
        if($_SESSION["foreignersIsauth"] === 1)
            header("Location: /foreigners/");
        require_once('checkAccess.php');
        try{
        $ac = new checkAccess();
        $ac->getAccess();
        } catch (Exception $e){
            unset($_SESSION["foreignersUserid"], $_SESSION["foreignersIsauth"]);
            header("location: /error.php");
        }
    break;
	
    case "foreignersList":
        if ($_SESSION["foreignersIsauth"] === 1) {
            require_once("foreignersList.php");
            $list = new ForeignersList();
            if(isset($_GET['reset']))
            $list->resetSearch();
            if(isset($_GET['search'])) $_SESSION['foreignersSearch'] = $list->sanitizeString(iconv('UTF-8', 'Windows-1251',$_GET['search']));
            $search = $list->searchForm();
            $content .= $list->getContent();
        }
        else header("Location: ".$hostName."/foreigners/login");
    break;
	
    case "csvexport":
        if ($_SESSION["foreignersIsauth"] === 1) {
            require_once('csv.php');
            $mode = 0;
            $maxMode = CsvExport::maxMode();
            $file_name = md5(uniqid(rand(), true));
            if(isset($_GET['csvmode']) && preg_match("/^[0-{$maxMode}]$/", $_GET['csvmode'])) $mode = $_GET['csvmode'];
            $file = new CsvExport("../web/{$file_name}.csv", $mode);
            $file->write();
        }
        else header("Location: ".$hostName."/foreigners/login");
    break;
//end foreigners------------------------------------------------------------------------------------------
		
    default:
        //header("location: /error.php");
    break;
	
 }
 
mysql_close($dblink);
	
require_once("ruleURL.php");
$checker = new replaceUrl($content);
$content = $checker->check();
$checker = new replaceUrl($search);
$search = $checker->check();

?>

<HTML xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE><?php echo $title;?></TITLE>

<META HTTP-EQUIV="Content-Type" CONTENT="text/html; windows-1251">
<META http-equiv="X-UA-Compatible" content="IE=9">
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=cyrillic-ext" rel="stylesheet" type="text/css">
<link href="/foreigners_main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/datepickerForeigners-ru.js"></script>
<script type="text/javascript" src="/js/foreignsScript.js" ></script>
<script type="text/javascript" src="/js/fixedthead.js" ></script>
<!--[if lt IE 9]>

<link rel="stylesheet" type="text/css" href="/style_IE.css" media="all"></link>

<![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

</HEAD>
<BODY>

<div id="header">
    <div class="logo"><a title="Главная страница" href="/foreigners/"></a></div>
    <div class="search"><?php echo $search;?></div>
</div>

<div class="name"><span><?php echo $title;?></span><a class="manual" href="/foreigners/manual"><?php echo $manual;?></a></div>

<div id="content">
    <?php echo $content;?>
</div>

<div id="footer">
</div>

</BODY>
</HTML>