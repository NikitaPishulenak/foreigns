<?php
class checkAccess{
    private $username;
    private $table = 'foreigners_users';
	
    public function __construct()
    {
        $this->username = $_SERVER["PHP_AUTH_USER"];
        //if($_SERVER["PHP_AUTH_USER"] === 'BakharauSV') $this->username = 'KalechytsSK';//$_SERVER["PHP_AUTH_USER"];
    }
	
    protected function getUserId($field)
    {
        $row = null;
        $query = "SELECT {$field} FROM {$this->table} WHERE username = '{$this->username}'";
        $result = mysql_query ($query);
		
        if(mysql_num_rows($result) === 1) {
            $row = mysql_fetch_assoc($result);
            $row = $row[$field];
        }
		
        return $row;
    }
	
    public function getAccess()
    {
        $id = $this->getUserId('id');
        $departmentid = $this->getUserId('departmentid');
		
        $this->ensure(!is_null($id), "У вас недостаточно прав на данную страницу");
        $_SESSION["foreignersIsauth"] = 1; //Делаем пользователя авторизованным
        require_once("Logger.php");
        $loger = new Logger($id);
        $loger->add();
        $_SESSION["foreignersUserid"] = $id;
        $_SESSION["foreignersDepartmentid"] = $departmentid;
        header("Location: /foreigners/");
    }
	
    protected function ensure($expr, $message)
    {
        if(!$expr) throw new Exception($message);
    }
}

?>