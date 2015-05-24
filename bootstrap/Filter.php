<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 5. 6.
 * Time: 오전 1:06
 */

/**
 * controller 가 시작 되기전 체크해야할 filter 를 모아두는 곳.
 * route.php 작성법 class@method::filter1|filter2
 * Class Filter
 */
class Filter {

    /** @var  UserHelper */
    private $user;

    /** @var  App */
    private $app;

    /** @var  Input */
    private $input;

    /** @var  BaseController */
    private $controller;

    /**
     * @param App $app
     * @param Input $input
     * @param BaseController $controller
     */
    public function __CONSTRUCT(App $app, Input $input, BaseController $controller) {
        $this->user = $controller->getUserHelper();
        $this->app  = $app;
        $this->input = $input;
        $this->controller = $controller;
    }

    /**
     * 어드민 유저가 아니면 상점 어드민 메인으로 간다.
     */
    public function isAdminUser()
    {
        if (!$this->user->isAdmin()) {
            header('Location: /mall_new/admin/');
            exit;
        }

    }

    /**
     * 로그인 했는지 체크한다.
     */
    public function isMember()
    {

    }

    public function isEqualCompany()
    {
        if ($this->user->isCompany() && $this->user->getUnum()) {

        }
    }

    /**
     * 어드민 이미지 리스트에서 상점 회원이면 상점 이미지 화면으로 이동
     */
    public function moveToImagesListForCompany()
    {
        if ($this->user->isCompany()) {
            header('Location: /mall_new/admin/images/company/' . $this->user->getUnum() . '/');
        }
    }

    /**
     * 어드민 이미지 리스트에서 상점 회원이면 상점 이미지 화면으로 이동
     */
    public function moveToQnaListForCompany()
    {
        if ($this->user->isCompany()) {
            header('Location: /mall_new/admin/customer/contents/' . $this->user->getUnum() . '/');
        }
    }
}