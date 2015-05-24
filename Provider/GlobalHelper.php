<?php
/**
 * Created by PhpStorm.
 * User: noggong
 * Date: 15. 5. 22.
 * Time: 오전 9:51
 */

class GlobalHelper
{

    /**
     * DB에서 특정 값을 불러왓을때 해당 값이 존재하는지 체크하고 해당 상점이 볼 권한이 있는 값인지 체크
     * item 내의 company_seq를 검사한다.
     * @param UserHelper $user
     * @param array $item
     */
    public static function isYourItemForAdmin(UserHelper $user, array $item, $url = '')
    {
        /** 상품 아이디는 받았으나 상품이 없는경우 || 상점이면서 가져온 상품이 해당 상점의 상품이 아닐경우 */
        if (empty($item) ||
            ($user->isCompany() && $user->getUnum() != $item['company_seq'])
        ) {

            $alertMesg = '잘못된 접근입니다.';
            $script = '<html><body>'
                . '<script type="text/javascript" language="JavaScript">' . "\n"
                . '//<![CDATA[' . "\n"
                . 'alert("' . str_replace(array('"', '\\'), array('\"', '\\\\'), $alertMesg) . '");' . "\n"
                . 'location.href="' . $url . '";' . "\n"
                . '//]]>' . "\n"
                . '</script>' . "\n"
                . '</body></html>';

            echo $script;
            exit;
        }
    }
}