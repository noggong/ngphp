<?php

/**
 * Class App
 */
class App {

	/**
	 * @var string
	 */
	public $home;

	/**
	 * @var string
	 */
	public $app_path;

	/**
	 * @var string
	 */
	public $asset_path;

	/**
	 * @var
	 */
	public $host;

	public function __CONSTRUCT()
	{
		$this->doc_root = $_SERVER['DOCUMENT_ROOT'];
		$this->home = $_SERVER['DOCUMENT_ROOT'] . '';
		$this->app_path = '/';
		$this->asset_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/';
		$this->host = '//' . $_SERVER['HTTP_HOST'];

	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function setConfig($name, $value) {
		$this->$name = $value;
	}

	/**
	 * 앱실행
	 */
	public function run() 
	{
		//?이하는 url이 아닌것으로 취급한다.
		$uri_arr = explode('?', $_SERVER['REQUEST_URI']);
		
		if (! $controller = Route::getContrller($uri_arr[0])) {

			error_log('Can`t find Controller file about ' . $uri_arr[0]);
			return false;
		};

		$input = new Input();

		/** @var BaseController $ctrl_obj */
		$ctrl_obj = new $controller['obj']($input, $this);

		$method_name = strtolower($_SERVER['REQUEST_METHOD']) . substr(strtoupper($controller['method']), 0, 1) . substr($controller['method'], 1);

        if (method_exists($ctrl_obj, $method_name)) {
			$param_string = '';

			//parameter 를 보내줘야 할때 파라미터 스트링을 만든다.
			if (!empty($controller['param'])) {
				$param = array();

				foreach ($controller['param'] as $val) {
					$param[] = '\'' . $val . '\'';
				}
				$param_string = implode(', ', $param);
			}

			/** 필터가 정의 되어있으면 실행한다. */
			if ($controller['filter']) {
				$filter = new Filter($this, $input, $ctrl_obj);

				foreach ($controller['filter'] as $filter_method) {
					eval('$filter->$filter_method(' . $param_string . ');');
				}
			}

			//메소드 실행. eval 이므로 정규표현식 정확하게 써야 한다.
			eval('$ctrl_obj->$method_name(' . $param_string . ');');

		} else {
			if (! method_exists($ctrl_obj, 'index')) {
				error_log('Can`t load Controllers`s method');
				return false;
			}

			$ctrl_obj->index();

		}

	}

}

$App = new App();