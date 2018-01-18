<?php
class csvImport{
    private static $form_file = 'tpl/uploadfile.tpl';
    private $table = 'iws_foreigners';
    private $file_type;
    private $file;
	
    public function __construct($name, $file)
    {
        $this->file_type = pathinfo($name, PATHINFO_EXTENSION);
        $this->ensure($this->file_type === 'csv', 'Неверный формат файла');
        $this->file = $file;
        $this->ensure(file_exists($this->file), 'Файл не существует');
    }
	
    private function qry($query)
    {
        return mysql_query($query);
    }
	
    private function sanitizeString($var) {
        $var = strip_tags($var);
        //$var = htmlentities($var);
        $var = stripslashes($var);
        $var = mysql_real_escape_string($var);
        return $var;
    }
	
    public function writeContent()
    {
        $file = fopen('php://memory', 'w+');
        fwrite($file, file_get_contents($this->file));
        rewind($file);
		
        while (($row = fgetcsv($file, ",")) != FALSE) {
		$result = $this->qry("INSERT INTO {$this->table} (adduserid,fio) VALUES({$_SESSION["foreignersUserid"]}, '{$this->sanitizeString($row[0])}')");
            $this->ensure($result, "Произошла ошибка записи в базу данных");
        }
		
        return true;
    }
	
    public static function getForm()
    {
        return file_get_contents(self::$form_file);
    }
	
    private function ensure($expr, $message)
    {
        if(!$expr) throw new Exception($message);
    }
}
?>