<?php
/* INIsecurepay.php
 *
 * 이니페이 플러그인을 통해 요청된 지불을 처리한다.
 * 지불 요청을 처리한다.
 * 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
 * <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
 *  
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
 */

  /****************************
   * 0. 세션 시작             *
   ****************************/
  session_start();								//주의:파일 최상단에 위치시켜주세요!!

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/

	include $_SERVER['DOCUMENT_ROOT']."/INIpay50/INI_include.php";    // 공통  변수  인클루드

	
	
	/***************************************
	 * 2. INIpay50 클래스의 인스턴스 생성 *
	 ***************************************/
	$inipay = new INIpay50;

	/*********************
	 * 3. 지불 정보 설정 *
	 *********************/
	$inipay->SetField("inipayhome", $realPath); // 이니페이 홈디렉터리(상점수정 필요)
	$inipay->SetField("type", "securepay");                         // 고정 (절대 수정 불가)
	$inipay->SetField("pgid", "INIphp".$pgid);                      // 고정 (절대 수정 불가)
	$inipay->SetField("subpgip","203.238.3.10");                    // 고정 (절대 수정 불가)
	$inipay->SetField("admin", $_SESSION['INI_ADMIN']);    // 키패스워드(상점아이디에 따라 변경)
	$inipay->SetField("debug", "true");                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->SetField("uid", $uid);                                 // INIpay User ID (절대 수정 불가)
    $inipay->SetField("goodname", iconv("utf-8","euc-kr",  $goodname) );                       // 상품명 
	$inipay->SetField("currency", $currency);                       // 화폐단위

	$inipay->SetField("mid", $_SESSION['INI_MID']);        // 상점아이디
	$inipay->SetField("rn", $_SESSION['INI_RN']);          // 웹페이지 위변조용 RN값
	$inipay->SetField("price", $_SESSION['INI_PRICE']);		// 가격
	$inipay->SetField("enctype", $_SESSION['INI_ENCTYPE']);// 고정 (절대 수정 불가)


     /*----------------------------------------------------------------------------------------
       price 등의 중요데이터는
       브라우저상의 위변조여부를 반드시 확인하셔야 합니다.

       결제 요청페이지에서 요청된 금액과
       실제 결제가 이루어질 금액을 반드시 비교하여 처리하십시오.

       설치 메뉴얼 2장의 결제 처리페이지 작성부분의 보안경고 부분을 확인하시기 바랍니다.
       적용참조문서: 이니시스홈페이지->가맹점기술지원자료실->기타자료실 의
                      '결제 처리 페이지 상에 결제 금액 변조 유무에 대한 체크' 문서를 참조하시기 바랍니다.
       예제)
       원 상품 가격 변수를 OriginalPrice 하고  원 가격 정보를 리턴하는 함수를 Return_OrgPrice()라 가정하면
       다음 같이 적용하여 원가격과 웹브라우저에서 Post되어 넘어온 가격을 비교 한다.

		$OriginalPrice = Return_OrgPrice();
		$PostPrice = $_SESSION['INI_PRICE']; 
		if ( $OriginalPrice != $PostPrice )
		{
			//결제 진행을 중단하고  금액 변경 가능성에 대한 메시지 출력 처리
			//처리 종료 
		}

      ----------------------------------------------------------------------------------------*/
	$inipay->SetField("buyername", iconv("utf-8","euc-kr", $buyername) );       // 구매자 명
	$inipay->SetField("buyertel",  $pd_shp_phone1."-".$pd_shp_phone2."-".$pd_shp_phone3);        // 구매자 연락처(휴대폰 번호 또는 유선전화번호)
	$inipay->SetField("buyeremail",$EMAIL);      // 구매자 이메일 주소
	$inipay->SetField("paymethod", $paymethod);       // 지불방법 (절대 수정 불가)
	$inipay->SetField("encrypted", $encrypted);       // 암호문
	$inipay->SetField("sessionkey",$sessionkey);      // 암호문


	$inipay->SetField("url",  $currUrl ); // 실제 서비스되는 상점 SITE URL로 변경할것


	$inipay->SetField("cardcode", $cardcode);         // 카드코드 리턴
	$inipay->SetField("parentemail", $parentemail);   // 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)
	
	/*-----------------------------------------------------------------*
	 * 수취인 정보 *                                                   *
	 *-----------------------------------------------------------------*
	 * 실물배송을 하는 상점의 경우에 사용되는 필드들이며               *
	 * 아래의 값들은 INIsecurepay.html 페이지에서 포스트 되도록        *
	 * 필드를 만들어 주도록 하십시요.                                  *
	 * 컨텐츠 제공업체의 경우 삭제하셔도 무방합니다.                   *
	 *-----------------------------------------------------------------*/
	$inipay->SetField("recvname", iconv("utf-8","euc-kr" ,  $pd_shp_name) );	// 수취인 명
	$inipay->SetField("recvtel",$pd_shp_tel1."-".$pd_shp_tel2."-".$pd_shp_tel3);		// 수취인 연락처
	$inipay->SetField("recvaddr", iconv("utf-8","euc-kr" , $recvaddr) );	// 수취인 주소
	$inipay->SetField("recvpostnum",$ADDR1." ".$ADDR2 );  // 수취인 우편번호
	$inipay->SetField("recvmsg",  iconv("utf-8","euc-kr" , $pd_shp_request) );		// 전달 메세지

  $inipay->SetField("joincard",$joincard);  // 제휴카드코드
  $inipay->SetField("joinexpire",$joinexpire);    // 제휴카드유효기간
  $inipay->SetField("id_customer",$id_customer);    //user_id

	
	/****************
	 * 4. 지불 요청 *
	 ****************/
	$inipay->startAction();
	          

	
	
	/*******************************************************************
	 * 7. DB연동 실패 시 강제취소                                      *
	 *                                                                 *
	 * 지불 결과를 DB 등에 저장하거나 기타 작업을 수행하다가 실패하는  *
	 * 경우, 아래의 코드를 참조하여 이미 지불된 거래를 취소하는 코드를 *
	 * 작성합니다.                                                     *
	 *******************************************************************/
	/*
	$cancelFlag = "false";

	// $cancelFlag를 "ture"로 변경하는 condition 판단은 개별적으로
	// 수행하여 주십시오.

	if($cancelFlag == "true")
	{
		$TID = $inipay->GetResult("TID");
		$inipay->SetField("type", "cancel"); // 고정
		$inipay->SetField("tid", $TID); // 고정
		$inipay->SetField("cancelmsg", "DB FAIL"); // 취소사유
		$inipay->startAction();
		if($inipay->GetResult('ResultCode') == "00")
		{
      $inipay->MakeTXErrMsg(MERCHANT_DB_ERR,"Merchant DB FAIL");
		}
	}
	*/
		
	
?>


<html>
<head>
<title>처리중</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style>
body, tr, td {font-size:9pt; font-family:굴림,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/ 
.pl_01 {padding:1 10 0 10; line-height:19px;}

/* Link ******/ 
.a:link  {font-size:9pt; color:#333333; text-decoration:none}
.a:visited { font-size:9pt; color:#333333; text-decoration:none}
.a:hover  {font-size:9pt; color:#0174CD; text-decoration:underline}

.txt_03a:link  {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:visited {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:hover  {font-size: 8pt;line-height:18px;color:#EC5900; text-decoration:underline}
</style>



</head>
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">
      <!--w=299     h= 153   -->
      <table width="300" height="150" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="143" align="center" valign="top" background="/INIpay50/img/loading_bg.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="33" align="center"style="padding:0 0 0 0"><b><img src="/INIpay50/img/title_02.gif" width="142" height="18"></b></td>
              </tr>
              <tr> 
                <td height="45" align="center" valign="bottom">다소 시간이 걸릴수도 있으니 
                  잠시 기다려 주세요.</td>
              </tr>
              <tr> 
                <td height="35" align="center"><img src="/INIpay50/img/loading.gif" width="269" height="14"></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>


 
            <?php
            	
            	if($inipay->GetResult('ResultCode') == "01"){  // 결제 에러시
            	print_r($inipay);
            	?>
						<script>
								alert('결제 진행중 오류가 발생하였습니다.\n결제가 되지는 않았으니 새로고침 후에 다시 진행하시고 또 발생시 관리자에게 문의 바랍니다.\n이용에 불편을 드려 죄송합니다..');
						</script>

				<?
					exit;
				
				}
		
	    ?>		
            
               




<!--
리턴SEQ : <?=$returnSeq?>
거래번호: <?=$inipay->GetResult('TID')?>
주문번호 : <?=$oid?>
승인번호 : <?=$inipay->GetResult('ApplNum')?>
-->
	<script>
	
		onload = function() {
			document.order_frm.submit();
		}
		
	</script>

<form id="order_frm" name="order_frm"  method="post"  action="/mypage/shopping_order_success_proc.php";>
	<input type="hidden" name="returnSeq" value="<?=$returnSeq?>" />
	<input type="hidden" name="PG_TRANS_NUM" value="<?=$inipay->GetResult('TID')?>" />
	<input type="hidden" name="PG_ORDER_NUM" value="<?=$oid?>" />
	<input type="hidden" name="PG_CONFIRM_NUM" value="<?=$inipay->GetResult('ApplNum')?>" />
</form>


</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                         





