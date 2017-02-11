<?php
class CsvExport{
    private $file_name;
    private $mode = null;
    private $where_state = null;
    private $table_name = 'iws_foreigners';
    private $status_table = 'foreigners_status';
    private $csv_str = '"ФИО";"Страна";"Оформление приглашения на учебу";"Дата";"Оформление временного пребывания";"Дата прибытия";"Дата ходатайства";"Договор на обучение";"Дата";"Оформление ордера на проживание в общежитии";"Дата";"Договор на проживание";"Дата";"Приказ на заселение";"Дата";"Регистрация договора на проживание в исполкоме";"Дата";"Прохождение медосмотра";"Дата";"Приказ о зачислении";"Дата";"Ходатайство на временное проживание";"Дата";"Статус";';
	
    public static function getWhere()
    {
        return array(
            array(),
            array("AND adduserid IN (SELECT u.id FROM foreigners_users AS u 
            LEFT JOIN foreigners_department AS d ON u.departmentid = d.id WHERE u.departmentid = {$_SESSION["foreignersDepartmentid"]})"),
            array("AND status = '1'"),
            array("AND status = '4'")
        );
    }
	
    public static function maxMode()
    {
        return count(self::getWhere()) - 1;
    }
	
    public function __construct($file, $mode = 0)
    {
        $this->file_name = $file;
        $this->csv_str  .= PHP_EOL;
        $this->where_state = self::getWhere();
        $this->mode = $mode;
    }
	
    private function qry($query)
    {
        return mysql_query($query);
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
	
    public function write()
    {
        $query = $this->qry("SELECT id,fio,status,country,adduserid,inv,DATE_FORMAT(invdate,'%d.%m.%Y') invdate,res,DATE_FORMAT(resdate,'%d.%m.%Y') resdate,DATE_FORMAT(reshoddate,'%d.%m.%Y') reshoddate,edu,DATE_FORMAT(edudate,'%d.%m.%Y') edudate,
            hor,DATE_FORMAT(hordate,'%d.%m.%Y') hordate,hoc,DATE_FORMAT(hocdate,'%d.%m.%Y') hocdate,zas,DATE_FORMAT(zasdate,'%d.%m.%Y') zasdate,isp,DATE_FORMAT(ispdate,'%d.%m.%Y') ispdate,med,DATE_FORMAT(meddate,'%d.%m.%Y') meddate,enr,
            DATE_FORMAT(enrdate,'%d.%m.%Y') enrdate,pet,DATE_FORMAT(petdate,'%d.%m.%Y') petdate,IF((DATEDIFF(CURDATE(),resdate)>=5 AND (res IS NULL)),1,0) as attention
            FROM {$this->table_name} WHERE removed = '0' " . implode(",", $this->where_state[$this->mode]) . "  ORDER BY fio ASC");
			
        while ($row = mysql_fetch_assoc($query)) {
            foreach($row as $key => $value) {
		$value = trim(preg_replace('/\s+/', ' ', $value));
                $row[$key] = htmlspecialchars_decode(str_replace("\"", "\"\"", $value));
            }
            $this->csv_str .= "{$row['fio']};{$row['country']};{$row['inv']};{$row['invdate']};{$row['res']};{$row['resdate']};{$row['reshoddate']};{$row['edu']};{$row['edudate']};{$row['hor']};{$row['hordate']};{$row['hoc']};{$row['hocdate']};{$row['zas']};{$row['zasdate']};{$row['isp']};{$row['ispdate']};{$row['med']};{$row['meddate']};{$row['enr']};{$row['enrdate']};{$row['pet']};{$row['petdate']};{$this->getStatusName($row['status'])};\r\n";
        }
		
        $file = fopen($this->file_name, "w"); // открываем файл для записи, если его нет, то создаем его в текущей папке, где расположен скрипт
        fwrite($file, trim($this->csv_str)); // записываем в файл строки
        fclose($file); // закрываем файл
		
        // задаем заголовки. то есть задаем всплывающее окошко, которое позволяет нам сохранить файл.
        header('Content-type: application/csv'); // указываем, что это csv документ
        header("Content-Disposition: inline; filename=export.csv"); // указываем файл, с которым будем работать
        readfile($this->file_name); // считываем файл
        unlink($this->file_name); // удаляем файл. то есть когда вы сохраните файл на локальном компе, то после он удалится с сервера
        exit();
    }
}
?>