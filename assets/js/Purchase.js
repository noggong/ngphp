/**
 * Created by noggong on 15. 4. 26..
 */

/**
 * Created by noggong on 15. 4. 24..
 */

var Purchase = window.Purchase || Purchase;

Purchase = (function(w){


    return {

        PurchaseInfo : {
            nasJsHost: (('https:' == document.location.protocol) ? 'https://' : 'http://'),

            NSmart_CAMP: '9490',

            NSmart_Page: '21930',

            NSmart_TURL: '//n90.nsmartad.com',

            actionURL  : ''
        },

        /** 주문 상세보기 레이어 **/
        detailInfoLayer : null,

        /** 현금영수증 신청 레이어 **/
        receiptRequestLayer : null,

        /** 현금영수증 상세보기 레이어 **/
        receiptDetailLayer : null,

        /** 반품 레이어 **/
        refundLayer : null,

        checkoutForm : null,

        init : function() {
            var purchase = this;

            purchase.checkoutForm = $('#checkout-form');

            /** 
             * http://plugin.inicis.com/pay61_secuni_cross.js 내 함수
             * 플러그인 설치 확인
             */
            w.StartSmartUpdate();

            purchase.setGlobalVarForInisys();
            document.write(unescape("%3Cscript src='//n00.nsmartad.com/etc?id=9' type='text/javascript'%3E%3C/script%3E"));

            purchase.changeClickControl('enable')
        },

        /**
         * 결제 진행시 글로벌로 필요한 변수들을 글로벌 스코프로 정의해준다.
         */
        setGlobalVarForInisys : function()
        {
            var purchase = this;

            w.nasJsHost = purchase.PurchaseInfo.nasJsHost;

            w.NSmart_CAMP = purchase.PurchaseInfo.NSmart_CAMP;

            w.NSmart_Page = purchase.PurchaseInfo.NSmart_Page;

            w.NSmart_TURL = purchase.PurchaseInfo.NSmart_TURL;
        },

        /**
         * 결제하기 버튼 클릭 상태 체크 변경
         * @param string status (enable,disable)
         */
        changeClickControl : function(status)
        {
            var purchase = this;
            purchase.checkoutForm.find('[name=clickcontrol]').val(status);
        },

        /**
         * 결제방법 변경
         * @param string method (CARD, DirectBank)
         */
        changePaymethod : function(method)
        {
            var purchase = this;
            purchase.checkoutForm.find('[name=gopaymethod]').val(method);
        },
        /**
         * 결제하기 상태 가져오기
         * @returns string
         */
        getClickControl : function()
        {
            var purchase = this;
            return purchase.checkoutForm.find('[name=clickcontrol]').val();
        },

        /**
         * 결제 상품 이름  가져오기
         * @returns string
         */
        getGoodsName : function()
        {
            var purchase = this;
            return purchase.checkoutForm.find('[name=goodname]').val();
        },

        /**
         * 결제 진행
         * @param string checkout_method
         */
        doCheckout : function(checkout_method)
        {
            var purchase = this;

            /** 필수 정보 폼 검사 */
            if (!purchase.validationCheckoutForm(purchase.checkoutForm)) {
                return false;
            }

            /** 결제 방법 등록 for inisys */
            purchase.changePaymethod(checkout_method);

            /** 결제 방법별로 결제 실행*/
            if (checkout_method == 'CARD' || checkout_method == 'DirectBank') {
                purchase.checkoutByInisys();
            } else if (checkout_method == 'Bank') {
                purchase.checkoutBank();
            }
        },

        /**
         * 카드 결제 진행
         */
        checkoutByInisys : function()
        {
            var purchase = this;
            purchase.doPay();

        },

        /**
         * 이니시스 샘플 코드에 있던 결제전 실행 코드
         */
        doPay: function()
        {
            var purchase = this;
            // MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
            // 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
            // 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
            // 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.

            if (purchase.getClickControl() == "enable") {

                if (purchase.getGoodsName() == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
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
                    if (w.MakePayMessage(purchase.checkoutForm[0])) {
                        purchase.changeClickControl('disable');
						purchase.checkoutForm.submit();
                        return true;
                    }
                    else {
                        if (w.IsPluginModule())//plugin타입 체크
                        {
                            alert("결제를 취소하셨습니다.");
                        }
                        return false;
                    }
                }
                return false;
            }
            else {
                return false;
            }
        },

        /**
         * 무통장 입금 결제 진행
         */
        checkoutBank : function()
        {
            var purchase = this;
            w.useSmartPop(false, $('#bank_layer'), 500, 350);
        },

        /**
         * 결제전 폼 검증
         */
        validationCheckoutForm : function(f)
        {
            var purchase = this;
            var rst =  true;
            f.find('input[required]').each(function(i) {
                if (Validation.trimNvl($(this).val())) {
                    alert($(this).attr('title') + '을(를) 입력해 주세요.');
                    $(this).focus();
                    rst = false;
                }
            });

            return rst;
        },
        /**
         * 주문상세 보기
         * @param orderId
         */
        showDetailInfoLayer : function(orderId) {
            var purchase = this;
            purchase.detailInfoLayer = w.useSmartPop('/mall_new/order/' + orderId + '/', null, 756);
        },
        /**
         *
         */
        closeRequestLayer : function() {
            parent.Purchase.receiptRequestLayer.close();
        },
        /**
         *
         */
        closeDetailLayer : function() {
            parent.Purchase.receiptDetailLayer.close();
        },
        /**
         * 현금영수증 신청
         * @param orderId
         */
        showReceiptRequestLayer : function(orderId){
            var purchase = this;
            purchase.receiptRequestLayer = w.useSmartPop('/mall_new/order/receipt/request/' + orderId + '/', null, 507);
        },
        /**
         * 현금영수증 상세보기
         * @param orderId
         */
        showReceiptDetailLayer : function(orderId){
            var purchase = this;
            purchase.receiptDetailLayer = w.useSmartPop('/mall_new/order/receipt/detail/' + orderId + '/', null, 548);
        },
        /**
         * 반품신청 / 주문취소 레이어 팝업
         * @param orderId
         * @param status
         */
        showRefundLayer : function(orderId ,status) {
            var purchase = this;
            purchase.refundLayer = useSmartPop('/mall_new/order/'+ status +'/' + orderId + '/', null, 756);
        },
        /**
         * 주문상세선택 닫기
         */
        closebtn : function(){
            parent.Purchase.refundLayer.close();
        },
        /**
         * 레이어 팝업 닫기
         * @param popKind
         */
        closeLayer : function(popKind) {
            parent.Purchase[popKind].close();
        },

		/**
		* Daum 주소찾기
		*/
		searchPost : function() {
			new daum.Postcode({
				oncomplete: function(data) {
					document.getElementById('ZIP1').value = data.postcode1;
					document.getElementById('ZIP2').value = data.postcode2;
					document.getElementById('ADDR1').value = data.address;
					document.getElementById('ADDR2').focus();

				}
			}).open();
		},
		
		changeInputType: function(type, checked_el) {
            var purchase = this;
			if (type == 1) {
				purchase.originMode();
			} else{
				purchase.addNew(checked_el);
			}
		},
		/**
		* 폼 값 불러오기
		*/
		originMode : function() {
			var purchase = this;
			purchase.checkoutForm.resetForm();
		}, 
		/**
		* 폼 리셋
		*/
		addNew : function(checked_el) {
			var purchase = this;
			console.log(purchase.checkoutForm);
			purchase.checkoutForm.clearForm();
			console.log(checked_el);
			checked_el.attr('checked', true);
			//$('input:radio[name=addressIs]:input[value=2]').attr("checked", true);
			
		},
		month : function() {

		},
        showReceiptLayer : function(companyId, purchaseId) {
            var purchase = this;
            purchase.detailInfoLayer = w.useSmartPop('/mall_new/order/receipt/' + companyId + '/' + purchaseId + '/', null,540);
        },
        /**
         * 구매영수증 팝업 닫기
         */
        closeLayer : function() {
            parent.Purchase.detailInfoLayer.close();
        },
        /**
         * 로그인
         * @param data
         */
        tempFormSubmit : function(data) {
            if (data.rst == 200) {
                parent.location.reload();
            } else {
                alert(data.msg);
            }
        },
        /**
         * 취소 / 반품 신청 폼
         * @param data
         */
        refundCancelFormSubmit : function(data) {
            if (data.rst == 200) {
                parent.location.reload();
            } else {
                alert(data.msg);
            }
        },

        /**
         * 현금영수증 신청 폼
         * @param data
         */
        receiptRequestFormSubmit : function(data) {
            if (data.rst == 200) {
                alert(data.msg);
                parent.Purchase.receiptRequestLayer.close();
                parent.Purchase.showDetailInfoLayer(data.orderId);
            } else {
                alert(data.msg);
            }
        },

        searchKeyword:function() {
            alert();
        }



    }
})(window)


$(document).ready(function() {
    /** 상품 관련 이벤트 모음 **/
    Purchase.event = (function(w, purchase)
    {

        $('.purchase-go-checkout').on('click', function() {

            var checkout_method = $('input[name=pay_type]:checked').val();
            purchase.doCheckout(checkout_method);
        })

        $('body').on('click', '.checkout-submit', function() {
            var payerNameElement = $('input[name=payer-name]');
            var payerName = payerNameElement.val();
            if ($.trim(payerName) == '') {
                payerNameElement.focus();
                alert('입금자명을 입력해 주세요.');
                return false;
            };

            purchase.checkoutForm.append(payerNameElement).submit();
        })

        /** 반품 신청 */
        $('.purchase-refund').on('click', function(e) {
            e.preventDefault();
            var orderId = $(this).closest('tr').data('orderId');
            var statusId = $(this).data('statusId');
            var status = 'refund';
            purchase.showRefundLayer(orderId,status);
        })

        /** 주문 취소 신청 */
        $('.purchase-cancel').on('click', function(e) {
            e.preventDefault();
            var orderId = $(this).closest('tr').data('orderId');
            var statusId = $(this).data('statusId');
            var status = 'cancel';
            console.log(statusId);
            purchase.showRefundLayer(orderId,status);
        })

        /** 배송 추적 */
        $('.purchase-trace').on('click', function(e) {
            e.preventDefault();
            var orderId = $(this).closest('tr').data('orderId');
            //useSmartPop('/mall_new/');
        })

        /** 교환 신청 **/
        //$('.purchase-change').on('click', function(e) {
        //    e.preventDefault();
        //    var orderId = $(this).closest('tr').data('orderId');
        //    //useSmartPop('/mall_new/order/refund/' + orderId + '/', null, 756);
        //})

        /** 주문 상세 **/
        $('.purchase-detail').on('click', function(e) {
            e.preventDefault();
            var orderId = $(this).closest('tr').data('orderId');
            purchase.showDetailInfoLayer(orderId);
        })

        /** 팝업 닫기 **/
        $('.pop-close').on('click', function(e) {
            e.preventDefault();
            var popKind = $(this).data('popKind');
            purchase.closeLayer(popKind);
        })

		/** Daum 주소 찾기 **/
		$('#btn_zip').on('click', function(e) {
            e.preventDefault();
			purchase.searchPost();
		});

		/** 새로 입력 **/
		$('input[name=addressIs]').on('click', function(e) {
			
			var type = $(this).val();
			purchase.changeInputType(type, $(this));

		});

		/** 1주일 기간 **/
		$('.month0').on('click', function(e) {
			purchase.month();

		});

        $("#start_dt").datepicker({
            changeMonth:true,
            changeYear:true,
            dateFormat:"yy-mm-dd"
        });

        $("#end_dt").datepicker({
            changeMonth:true,
            changeYear:true,
            dateFormat:"yy-mm-dd"
        });

        /** 구매영수증 레이어 열기 **/
        $("#receipt_btn").on('click',function(e){
            e.preventDefault();
            //console.log($('#receipt_btn').data('companySeq'));

            var companyId = $('#receipt_btn').data('companySeq');
            var purchaseId = $('#receipt_btn').data('purchaseSeq');

            purchase.showReceiptLayer(companyId, purchaseId);
        });

        /** 구매영수증 팝업 닫기 **/
        $('#close_btn').on('click', function(e) {
            e.preventDefault();
            purchase.closeLayer();
        });

        /** 구매영수증 팝업 닫기 **/
        $('#print_btn').on('click', function(e) {
            e.preventDefault();
            window.print();
        });

        /** 로그인 이벤트 */
        $('#temp-order-form').ajaxForm({
            dataType : 'json',

            beforeSubmit: function(){
                if (Validation.trimNvl($('#pr_order_number').val())) {
                    alert('주문번호를 입력해 주세요.');
                    $(this).find('#user_name').focus();
                    return false;
                }

                if (Validation.trimNvl($('#passwd').val())) {
                    alert('비밀번호를 입력해 주세요.');
                    $(this).find('#passwd').focus();
                    return false;
                }
            },
            success: purchase.tempFormSubmit
        });
        /** 주문상세선택 닫기 */
        $('#refCancelClose_btn').on('click',function(e){
            e.preventDefault();
            purchase.closebtn();
        });

        /** 현금영수증 신청 */
        $('#orderReceipt_request').on('click',function(e){
            e.preventDefault();
            var orderId = $("#dataId").text();
            parent.Purchase.detailInfoLayer.close();
            parent.Purchase.showReceiptRequestLayer(orderId);
        });

        /** 현금영수증 상세보기 */
        $('#orderReceipt_detail').on('click',function(e){
            e.preventDefault();
            var orderId = $("#dataId").text();
            parent.Purchase.detailInfoLayer.close();
            parent.Purchase.showReceiptDetailLayer(orderId);
        });

        /** 현금영수증 신청 뒤로가기 버튼*/
        $('#back_receiptRequest').on('click',function(e){
            e.preventDefault();
            var orderId = $(this).data('orderId');
            parent.Purchase.receiptRequestLayer.close();
            parent.Purchase.showDetailInfoLayer(orderId);
        });

        /** 주문상세선택 닫기 */
        $('#close_receiptRequest').on('click',function(e){
            e.preventDefault();
            purchase.closeRequestLayer();
        });

        /** 현금영수증 신청 뒤로가기 버튼*/
        $('#back_receiptDetail').on('click',function(e){
            e.preventDefault();
            var orderId = $(this).data('orderId');
            parent.Purchase.receiptDetailLayer.close();
            parent.Purchase.showDetailInfoLayer(orderId);
        });

        /** 주문상세선택 닫기 */
        $('#close_receiptDetail').on('click',function(e){
            e.preventDefault();
            purchase.closeDetailLayer();
        });

        /** 반품 / 취소 신청 폼 */

        $('#refund_cancel').ajaxForm({

            dataType : 'json',

            beforeSubmit: function(){
                if (Validation.trimNvl($('#comment').val())) {
                    alert('판매자에게 요청할 메시지를 입력해 주세요.');
                    $(this).find('#user_name').focus();
                    return false;
                }
            },
            success: purchase.refundCancelFormSubmit
        });

        /** 주문관리 검색*/
        $('#btn_search').on('click', function(e) {
            e.preventDefault();
            purchase.searchKeyword();
        });

        /** 현금영수증 신청 **/
        $('#receiptRequest').ajaxForm({
            dataType : 'json',
            beforeSubmit: function(){
            },
            success: purchase.receiptRequestFormSubmit
        });


    })(window, Purchase)
})
