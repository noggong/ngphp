<?php

class Route {

	/**
	 * @var array
	 */
	static $route_collerction = array();

	/**
	 * @var array
	 */
	static $route_reg_collerction = array();

	/**
	 * @var string
	 */
	static $prefix = 'route';

	/**
	 * @param string $request_uri
	 * @param string $controller
	 */
	static function get($request_uri, $controller)
	{
		global $App;
		if (empty(Route::$route_collerction)) {
			Route::$route_collerction = new stdClass;
		}

		if ($request_uri == '/') {

			$name = $App->app_path;
		} else {
			$name = $App->app_path . $request_uri;
		}

		$name = Route::plasticUri($name);

		Route::$route_collerction->$name = $controller;

	}

	/**
	 * @param $request_uri
	 * @param $controller
	 */
	static function rest($request_uri, $controller)
	{
		global $App;
		if (empty(Route::$route_reg_collerction)) {
			Route::$route_reg_collerction = new stdClass;
		}

		if ($request_uri == '/') {

			$name = $App->app_path;
		} else {
			$name = $App->app_path . $request_uri;
		}

		$name = Route::plasticUri($name);

		Route::$route_reg_collerction->$name = $controller;

	}

	/**
	 * @param $request_uri
	 * @return array
	 */
	static function getContrller($request_uri)
	{
		$name = Route::plasticUri($request_uri);

		//error_log(print_r(Route::$route_reg_collerction, true));
		//error_log($name);
		$reg_ctrl = Route::searchRouteByReg($name);
		$param = array();
		if ($reg_ctrl) {
			$ctrl = $reg_ctrl['ctrl'];
			$param = $reg_ctrl['param'];
		} else {
			if (!isset(Route::$route_collerction->$name)) {

				return false;
			}
			$ctrl = Route::$route_collerction->$name;
		}
		$ctrl = explode('@', $ctrl);

		/** 메소드를 기입하지 않으면 uri 의 가장 마지막 path가 method 명이 된다. */
		if (empty($ctrl[1])) {
			$uri_array = explode('/', $request_uri);

			$method = array_pop($uri_array);
			if (empty($method)) {
				$method = array_pop($uri_array);
			}
		} else {
			$method = $ctrl[1];
		}

		/** controller method 실행전 실행될 filter */
		$filter = false;
		$method_arr = explode('::', $method);
		if (sizeof($method_arr) > 1) {
			$filter = explode('|', $method_arr[1]);
		}


		$method = $method_arr[0];

		return array(
			'obj'    => $ctrl[0] . 'Controller',
			'method' => $method,
			'param'  => $param,
			'filter' => $filter
		);
	}

	/**
	 * @param $uri
	 * @return string
	 */
	static function plasticUri($uri) 
	{
		if (substr($uri, -1, 1) != '/') {
			$uri = $uri . '/';

		}
		return Route::$prefix . str_replace('/', '.', $uri);
	}

	/**
	 * @param $name
	 * @return array|bool
	 */
	static function searchRouteByReg($name)
	{
		foreach (Route::$route_reg_collerction as $reg => $ctrl) {
			
			$pattern = '/' . $reg . '/';
			if (preg_match($pattern, $name, $matches)) {
				array_shift($matches);
				return array(
					'ctrl' => $ctrl,
					'param' => $matches
				);
			}
		}

		return false;

	}
}