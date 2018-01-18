<?php
header("Content-type: text/html; charset=windows-1251");
//header("Content-type: text/html; charset=utf-8");
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = 1;
}
/*
Функции, обезвреживающие вводимые данные пользователем
@param $var
@return string
*/
function sanitizeString($var) {
    $var = strip_tags($var);
    $var = htmlspecialchars($var);
    $var = stripslashes($var);
    //$var = mysql_real_escape_string($var);
    return $var;
}

function getAggregate($val) {
    return empty($val) ? "добавить" : "<span class='text'>$val</span>";
}

function DateUserToSQL($str){//выполняет преобразование даты в корректный формат, который хранится в MySql, принимает в качестве параметра строку в формате дд.мм.гггг, возвращает false, если строка не соответствует формату, либо саму дату если строка соответствует заданному формату
	$parts = explode('.', $str);
	$parts = array_reverse($parts);
	$parts = implode("-", $parts);
	return $parts;
}

if ($_SESSION["foreignersIsauth"] === 1) {
    require_once('config/configForeigners.php');
    require_once('../languages/lang_ru.php');
    require_once("../foreigns/foreignersList.php");
    $action = iconv('UTF-8', 'Windows-1251',@$_POST['action']);
    if(isset($_FILES["filename"])) $action = "importcsv";
    $id = iconv('UTF-8', 'Windows-1251',sanitizeString(@$_POST['id']));
    $fio = iconv('UTF-8', 'Windows-1251',sanitizeString(trim(@$_POST['fio'])));
	
    if(preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1]).(0[1-9]|1[0-2]).20([0-9]{2})$/', @$_POST['datu'])) {
        $originaldat = @$_POST['datu'];
        $dat = DateUserToSQL(@$_POST['datu']);
    }
    else {
        $originaldat = NULL;
        $dat = NULL;
    }
	
    if(preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1]).(0[1-9]|1[0-2]).20([0-9]{2})$/', @$_POST['datuhod'])) $dathod = DateUserToSQL($_POST['datuhod']);
    else $dathod = NULL;
	
    $dblink = mysql_connect($dbhost, $dbuname, $dbpass) or die("Не могу подключиться к базе");
    @mysql_select_db($dbname) or die("Не могу выбрать базу");
    mysql_query ("SET NAMES cp1251");
	
    switch($action) {
		
        case "before_importcsv":
        require_once('importcsv.php');
        echo csvImport::getForm();
        break;
		
        case "importcsv":
        require_once('importcsv.php');
        $output = false;
	
        try {
            $t = new csvImport($_FILES["filename"]["name"], $_FILES["filename"]["tmp_name"]);
            $t->writeContent();
        }
        catch(Exception $e) {
	        $output =  "<div class=\"error\">{$e->getMessage()}</div>" . csvImport::getForm();
        }
	
        echo $output;
        break;
		
        case "print_order":
        require_once('printOrder.php');
        $dataobj = new ForeignersList();
        $data = $dataobj->getForeign($id);
        $data['dekan'] = $dataobj->getDekan($data['adduserid']);
        $data['adduserid'] = $dataobj->getUserName($data['adduserid']);
        $print = new printOrder($data);
        echo $print->getContent();
        break;
		
        case "addForeigner":
        if(!empty($id)){
            $flist = new ForeignersList();
            $columns = "<tr id='{$id}'>{$flist->builtRowStages($flist->getForeign($id))}</tr>";
            echo $columns;
        }
        break;
		
        case "beforeadd":
        ob_start();
        require_once("tpl/fioForm.tpl");
        echo ob_get_clean();
        break;
		
        case "add":
        if(!empty($fio)) {
            $flist = new ForeignersList();
            $return = $flist->addForeign(array('fio' => $fio));
        }
		
        if(@!$return) {
            echo "<div class=\"error\">Введите корректные данные</div>";
            ob_start();
            require_once("tpl/fioForm.tpl");
            echo ob_get_clean();
        }elseif(is_int($return)){
            echo "{$return}";
        }
        break;
		
        case "del":
        if(!empty($id)){
            $flist = new ForeignersList();
            $return = $flist->updateForeign($id, array('removed' => 1));
            echo $return;
        }
        break;

        case "delFromArchiv":
            if(!empty($id)){
                $flist = new ForeignersList();
                $return = $flist->updateForeignAfterDel($id);
                echo $return;
            }
            break;
		
        case "before_updatefio":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $fio = $fio['fio'];
            ob_start();
            require_once("tpl/fioForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatefio":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('fio' => $fio));
            if($result) echo getAggregate($fio);
        }
        break;
		
        case "before_updatecountry":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $fio = $fio['country'];
            ob_start();
            require_once("tpl/fioForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatecountry":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('country' => $fio));
            if($result) echo getAggregate($fio);
        }
        break;
		
        case "before_updatestatus":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            echo "<form id=\"addForm\">" . $flist->getStatusSelect($fio['status']) . "</form>";
        }
        break;
		
        case "updatestatus":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('status' => $fio));
            if($result) echo $flist->getAggregate('status', $fio);
        }
        break;
		
        case "before_updateinvitation":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['invdate']) ? $dat = date("d.m.Y") : $fio['invdate'];
            $fio = $fio['inv'];
            $label = "Выписка приглашения на приезд";
            ob_start();
            require_once("tpl/fioForm.tpl");
            echo ob_get_clean();
        }
        break;


        case "updateinvitation":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('inv' => $fio));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;

        case "before_updateFormNumber":
            if(!empty($id)){
                $flist = new ForeignersList();
                $fio = $flist->getForeign($id);
                //$dat = empty($fio['invdate']) ? $dat = date("d.m.Y") : $fio['invdate'];
                $fio = $fio['formNumber'];
                $label = "Выписка приглашения на приезд";
                ob_start();
                require_once("tpl/fioForm.tpl");
                echo ob_get_clean();
            }
            break;


        case "updateFormNumber":
            if(!empty($id)){
                $flist = new ForeignersList();
                $result = $flist->updateForeign($id, array('formNumber' => $fio));
                if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
            }
            break;

        case "before_updateinvitationDate":
            if(!empty($id)){
                $flist = new ForeignersList();
                $fio = $flist->getForeign($id);
                $dat  = date("d.m.Y");
                ob_start();
                require_once("tpl/addFormDate.tpl");
                echo ob_get_clean();
            }
            break;


        case "updateinvitationDate":
            if(!empty($id)){
                $flist = new ForeignersList();
                $result = $flist->updateForeign($id, array('invdate' => $dat));
                if($result) echo "<span class='date'>{$originaldat}</span>";
            }
            break;

		
        case "before_updateres":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['resdate']) ? $dat = date("d.m.Y") : $fio['resdate'];
            $dathod = empty($fio['reshoddate']) ? date("d.m.Y") : $fio['reshoddate'];
            $fio = $fio['res'];
            $label = "Оформление временного пребывания";
            ob_start();
            require_once("tpl/resForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updateres":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('res' => $fio, 'resdate' => $dat, 'reshoddate' => $dathod));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updateedu":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['edudate']) ? $dat = date("d.m.Y") : $fio['edudate'];
            $fio = $fio['edu'];
            $label = "Договор на обучение";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updateedu":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('edu' => $fio, 'edudate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updatehor":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['hordate']) ? $dat = date("d.m.Y") : $fio['hordate'];
            $fio = $fio['hor'];
            $label = "Оформление ордера на проживание в общежитии";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatehor":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('hor' => $fio, 'hordate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updatehoc":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['hocdate']) ? $dat = date("d.m.Y") : $fio['hocdate'];
            $fio = $fio['hoc'];
            $label = "Подписание договора на проживание";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatehoc":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('hoc' => $fio, 'hocdate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updatezas":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['zasdate']) ? $dat = date("d.m.Y") : $fio['zasdate'];
            $fio = $fio['zas'];
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatezas":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('zas' => $fio, 'zasdate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updateisp":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['ispdate']) ? $dat = date("d.m.Y") : $fio['ispdate'];
            $fio = $fio['isp'];
            $label = "Регистрация договора на проживание в исполкоме";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updateisp":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('isp' => $fio, 'ispdate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updatemed":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['meddate']) ? $dat = date("d.m.Y") : $fio['meddate'];
            $fio = $fio['med'];
            $label = "Прохождение медосмотра1";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatemed":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('med' => $fio, 'meddate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updateenr":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['enrdate']) ? $dat = date("d.m.Y") : $fio['enrdate'];
            $fio = $fio['enr'];
            $label = "Приказ о зачислении";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updateenr":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('enr' => $fio, 'enrdate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;
		
        case "before_updatepet":
        if(!empty($id)){
            $flist = new ForeignersList();
            $fio = $flist->getForeign($id);
            $dat = empty($fio['petdate']) ? $dat = date("d.m.Y") : $fio['petdate'];
            $fio = $fio['pet'];
            $label = "Ходатайство на временное проживание";
            ob_start();
            require_once("tpl/addForm.tpl");
            echo ob_get_clean();
        }
        break;
		
        case "updatepet":
        if(!empty($id)){
            $flist = new ForeignersList();
            $result = $flist->updateForeign($id, array('pet' => $fio, 'petdate' => $dat));
            if($result) echo "<span class='date'>{$originaldat}</span>" . "<br>" .getAggregate($fio);
        }
        break;


        case "before_updateActionEndDate":
            if(!empty($id)){
                $flist = new ForeignersList();
                $fio = $flist->getForeign($id);
                $dat = date("d.m.Y");
                ob_start();
                require_once("tpl/addFormDate.tpl");
                echo ob_get_clean();
            }
            break;


        case "updateActionEndDate":
            if(!empty($id)){
                $flist = new ForeignersList();
                $result = $flist->updateForeign($id, array('actionEndDate' => $dat));
                if($result) echo "<span class='date'>{$originaldat}</span>";
            }
            break;

        case "before_updateWhoInvites":
            if(!empty($id)){
                $flist = new ForeignersList();
                $fio = $flist->getForeign($id);
                $fio = $fio['whoInvites'];
                ob_start();
                require_once("tpl/fioForm.tpl");
                echo ob_get_clean();
            }
            break;

        case "updateWhoInvites":
            if(!empty($id)){
                $flist = new ForeignersList();
                $result = $flist->updateForeign($id, array('whoInvites' => $fio));
                if($result) echo getAggregate($fio);
            }
            break;


        case "before_updateNote":
            if(!empty($id)){
                $flist = new ForeignersList();
                $fio = $flist->getForeign($id);
                $fio = $fio['note'];
                ob_start();
                require_once("tpl/fioForm.tpl");
                echo ob_get_clean();
            }
            break;

        case "updateNote":
            if(!empty($id)){
                $flist = new ForeignersList();
                $result = $flist->updateForeign($id, array('note' => $fio));
                if($result) echo getAggregate($fio);
            }
            break;

    }
	
    mysql_close($dblink);
}
else header("location: /error.php");//перенаправление

?>