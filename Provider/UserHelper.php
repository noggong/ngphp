<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 4. 24.
 * Time: 오전 2:00
 */

class UserHelper {

    /**
     * @var UserRepository
     */
    private $user_repo;

    /**
     * @var int
     */
    private $unum;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $uname;

    /**
     * @var string
     */
    private $uauth;

    private $temp_user = false;

    private $session_name = 'temp_member';

    private $session_name_temp_order = 'temp_order';

    /** @var bool $is_company 상점 아이디 인지 체크 */
    private $is_company = false;

    /** @var bool $is_admin 어드민 아이디인지 체크 */
    private $is_admin = false;

    /** @var  Company 상점 회원일때 company model */
    private $company_model;
    /**
     * @var
     */
    private $detail_info;

    public function __CONSTRUCT(UserRepository $user_repo)
    {

        $this->user_repo = $user_repo;
        if (isset($_SESSION['uAuth']) && isset($_SESSION['uNum'])  && !empty($_SESSION['uNum'])) {

            $this->uauth = $_SESSION['uAuth'];
            $this->unum = $_SESSION['uNum'];
            $this->uid = $_SESSION['uId'];
            $this->uname = $_SESSION['uName'];

            /** 0: 어드민 1: 일반 회원 2: 상점 회원 */
            if ($this->uauth === '0') {
                $this->is_admin = true;
                $this->detail_info = $this->getUser($this->unum);
            } else if ($this->uauth == 2) {
                $this->is_company = true;
                $company = new Company();
                $this->setCompanyModel($company);
                $this->detail_info = $this->company_model->find($this->unum)->toArray();
                unset($this->company_model);

            } else if ($this->uauth == 1) {
                $this->detail_info = $this->getUser($this->unum);
            }
        }

    }

    private function setCompanyModel(Company $company)
    {
        $this->company_model = $company;
    }

    /**
     * 로그인 확인
     * @return bool
     */
    public function isLogin()
    {
        return ($this->unum && $this->uid);
    }

    /**
     * 어드민 아이디인지 체크
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * 상점 아이디인지 체크
     * @return bool
     */
    public function isCompany()
    {
        return $this->is_company;
    }

    /**
     * @return string
     */
    public function getUAuth()
    {
        if ($this->isLogin()) {
            return $this->uauth;
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function getUser($id)
    {
        return $this->user_repo->getUser($id)->toArray();
    }

    /**
     * @return array
     */
    public function getUid()
    {
        return ($this->isLogin()) ? $this->uid : false;
    }

    /**
     * @return int
     */
    public function getUnum()
    {
        return ($this->isLogin()) ? $this->unum : false;
    }

    /**
     * @return int
     */
    public function getName()
    {
        return ($this->isLogin()) ? $this->uname : false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function getUserInfo($key)
    {
        return (!empty($this->detail_info[$key])) ? $this->detail_info[$key] : false;
    }


    /**
     * @param string $zip
     * @return array
     */
    public static function getSplitZip($zip)
    {
        return explode('-', $zip);

    }

    /**
     * @param string $tel
     * @return array
     */
    public static function getSplitTel($tel)
    {
        return explode('-', $tel);
    }

    /**
     * 비회원 구매 대상자 로긴시키기
     * @param OrderUtil $order_util
     * @return bool|int
     */
    public function loginTempUser(OrderUtil $order_util)
    {
        if (!empty($_SESSION[$this->session_name])) {
            $user_info = $order_util->getTempUser($_SESSION[$this->session_name]);
            $this->uauth = 'temp';
            $this->unum =$user_info['seq'];
            $this->uid = 'temp';
            $this->uname = 'temp';

            $this->detail_info['MB_EMAIL'] = $user_info['email'];
            $this->detail_info['MB_HAND_TEL'] = $user_info['tel'];
            $this->detail_info['MB_NAME'] = $user_info['name'];
            $this->temp_user = true;
        } else {
            return false;
        }
    }

    public function isTempUser()
    {
        return $this->temp_user;
    }

    /**
     * 비회원 유저 로그아웃 시킴
     */
    public function doLogOutForTemp()
    {
        unset($_SESSION[$this->session_name]);
        $this->temp_user = false;
        $this->__CONSTRUCT($this->user_repo);
    }

    /**
     * 비회원 세션굽기.
     * @param int $temp_user_id
     */
    public function bakeSessionForTemp($temp_user_id)
    {
        $_SESSION[$this->session_name] = $temp_user_id;
    }
    /**
     * 구매내역 비회원 세션굽기.
     * @param int $order_id
     */
    public function bakeSessionForTempOrder($order_id)
    {
        $_SESSION[$this->session_name_temp_order] = $order_id;
    }
    /**
     * @param Company $company
     * @param string $id
     * @param string $pw
     * @return bool
     */
    public static function doLoginForCompany(Company $company, $id, $pw)
    {
        if (!$company_info = $company->where('id', $id)->rawWhere('pwd = PASSWORD("' . $pw . '")')->first()->toArray()) {
            return false;
        }

        $_SESSION['uAuth'] = 2;
        $_SESSION['uNum'] = $company_info["seq"];
        $_SESSION['uName'] = $company_info["alias"];
        $_SESSION['uId'] = $company_info["id"];
        $_SESSION['isCompany'] = true;

        return true;

    }

    /**
     * 로그인 한 상점의 이미지 가져오
     * @param int $type
     * @return string
     */
    public function getImageSrc($type = 6)
    {
        /** 상점 아이디 로그인 이 아니면 빈값을 return 한다. */
        if (!$this->is_company) {
            return '';
        }
        $image_url = ImageHelper::urlencode('/mall_new/assets/images/companiesLogo/' . $this->detail_info['image']);
        return '/mall_new/image/' . $image_url . '/type/' . $type . '/';
    }

    /**
     * 유저 아이디(pk) 통해 유저 정보 가져오기
     * @param int $user_id
     * @return array
     */
    public static function getUserInfoByUserId($user_id) {
        $hw_member_model = new HwMember();
        return $hw_member_model->find($user_id)->toArray();
    }

    /**
     * 유저 아이디 (Pk)를 통해 유저 이름 가져오기
     * @param info $user_id
     * @return string
     */
    public static function getUserNameByUserId($user_id)
    {

        $user_info = UserHelper::getUserInfoByUserId($user_id);

        if (empty($user_info)) {
            return 'Unknown';
        }

        return $user_info['MB_NAME'];
    }

    /**
     * 유저 아이디 (pk)를 통해 유저 ID 가져오기
     * @param info $user_id
     * @return string
     */
    public static function getUserIdByUserId($user_id)
    {

        $user_info = UserHelper::getUserInfoByUserId($user_id);

        if (empty($user_info)) {
            return 'Unknown';
        }

        return $user_info['MB_ID'];
    }

    /**
     * 유저 아이디(pk) 통해 비회원 주문자 정보 가져오기
     * @param int $user_id
     * @return array
     */
    public static function getNoneMemberInfoByUserId($user_id) {
        $nonemember_member_model = new NonememberOrder();
        return $nonemember_member_model->find($user_id)->toArray();
    }

    /**
     * 유저 아이디 (Pk)를 통해 비회원 주문자 이름 가져오기
     * @param info $user_id
     * @return string
     */
    public static function getNoneMemberNameByUserId($user_id)
    {

        $user_info = UserHelper::getNoneMemberInfoByUserId($user_id);

        if (empty($user_info)) {
            return 'Unknown';
        }

        return $user_info['name'];

    }

    /**
     * @return bool
     */
    public function getSessionForTempOrder()
    {
        return (!empty($_SESSION[$this->session_name_temp_order])) ? $_SESSION[$this->session_name_temp_order] : false;
    }
}
