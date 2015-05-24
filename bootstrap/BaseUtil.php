<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 4. 22.
 * Time: 오후 3:55
 */

class BaseUtil {

    /**
     * @var App
     */
    protected $app;

    /**
     * @var
     */
    protected $user;

    /**
     * @var BaseController
     */
    protected $controller;
    /**
     * @param App $app
     * @param UserHelper $user
     * @param BaseController $controller
     */
    public function __CONSTRUCT(App $app, UserHelper $user, BaseController $controller)
    {
        $this->app = $app;
        $this->user = $user;
        $this->controller = $controller;
    }

}