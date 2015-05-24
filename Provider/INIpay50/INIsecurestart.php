<?php
/* INIsecurepaystart.php
 *
 * 이니페이 웹페이지 위변조 방지기능이 탑재된 결제요청페이지.
 * 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
 * <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
 *
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
 */

/****************************
 * 0. 세션 시작                *
 ****************************/
/** 프레임워크 단에서 이미 start 함 */
//@session_start(); 					//주의:파일 최상단에 위치시켜주세요!!

/**************************
 * 1. 라이브러리 인클루드 *
 **************************/

include $_SERVER['DOCUMENT_ROOT'] . "/INIpay50/INI_include.php";    // 공통  변수  인클루드
?>




<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script>
<script language=javascript>
    StartSmartUpdate();
</script>
<!------------------------------------------------------------------------------- 
* 웹SITE 가 https를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js를 사용 
* 웹SITE 가 Unicode(UTF-8)를 이용하면 http://plugin.inicis.com/pay61_secuni_cross.js를 사용 
* 웹SITE 가 https, unicode를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js 사용 
-------------------------------------------------------------------------------->
<!---------------------------------------------------------------------------------- 
※ 주의 ※
 상단 자바스크립트는 지불페이지를 실제 적용하실때 지불페이지 맨위에 위치시켜 
 적용하여야 만일에 발생할수 있는 플러그인 오류를 미연에 방지할 수 있습니다.

<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script> 
  <script language=javascript>
  StartSmartUpdate();	// 플러그인 설치(확인)
  </script>
----------------------------------------------------------------------------------->


<script language=javascript>

    var openwin;

    function pay(frm) {
        // MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
        // 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
        // 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
        // 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.

        if (document.frm.clickcontrol.value == "enable") {

            if (document.frm.goodname.value == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
            {
                alert("상품명이 빠졌습니다. 필수항목입니다.");
                return false;
            }
            else if (( navigator.userAgent.indexOf("MSIE") >= 0 || navigator.appName == 'Microsoft Internet Explorer' ) && (document.INIpay == null || document.INIpay.object == null))  // 플러그인 설치유무 체크
            {
                alert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. \n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
                return false;
            }
            else {
                /******
                 * 플러그인이 참조하는 각종 결제옵션을 이곳에서 수행할 수 있습니다.
                 * (자바스크립트를 이용한 동적 옵션처리)
                 */


                if (MakePayMessage(frm)) {
                    disable_click();
                    //openwin = window.open("/INIpay50/childwin.html","childwin","width=299,height=149");
                    return true;
                }
                else {
                    if (IsPluginModule())     //plugin타입 체크
                    {
                        alert("결제를 취소하셨습니다.");
                        tmpOrderDel();   // 임시로 저장한 결제 정보 삭제
                    }
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }


    function enable_click() {
        document.frm.clickcontrol.value = "enable";
    }

    function disable_click() {
        document.frm.clickcontrol.value = "disable";
    }

    function focus_control() {
        if (document.frm.clickcontrol.value == "disable")
            openwin.focus();
    }
</script>

