<?php
class printOrder {
    public $title = 'Печать ордера';
    public $css = 'printOrder.css';
    public $data = array();
    public $source = 'printOrder.tpl';
	
    public function __construct(Array $data, $source = null)
    {
        $this->data = $data;
        if ($source) $this->source = $source . '.tpl';
    }
	
    public function getContent()
    {
        ob_start();
        require_once('tpl/' . $this->source);
        return ob_get_clean();
    }
	
}
?>