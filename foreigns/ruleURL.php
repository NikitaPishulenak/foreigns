<?php

class replaceUrl{
    private $str;

    function __construct ($value = '')
    {
        $this->str = $value;
    }
	
    public function check()
    {
        if(preg_match("/gofor=foreigners/",$this->str)){
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList&sort=(\d{1,})&direction=DESC/","foreigners/sort/\\1/down",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=manual/","foreigners/manual",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList&sort=(\d{1,})/","foreigners/sort/\\1",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList&archiv=([0-1])/","foreigners/archiv/\\1",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList&search=(.+)/","foreigners/search/\\1",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList&reset=1/","foreigners/reset",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreignersList/","foreigners",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreigners&islgnerror=1/","foreigners/login/error",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreigners&out=1/","foreigners/login/out",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=foreigners/","foreigners/login",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=csvexport&csvmode=([1-9])/","foreigners/export/csv/\\1",$this->str);
            $this->str = preg_replace("/foreigns\/index.php\?gofor=csvexport/","foreigners/export/csv",$this->str);
        }
		
        return $this->str;
    }
}

?>