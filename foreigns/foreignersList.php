<?php
function updateCallBack (&$item, $key) {
    if(empty($item)) $item = "{$key} = NULL";
    else $item = "{$key} = '{$item}'";
}

class ForeignersList {
    private $_table = 'iws_foreigners'; //Имя таблицы с иностранцами
    private $_fields = array('id' => 'NULL','fio' => NULL,'adduserid' => 'NULL', 'inv' => 'NULL','invdate' => 'NULL', 'formNumber' => 'NULL', 'res' => 'NULL','resdate' => 'NULL','reshoddate' => 'NULL','edu' => 'NULL', 'edudate' => 'NULL', 'hor' => 'NULL',
        'hordate' => 'NULL','hoc' => 'NULL','hocdate' => 'NULL','zas' => 'NULL','zasdate' => 'NULL','isp' => 'NULL','ispdate' => 'NULL','med' => 'NULL','meddate' => 'NULL','enr' => 'NULL','enrdate' => 'NULL','pet' => 'NULL','petdate' => 'NULL', 'removed' => 0,
        'country' => 'NULL', 'status' => 'NULL', 'depart' => 'NULL', 'whoInvites' => 'NULL', 'actionEndDate' => 'NULL', 'note' => 'NULL');
    private $_rules = array('id' =>array(1, 6, 8, 9, 17, 21), 'fio' => array(1, 6, 8, 9, 12, 17, 21), 'depart' => array(1, 6, 8, 9, 12, 17, 21), 'formNumber' => array(1, 6, 8, 9, 12, 17, 21), 'country' => array(1, 6, 8, 9, 12, 17, 21),
        'inv' => array(1, 6, 8, 9, 12, 17, 21), 'invdate' => array(1, 6, 8, 9, 12, 17, 21),'res' => array(1, 6, 8, 9, 14, 15, 16, 17, 21), 'edu' => array(2, 8, 9, 17, 21), 'hor' => array(1, 6, 8, 9, 17, 21), 'hoc' => array(3, 8, 9, 17, 21),
        'zas' => array(3, 8, 9, 17, 21), 'isp' => array(6, 8, 9, 14, 15, 16, 17, 21), 'med' => array(4, 8, 9, 17, 21), 'enr' => array(1, 6, 8, 9, 13, 17, 21), 'pet' => array(1, 6, 8, 9, 14, 15, 16, 17, 21), 'status' => array(1, 6, 8, 9, 17, 21),
        'whoInvites' => array(1, 6, 8, 9, 12, 17, 21),'actionEndDate' => array(1, 6, 8, 9, 12, 17, 21),'note' => array(1, 6, 8, 9, 12, 17, 21));
    private $_sort = array('fio', 'invdate', 'resdate', 'edudate', 'hordate', 'hocdate', 'ispdate', 'meddate', 'enrdate', 'petdate', 'country', 'status', 'zasdate', 'adduserid', 'inv', 'formNumber', 'whoInvites', 'actionEndDate', 'note');
    private $_classes = array('fio' => 'fio', 'depart' => 'depart', 'country' => 'country', 'invdate' => 'invitDate', 'inv' => 'invitation', 'formNumber' => 'formNumber', 'res' => 'residence', 'edu' => 'educationcontract', 'hor' => 'hostelorder', 'hoc' => 'hostelcontract',
        'zas' => 'zaselenie', 'isp' => 'ispolkom', 'med' => 'medicalcheckup', 'enr' => 'enrollment','status' => 'status', 'whoInvites' => 'whoInvites', 'actionEndDate' => 'actionEndDate', 'note' => 'note');
	
    private $outside = array(1, 8, 9);// users имеющие отдельные списки
    private $dekan_rule = array(1, 6, 21);
    private $status_PO = 'foreigners_statusPO';//думаю убрать
    private $status_table = 'foreigners_status';
    private $users_table = 'foreigners_users';

    const IURL = '/foreigns/index.php?gofor=foreignersList';//Адрес текущей страницы
    const AURL = '/foreigns/index.php?gofor=foreigners';//Адрес страницы авторизации
    const SORTUP = 'ASC';//направление сортировки данных - прямое
    const SORTDOWN = 'DESC';//направление сортировки данных - обратное
	
    public function __construct()
    {
        if($_SESSION["foreignersUserid"] == 1) $this->status_table = $this->status_PO;
    }
    /*
	  Функции, обезвреживающие вводимые данные пользователем
	  @param $var
	  @return string
	 */
    public function sanitizeString($var) {
        $var = strip_tags($var);
        //$var = htmlentities($var);
        $var = stripslashes($var);
        $var = mysql_real_escape_string($var);
        return $var;
    }
	
	
    private function qry($query)
    {
        return mysql_query($query);
    }
	
    public function getAggregate($key, $val)
    {
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;
		
        if($key == 'status') $val = $this->getStatusName($val);
       // elseif($key == 'depart') $val = $this->getDepartment($key, $val);
        //elseif($key == 'fio') $val .= $this->getUserName();

        if($key!=='depart')
            $text = ($archiv === 1) ? '' : 'добавить';
		
        return empty($val) ? $text : "<span class='text'>{$val}</span>";
    }
	
    public function getUserName($id)
    {
        //if(!in_array($_SESSION["foreignersUserid"],$this->_rules['fio'])) return false;
		
        $sql = $this->qry("SELECT name FROM {$this->users_table} WHERE id='{$id}'");
		
        if(mysql_num_rows($sql) === 1) {
            $row = mysql_fetch_assoc($sql);
            return $row['name'];
        }
        else return false;
    }
	
    public function getDekan($id)
    {
        if(!in_array($_SESSION["foreignersUserid"],$this->dekan_rule)) return false;
		
        $sql = $this->qry("SELECT dekan FROM {$this->users_table} WHERE id='{$id}'");
		
        if(mysql_num_rows($sql) === 1) {
            $row = mysql_fetch_assoc($sql);
            return $row['dekan'];
        }
        else return false;
    }
	
    private function getDepartment($id, $key)
    {
        if($key == 'depart') return "<span class='department'>{$this->getUserName($id)}</span";
    }
	
    private function partDirection($direction)
    {
        return $direction === self::SORTUP ? '&direction='.self::SORTDOWN : '';
    }
	
    public function resetSort()
    {
        return "<div class='resetsort'><a href='".self::IURL."&reset=1'>Сбросить сортировки и результаты поиска</a></div>";
    }
	
    public function archiv()
    {
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;
		
        $result = "<div class='list " . ($archiv === 0 ? 'activ' : '') . "'><a href='" . self::IURL . "&archiv=0/'>Список</a></div>" .
            "<div class='archiv " . ($archiv === 1 ? 'activ' : '') . "'><a href='" . self::IURL . "&archiv=1/'>Архив</a></div>";
		
        return $result;
    }
	
    public function resetSearch()
    {
        unset($_SESSION['foreignersSearch']);
    }
	
    private function getStatusName($id)
    {
        $sql = $this->qry("SELECT name FROM {$this->status_table} WHERE id='{$id}'");
		
        if(mysql_num_rows($sql) === 1) {
            $row = mysql_fetch_assoc($sql);
            return $row['name'];
        }
        else return false;
    }
	
    public function getAggregateStatus($key, $val)
    {
        
        if($key == 'status') $val[$key] = $this->getStatusName($val[$key]);
		
        return empty($val[$key]) ? '' : "<span class='text'>{$val[$key]}</span>";
    }
	
    public function getStatusSelect($sel = 1)
    {
        $sql = $this->qry("SELECT * FROM {$this->status_table}");
        $select = "<select name='fio' id='status'>";
        while ($row = mysql_fetch_assoc($sql)) {
            $selected = $sel == $row['id'] ? 'selected' : '';
            $select .= "<option {$selected} value='{$row['id']}'>{$row['name']}</option>";
        }
        $select .= "</select>";
        return $select;
    }
	
    public function ClassDel()
    {
        $result = '';
        if(in_array($_SESSION["foreignersUserid"],$this->_rules['fio']))
            $result = "<br><span class='del' title='Перенести в архив'></span>";
		
        if(in_array($_SESSION["foreignersUserid"],$this->dekan_rule))
            $result .= "<span class='print' title='Печать ордера на жилое помещение в общежитии'></span>";
		
        return $result;
    }
    public function ClassDelFromArchiv()
    {
        $result = '';
        if(in_array($_SESSION["foreignersUserid"],$this->_rules['fio']))
            $result = "<br><span class='delFromArchiv' title='Удалить из БД'></span>";

        return $result;
    }
	
    public function ClassAdd()
    {
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;
        $result = '';
        if(in_array($_SESSION["foreignersUserid"],$this->_rules['fio']) && ($archiv === 0)) $result = "<div class='fadd' title='Добавить'></div>";
        return $result;
    }
	
    public function getExport()
    {
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;
        $result = '';
        if(in_array($_SESSION["foreignersUserid"],$this->dekan_rule) && ($archiv === 0)) $result .= "<div class='importcsv'>Импорт csv файла</div>";
        if(in_array($_SESSION["foreignersUserid"],$this->_rules['fio']) && ($archiv === 0)) $result .= "<div class=\"csv\">Экспорт в excel
            <div class='csvInto'><ul><li><a href='/foreigns/index.php?gofor=csvexport'>Все</a></li>
            <li><a href='/foreigns/index.php?gofor=csvexport&csvmode=1'>Список {$this->getUserName($_SESSION["foreignersUserid"])}</a></li>
            <li><a href='/foreigns/index.php?gofor=csvexport&csvmode=2'>Все студенты</a></li>
            <li><a href='/foreigns/index.php?gofor=csvexport&csvmode=3'>Все не поступившие</a></li>
            </ul></div></div>";
        return $result;
    }
	
    protected function getDescribe($key, $val)
    {
        if($key == 'fio' || $key == 'country' || $key == 'status' || $key == 'depart' || $key == 'inv') $result = "";
        else $result = "<span class='date'>{$val[$key.'date']}</span><br>";
		
        return $result;
    }

    public function builtRowStages($row = array())
    {
        $result = null;
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;

        foreach($this->_classes as $key => $value) {
			if(in_array($_SESSION["foreignersUserid"], $this->_rules[$key]) && ($archiv === 0)) $result .= "<td class='{$value}td'><span class='{$value}'>{$this->getDescribe($key, $row)}{$this->getAggregate($key, $row[$key])}</span>{$this->getDepartment($row['adduserid'], $key)}</td>"; // позв редактировать
            elseif(in_array($_SESSION["foreignersUserid"], $this->_rules[$key]) && ($archiv === 1)) $result .= "<td class='{$value}td'><span>{$this->getDescribe($key, $row)}{$this->getAggregate($key, $row[$key])}</span>{$this->getDepartment($row['adduserid'], $key)}</td>";
            else  $result .= "<td class='{$value}td'><span>{$this->getDescribe($key, $row)}{$this->getAggregateStatus($key, $row)}</span>{$this->getDepartment($row['adduserid'], $key)}</td>";
        }
		
        if($archiv === 0) $result .= "<td style='background:#fff;'>{$this->classDel()}</td>";
        elseif ($archiv===1) $result.="<td style='background:#fff;'>{$this->classDelFromArchiv()}</td>";

        return $result;
    }
	
    public function getForeign($id)
    {
        $query = $this->qry("SELECT id,fio, formNumber, status,country,adduserid,inv,DATE_FORMAT(invdate,'%d.%m.%Y') invdate,res,DATE_FORMAT(resdate,'%d.%m.%Y') resdate,DATE_FORMAT(reshoddate,'%d.%m.%Y') reshoddate,edu,DATE_FORMAT(edudate,'%d.%m.%Y') edudate,
            hor,DATE_FORMAT(hordate,'%d.%m.%Y') hordate,hoc,DATE_FORMAT(hocdate,'%d.%m.%Y') hocdate,zas,DATE_FORMAT(zasdate,'%d.%m.%Y') zasdate,isp,DATE_FORMAT(ispdate,'%d.%m.%Y') ispdate,med,DATE_FORMAT(meddate,'%d.%m.%Y') meddate,enr,
            DATE_FORMAT(enrdate,'%d.%m.%Y') enrdate,pet,DATE_FORMAT(petdate,'%d.%m.%Y') petdate,IF((DATEDIFF(CURDATE(),resdate)>=5 AND (res IS NULL)),1,0) as attention, whoInvites, actionEndDate, note
            FROM {$this->_table} WHERE id='{$id}'");
        $row = (mysql_num_rows($query) === 1) ? mysql_fetch_assoc($query) : array();
        return $row;
    }

    public function getAllForeigns($attention, $sort = 'fio', $direction = 'ASC')
    {
        $where = null;
        $this->csv .= "\r\n";
        $archiv = isset($_SESSION['archiv']) ?  $_SESSION['archiv'] : 0;
        $attention = $attention ?  '' : 'attention DESC,';
		
        if(in_array($_SESSION["foreignersUserid"], $this->outside)) $where = "AND adduserid = '{$_SESSION["foreignersUserid"]}' ";
		//тут поиск
        if(isset($_SESSION['foreignersSearch'])) $where .= "AND fio LIKE '%{$_SESSION['foreignersSearch']}%' OR country LIKE '%{$_SESSION['foreignersSearch']}%' OR res LIKE '%{$_SESSION['foreignersSearch']}%' OR formNumber LIKE '%{$_SESSION['foreignersSearch']}%' OR whoInvites LIKE '%{$_SESSION['foreignersSearch']}%' OR enr LIKE '%{$_SESSION['foreignersSearch']}%' OR pet LIKE '%{$_SESSION['foreignersSearch']}%' 
        OR inv LIKE '%{$_SESSION['foreignersSearch']}%' OR edu LIKE '%{$_SESSION['foreignersSearch']}%' OR hor LIKE '%{$_SESSION['foreignersSearch']}%' OR hoc LIKE '%{$_SESSION['foreignersSearch']}%' OR zas LIKE '%{$_SESSION['foreignersSearch']}%' OR isp LIKE '%{$_SESSION['foreignersSearch']}%' OR med LIKE '%{$_SESSION['foreignersSearch']}%'  OR note LIKE '%{$_SESSION['foreignersSearch']}%'";
        $return = "<table id='foreigners' class=\"table_col\">
        <thead><tr>
            <th>
                <a>№</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=0{$this->partDirection($direction)}'>ФИО</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=13{$this->partDirection($direction)}'>Отдел</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=10{$this->partDirection($direction)}'>Страна</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=1{$this->partDirection($direction)}'>Дата согласования пришлашения в ОГиМ</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=14{$this->partDirection($direction)}'>Кто ходатайствует о приглашении</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=15{$this->partDirection($direction)}'>№ бланка приглашения</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=2{$this->partDirection($direction)}'>Оформление временного пребывания</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=3{$this->partDirection($direction)}'>Договор на обучение</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=4{$this->partDirection($direction)}'>Оформление ордера на проживание</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=5{$this->partDirection($direction)}'>Договор на проживание</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=12{$this->partDirection($direction)}'>Приказ на заселение</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=6{$this->partDirection($direction)}'>Регистрация</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=7{$this->partDirection($direction)}'>Прохождение медосмотра</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=8{$this->partDirection($direction)}'>Приказ о зачислении</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=11{$this->partDirection($direction)}'>Статус</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=16{$this->partDirection($direction)}'>Структурное подразделение, выдавшее приглашение</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=17{$this->partDirection($direction)}'>Дата окончания действия приглашения</a>
            </th>
            <th>
                <a href='" . self::IURL . "&sort=18{$this->partDirection($direction)}'>Примечание</a>
            </th>
        </tr></thead>
        <tbody>";
        $query = $this->qry("SELECT id,fio,status, formNumber,country,adduserid,inv,DATE_FORMAT(invdate,'%d.%m.%Y') invdate,res,DATE_FORMAT(resdate,'%d.%m.%Y') resdate,DATE_FORMAT(reshoddate,'%d.%m.%Y') reshoddate,edu,DATE_FORMAT(edudate,'%d.%m.%Y') edudate,
            hor,DATE_FORMAT(hordate,'%d.%m.%Y') hordate,hoc,DATE_FORMAT(hocdate,'%d.%m.%Y') hocdate,zas,DATE_FORMAT(zasdate,'%d.%m.%Y') zasdate,isp,DATE_FORMAT(ispdate,'%d.%m.%Y') ispdate,med,DATE_FORMAT(meddate,'%d.%m.%Y') meddate,enr,
            DATE_FORMAT(enrdate,'%d.%m.%Y') enrdate,pet,DATE_FORMAT(petdate,'%d.%m.%Y') petdate,IF((DATEDIFF(CURDATE(),resdate)>=5 AND (res IS NULL)),1,0) as attention, whoInvites, actionEndDate, note
            FROM {$this->_table} WHERE removed = '{$archiv}' {$where} ORDER BY {$attention} {$this->_table}.{$sort} {$direction}");

        $counterForegines=0;
        while ($row = mysql_fetch_assoc($query)) {
            $counterForegines++;
           // print_r($row);
            $class = $row['attention'] == 1 ? "attention": "";
            $return .= "<tr id='{$row['id']}' class=\"{$class}\"><td>$counterForegines</td>{$this->builtRowStages($row)}</tr>";
        }

        $return .= "</tbody></table>";
        return $return;
    }
	
    public function getCountForeigns()
    {
        $query = $this->qry("SELECT COUNT(*) FROM {$this->_table}");
        $row = (mysql_num_rows($query) === 1) ? mysql_fetch_row($query) : false;
        return $row[0];
    }
	
    public function addForeign($data = array())
    {
        if (!empty($data)) {
            $this->_fields = array_merge($this->_fields,$data);
            $sql = "INSERT INTO {$this->_table} VALUES ('{$this->_fields['id']}','{$this->_fields['fio']}',{$_SESSION["foreignersUserid"]},{$this->_fields['inv']},{$this->_fields['invdate']}, {$this->_fields['formNumber']}, {$this->_fields['res']},{$this->_fields['resdate']},
                {$this->_fields['reshoddate']},{$this->_fields['edu']},{$this->_fields['edudate']},{$this->_fields['hor']},{$this->_fields['hordate']},{$this->_fields['hoc']},{$this->_fields['hocdate']},{$this->_fields['zas']},{$this->_fields['zasdate']},
                {$this->_fields['isp']},{$this->_fields['ispdate']},{$this->_fields['med']},{$this->_fields['meddate']},{$this->_fields['enr']},{$this->_fields['enrdate']},
                {$this->_fields['pet']},{$this->_fields['petdate']},{$this->_fields['removed']},{$this->_fields['country']},{$this->_fields['status']},{$this->_fields['whoInvites']},{$this->_fields['actionEndDate']},{$this->_fields['note']})";
            $query = $this->qry($sql);
				
            if($query) $fid = mysql_insert_id();
            else $fid = false;
			
            return $fid;
        }
        else return false;
    }
	
    public function deleteForeign($id)
    {
        return $this->qry("DELETE FROM {$this->_table} WHERE id = {$id}");
    }
	
    public function updateForeign($id, $data = array())
    {
        if (!empty($data)) {
            array_walk($data, "updateCallBack");
            $set = implode(",", $data);
            $q_result = $this->qry("UPDATE {$this->_table} SET {$set} WHERE id='{$id}';");
            return $q_result;
        }
        else return false;
    }

    public function updateForeignAfterDel($id)
    {
        $q_result = $this->qry("DELETE FROM {$this->_table} WHERE id='{$id}';");
        return $q_result;
    }
	
    public function searchForm()
    {
        $value = isset($_SESSION['foreignersSearch']) ?  $_SESSION['foreignersSearch'] : '';
        $form = "<form id='searchForm' action='" . self::IURL . "' method='get'><input type='text' name='search' placeholder='Поиск' value='{$value}'><button title='Найти'></button></form>";
        return $form;
    }
	
    public function getContent()
    {
        $sort = 'fio';
        $direction = self::SORTUP;

        $sortattention = isset($_GET['sort']) ? true : false;
        $srt = isset($_GET['sort']) ? (int) $_GET['sort'] : 0;
		
        if(isset($_GET['archiv']) && ((int) $_GET['archiv'] === 1)) $_SESSION['archiv'] = 1;
        elseif(isset($_GET['archiv']) && ((int) $_GET['archiv'] === 0)) unset($_SESSION['archiv']);
		
        if(isset($_GET['direction']) && ($_GET['direction'] === self::SORTDOWN)) $direction = self::SORTDOWN;
		
        if(array_key_exists($srt, $this->_sort)) $sort = $this->_sort[$srt];

        $content = "<span id='dekan'>{$this->getDekan($_SESSION["foreignersUserid"])}</span><div class='tab-container'><div class='tabheader'>";
        $content .= "{$this->archiv()}{$this->getExport()}</div>";
        $content .= "<div class='actions'>{$this->ClassAdd()}{$this->resetSort()}</div>{$this->getAllForeigns($sortattention,$sort, $direction)}</div><div class=\"dialog_box_cont\">
            <div class=\"dialog_box\">
            <div class=\"dialogHeader\"><h2>Редактирование мероприятия</h2></div>
            <div class=\"dialog_box_inner\"></div>
            <div class=\"dialog_box_ok\">Сохранить</div>
            <div class=\"dialog_box_exit\">Отмена</div>
            </div></div><div class=\"dialog_box_wraper\"></div>";
        return $content;
    }
}
?>