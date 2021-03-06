<?php
define('ACTCTRL_GET', 'get');
define('ACTCTRL_POST', 'post');
define('ACTCTRL_AJAX', 'ajax');
define('ACTCTRL_CUST', 'custom');

class ActionsController {
	
	public $__action_sources;
	public $__current_data;
	public $__action_source;
	public $__action;
	
	public $__params = array();
	
	public function preAction($action){
		
		safeMode($action !== 'main');
	}
	
	public function postAction($action){
		;
	}
	
	public function __construct(){
		
		$this->registerStandardSources();
	}
	
	public function registerSource($source_id, &$source_data){
		
		$this->__action_sources[$source_id] = &$source_data;
	}
	
	public function registerStandardSources(){
		
		$Register = &Register::getInstance();
		$GetVars = &$Register->get(VAR_GET);
	
		if(isset($GetVars['caller'])){
			
			ClassManager::includeClass('jshttprequest');
			$JsHttpRequest = new JsHttpRequest(translate("str_default_charset"));
		}
		
		global $_REQUEST;
		
		$this->registerSource(ACTCTRL_AJAX, $_REQUEST);
		$this->registerSource(ACTCTRL_POST, $Register->get(VAR_POST));
		$this->registerSource(ACTCTRL_GET, $Register->get(VAR_GET));
	}
	
	static public function exec(
		$controller_name, 
        array $sources = array(ACTCTRL_POST, ACTCTRL_GET, ACTCTRL_AJAX, ACTCTRL_CUST), 
        $params = array()
    ){
		$controller = new $controller_name;
		/*@var $controller ActionsController*/
		$controller->__params = $params;
		$controller->__exec($sources);
	}
	
	public function __exec_cust($data){
		
		$this->registerSource(ACTCTRL_CUST, $data);
		
		return $this->__exec(ACTCTRL_CUST);
	}
	
	public function __exec($sources = null){

        if (!is_array($sources)) $sources = array($sources);

        foreach ($sources as $source) {

            if (!isset($this->__action_sources[$source]['action'])) continue;

            if (!method_exists($this, $this->__action_sources[$source]['action'])) {
                pear_dump('No action handler');
                die;
            }

            $this->__current_data = &$this->__action_sources[$source];
            $this->__action_source = $source;
            $this->__action = $this->__action_sources[$source]['action'];

            switch ($source) {
                case ACTCTRL_GET:
                    renderURL('action=', '', true);
                    break;
            }

            $this->preAction($this->__action);

            $return = $this->{$this->__action}();

            $this->postAction($this->__action);

            return $return;
		}
		
		if(!$this->__action){
			$this->__current_data = &$this->__action_sources[ACTCTRL_GET];
			$this->__action_source = ACTCTRL_GET;
			$this->__action = 'main';
			
			$this->preAction($this->__action);
			
			$return = $this->{$this->__action}();
		}
	}
	
	public function getData($key = null, $key2 = null){
		
		if (is_null($key)) {
            return $this->__current_data;
        }
		
		if (is_null($key2)) {
			return isset($this->__current_data[$key])?$this->__current_data[$key]:'';
        } else {
			return isset($this->__current_data[$key][$key2])?$this->__current_data[$key][$key2]:'';
        }
	}

	public function existsData($key){
		return isset($this->__current_data[$key]);		
	}
	
	public function setData($key, $value){
		
		$this->__current_data[$key] = $value;
	}

	public function main(){
		;
	}
}