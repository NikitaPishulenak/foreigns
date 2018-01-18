<?php
class Logger{
    private $table_name = 'foreigners_logs';
    private $data = array();
	
    public function __construct ($id = null)
    {
        if(!is_null($id)) {
            $this->data['userid'] = $id;
            $this->data['userip'] = $_SERVER['REMOTE_ADDR'];
            $this->data['d'] = date('Y-m-d');
            $this->data['t'] = date('H:i:s');
            
        }
    }
	
    private function qry($query)
    {
        return mysql_query($query);
    }
	
    public function add()
    {
        if (!empty($this->data)) {
            $fields = implode(',', array_keys($this->data));
            $values = "'" . implode("','", $this->data) . "'";
            $sql = "INSERT INTO {$this->table_name} ({$fields}) VALUES ({$values})";
            $query = $this->qry($sql);
				
            if($query) $fid = mysql_insert_id();
            else $fid = false;
			
            return $fid;
        }
        else return false;
    }
	
    private function ensure($expr, $message)
    {
        if(!$expr) throw new Exception($message);
    }
}

?>