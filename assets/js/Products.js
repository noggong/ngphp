/**
 * Created by noggong on 15. 4. 23..
 */

var Product = window.Product || Product;

Product = (function(w) {

    return {
        productInfo : {
            /** 재고 **/
            strock : 0,

            /** 재고를 사용할 것 인가 **/
            isLimit : false,

            /** 상품명 **/
            goodsName : '',

            /** 상품 가격 **/
            price : 0,

            /** 상품 할인가 (실제 가격) **/
            priceDc : 0,

            /** 선택 옵션 가격 **/
            optionsPrice : 0,

            /** 선택 옵션 수량 **/
            optionsStock : 0,

            /** 상점 id **/
            companySeq : 0,

            /** 상품 id */
            goodsSeq : 0
        },

        init : function($productInfo) {

            product = this;
            $.extend(product.productInfo, $productInfo);
            return product;
        },

        /** 옵션 변경 */
        changeOption : function (optionId, priceApply, priceShip) {
            var product = this;

            $('.bring-to-cart').data('goodsOptionSeq', optionId);
            product.changeStock(1);
            product.insertTotalPrice(priceApply, priceShip, 1);
        },

        /** 장바구니 담을 수량 변경 */
        changeStockInCartButton : function (qty) {
            $('.bring-to-cart').data('count', qty);
        },

        /** 구매 수량 더하기*/
        stockPlus : function() {
            var limit  = (product.productInfo.isLimit == 'Y') ? product.productInfo.optionsStock : false
            var current_qty = product.getStock()

            /** 수량 제한이 없거나 현재 수량이 옵션의 수량 보다 작을때 수량이 더해진다 */
            if (!limit || limit > current_qty) {
                product.changeStock(++current_qty);
                product.changeStockInCartButton(current_qty);
            } else {
                alert('재고가 부족 합니다.');
            }

        },
        /** 구매 수량 빼기*/
        stockMinus : function() {
            var current_qty = product.getStock();

            if (1 < current_qty) {
                product.changeStock(--current_qty);
                product.changeStockInCartButton(current_qty);
            }
        },

        /** 수량 변경 */
        changeStock : function(qty) {
            $('.product-stock').val(qty);
            var priceApply = product.getCurrentOptionPrice();
            var priceShip = product.getShipPrice();
            product.insertTotalPrice(priceApply, priceShip, qty);
        },

        /** 현재 수량 가져오기 */
        getStock : function() {
            return Number($('.product-stock').val());
        },

        /**
         * 선택되어있는 옵션 id 가져오기
         * @returns {*|jQuery}
         */
        getOption : function() {
            return $('.product-options').val();
        },

        getItemJson : function(goods_option_seq, company_seq, goods_seq, order_count)
        {
            return JSON.stringify([{
                'goods_option_seq' : goods_option_seq,
                'company_seq' : company_seq,
                'goods_seq' : goods_seq,
                'order_count' : order_count
            }]);
        },

        /**
         * @param priceApply
         * @param priceShip
         * @param qty
         */
        insertTotalPrice: function(priceApply, priceShip, qty) {
            var total_price = (priceApply + priceShip) * qty;
            $('#total_Price').html(w.numberWithCommas(total_price));
        },

        /**
         * 바로 구매
         */
        goPurchase : function()
        {
            var product = this;

            var cartItemsStringToJson = product.getItemJson(product.getOption(),
                product.productInfo.companySeq,
                product.productInfo.goodsSeq,
                product.getStock());

            var comapanySeqElement = $(document.createElement('input')).
                attr('name', 'company_seq').
                attr('type', 'hidden').
                val(product.productInfo.companySeq);

            var cartItemsStringElement = $(document.createElement('input')).
                attr('name', 'cartItemsString').
                attr('type', 'hidden').
                val(cartItemsStringToJson);

            var is_basket = $(document.createElement('input')).
                attr('name', 'is_basket').
                attr('type', 'hidden').
                val('N');

            var cartForm = $(document.createElement('form')).
                attr('method', 'POST').
                attr('action', '/mall_new/purchase/').
                append(comapanySeqElement).
                append(cartItemsStringElement).
                append(is_basket).appendTo('body');

            cartForm.submit();
        },
		/**
		* 수량체크 가격 up
		*
		*/
		upPrice : function(){
			var price_qty = Number($('.product-stock').val());
			var now_price = $('.ftR').text();
			var return_price = w.numberWithCommas(now_price);
			var total_price = price_qty * now_price;
			
			
			$('#total_Price').val(total_price);
		},

        /**
         * @returns {*|jQuery}
         */
        getCurrentOptionPrice: function() {
            return  $('.product-options option:selected').data('priceApply');
        },

        /**
         * @returns {*|jQuery}
         */
        getShipPrice: function() {
            return $('.optionSec').data('priceShip');
        }


    }
})(window)

$(document).ready(function() {

    /** 상품 관련 이벤트 모음 **/
    Product.event = (function(product) {

        /** 리스트 검색 창 활성화 / 비활성화 */
        $('.search_btn').click(function () {
            $('.searchItem').slideToggle('show', function () {

                if ($('.search_btn').hasClass('open_btn')) {
                    $('.search_btn').removeClass('open_btn');
                    $('.search_btn').addClass('clo_btn');
                } else {
                    $('.search_btn').addClass('open_btn');
                    $('.search_btn').removeClass('clo_btn');
                }
            });
        })

        /** 옵션 변경 */
        $('.product-options').change(function() {
            var priceApply = product.getCurrentOptionPrice();
            var priceShip = product.getShipPrice();

            product.changeOption($(this).val(), priceApply, priceShip);

        })

        /** 수량 더하기 **/
        $('.stock-plus').click(function(e) {
            e.preventDefault();
            product.stockPlus();
        })

        /** 수량 빼기 **/
        $('.stock-minus').click(function(e) {
            e.preventDefault();
            product.stockMinus();
        })

        /** 결제 하러 가기 **/
        $('.go-buy').click(function(e) {
            e.preventDefault();
            product.goPurchase();
        })
		
		/** 수량체크 가격 up**/
		$('.stock-plus').click(function(e){
			e.preventDefault();
			product.upPrice();
		})

    })(Product)
})

