#smartPop
##History
1. 2012.04.03 테두리 표시 방식 변경 및 불필요한 요소 제거  
기존에는 둥근 테두리를 지원하기 위해 이미지를 사용했으나 사용빈도가 없어서  이미지를 빼버리고 style 속성으로 지정할 수 있게 함.  
따라서 이미지가 들어가는 부분은 닫기 버튼 하나.  
**추가 옵션**  
border(테두리 두께)  
borderColor(테두리 색)  
padding(안쪽여백)  
closeMargin(닫기버튼 여백 : 위쪽, 오른쪽)  

2. 2012.01.02 위치고정 및 기타 버그 수정
3. 2011.12.20 ie버그 수정 - $.smartPop.close() 호출 시 jQuery오류 발생.  
처음에만 install하고 그 뒤로는 show() or hide()로 처리



##레이어 팝업 띄우기 - html or url

레이어로 팝업 띄우는 소스는 웹에서 쉽게 구할 수 있지만 딱 내입맛에 맞는것이 없어서 고민하다가 마침 쓸 일이 생겨서 만들었다.
일반적인 크기의 내용을 보여주는 팝업의 경우는 문제가 없지만 브라우저보다 큰 내용을 보여 줄 경우 스크롤바가 레이어에 생겨 버려서 모양도 별로 안예쁘고 스크롤 하기도 불편하다.    
불편한 이유는 본문 스크롤과 팝업 스크롤이 겹치기 때문이다.  
그리고 또하나의 문제점은 브라우저 크기를 변경하면 팝업 위치가 고정이 되어 버려서 모양이 깨지게 된다.(기존에 단순하게 만든 팝업)  
  
좀 불편한게 있긴해도 바빠서 그냥 무시하고 있었는데 요즘 페이스북을 많이 하다 보니 페이스북에서 특이한 점을 발견했다.  
페이스북 사진을 클릭하면 레이어 팝업이 뜨게 되는데 이때 스크롤바가 메인 페이지의 뉴스피드 스크롤바가 아니라 팝업의 스크롤바이다.  
뉴스피드는 스크롤 할 때 마다 새로운 내용을 가져오기 때문에 스크롤을 하면 할수록 스크롤 길이가 커지게 된다.  
그런데 어느 스크롤 위치에서든 사진 보기를 클릭하면 사진 크기와 댓글 길이에 딱 맞게 필요한 만큼만 스크롤이 가능하고 레이어를 닫으면 다시 원래 뉴스피드의 스크롤이 나온다.  
  
처음엔 브라우저 기본 스크롤 위로 레이어를 올렸는지 알고 무척 신기해했다.  
불가능한 것으로 알고 있었는데 어떻게 했는지?  
소스를 분석해 보려고 봐도 일반 사이트와는 달라서 확인하기가 쉽지가 않다.  
스크립트 파악은 힘들고 html 엘리먼트값의 변화를 보면서 css를 분석했다.  
명령은 자바스크립트로 하지만 실제 동작은 css를 조작하는 것이기 때문에~  
한참의 삽질끝에 페이스북과 같은 효과를 만들었다.  
만든김에 좀더 편하게 쓰려고 jQuery 플러그인 형태로 작업했다.  
  
###목적
1. 내용에 상관없이 깔끔한 스크롤바 처리하기
2. 브라우저 크기에 상관없이 중앙에 띄우기
3. html 컨텐츠, url 페이지 모두 가능하게 하기
  
### 특징
1. 브라우저 호환
2. 깔끔한 스크롤바 처리
3. 브라우저 크기 변경시 레이어 팝업 자동 중앙 정렬
4. html, url 모두 사용 가능
5. url페이지를 띄울 경우 프레임 크기 자동 조절  

  
### 설치
3개의 파일을 페이지에 추가하면 된다.  

	1. <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.min.js"></script>
	2. <script type="text/javascript" src="jquery.smartPop.js"></script>
	3. <link rel="stylesheet" href="jquery.smartPop.css" />
	
	
	
### 사용법
**html 내용 보여주기**

```
$.smartPop.open({title: '스마트팝', background: "red", width: 500, height: 500, html: '보여줄 내용' });
 
// 위치고정
$.smartPop.open({title: '스마트팝', width: 500, height: 500, html: '보여줄 내용', position:'fixed', left: 10, top: 10 });
```  

**url 페이지 띄우기**

```
$.smartPop.open({title: '스마트팝', width: 500, height: 500, url: '보여줄 페이지' });
```
세로 크기는 불러오는 페이지 크기에 맞게 자동으로 조절됨


**높이값 확인 로그**

```
$.smartPop.open({title: '스마트팝', width: 500, height: 500, log: true, url: '보여줄 페이지' });
```
log:true 설정


**창 바깥쪽 클릭하면 창닫기	**

```
$.smartPop.open({title: '스마트팝', bodyClose: true, width: 500, height: 500, html: '보여줄 내용' });
```

**닫기버튼 변경**

```
$.smartPop.open({title: '스마트팝', bodyClose: true, width: 500, height: 500, html: '보여줄 내용', closeImg: {width:13, height:13, src:'img/btn_close2.png'} });
```


**기본 옵션**

```
$.smartPop.defaults = {
    position    : 'center',
    left        : 310,
    top         : 10,
    bodyClose   : true,
    padding     : 10,
    background  : '#fff',
    border      : 5,
    borderColor : '#39a3e5',
    closeMargin : 3,
    closeImg    : {width:13, height:13, src:'img/btn_close1.png'},
    opacity     : .7,
    width       : 720,
    height      : 500,
    html        : '',
    url         : '',
    log         : false
};
```


### 다음 업데이트할 내용
css3를 활용한 html5를 사용하여 다양한 효과 적용.  
  
  

	
	
	
	
	
	
	
	
	
	
	
	

	