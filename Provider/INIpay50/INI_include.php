<?
	@session_start();	

	include $_SERVER['DOCUMENT_ROOT'] . "/../INIpay50/libs/INILib.php";


	// 도메인 경로
	$currUrl= "http://".$_SERVER["HTTP_HOST"]."/";


	$realPath = $_SERVER['DOCUMENT_ROOT'] . "/../INIpay50/";   // 이니시스 모듈이 있는 절대 경로

	$key_Pwd = "1111";		//  키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)





  ?>

