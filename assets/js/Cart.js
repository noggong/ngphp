/**
 * Created by noggong on 15. 4. 24..
 */

var Cart = window.Cart || Cart;

Cart = (function(w){

    return {

        /** 장바구니에서 주문으로 넘어갈때 submit 하는 폼.
        cartForm : null,
        /** 장바구니 넣기 기능**/

        cartBox: {},

        /** 장바구니를 셋팅한다.*/
        init: function() {
            var self = Cart;
            $.each($('.cart-box'), function(i) {
                var cartItemInfo = $(this).find('.cart-item');
                var companySeq = $(this).data('companySeq');
                var totalPrice = 0;
                var shipPrice = 0;
                //self.cartBox[$(this).data('companySeq')];
                if (!self.cartBox[$(this).data('companySeq')]) {
                    self.cartBox[$(this).data('companySeq')] = {};
                };


                $.each(cartItemInfo, function(){
                    self.cartBox[companySeq][$(this).data('goodsSeq')] = {
                        seq        : $(this).data('goodsSeq'),
                        price      : $(this).data('orderPrice'),
                        shipPrice : $(this).data('shipPrice'),
                        count      : $(this).data('count')
                    };
                    totalPrice += $(this).data('orderPrice');
                    if (shipPrice < $(this).data('shipPrice')) {
                        shipPrice = $(this).data('shipPrice');
                    }
                });

                self.cartBox[companySeq]['totalPrice'] = totalPrice;
                self.cartBox[companySeq]['shipPrice'] = shipPrice;

            })


        }    ,
        bringToCart: function ($order_count, $company_seq, $goods_seq, $goods_option_seq) {
            var cart = this;
            $.ajax({
                'url': '/mall_new/cart/',
                'data': {
                    'order_count' : $order_count,
                    'company_seq' : $company_seq,
                    'goods_seq'   : $goods_seq,
                    'goods_option_seq' : $goods_option_seq
                },
                'type': 'POST',
                'dataType': 'JSON',
                'success': function (data) {
                    var status;
                    if (data.rst == '400'){
                    /** 알수 없는 이유로 실패 */
                        return false;
                    } else if (data.rst == '200') {
                    /** 성공 **/
                        status = 'Y'
                    } else if (data.rst == '300') {
                    /** 해당 상품 이미 있음 **/
                        status = 'N';
                    }

                    cart.openResultCateAction(status);

                }
            })
        },

        /** 장바구니 넣기 이후 결과 팝업 */
        openResultCateAction : function(status) {
            w.useSmartPop('/mall_new/cart/result/' + status + '/', false, 400, 150);
        },

        /**
         *
         * @param object cartItem 주문할 상품 목록
         * @param int companySeq 상점 아이디
         */
        goPurchase : function(cartItem, companySeq) {
            var cart = this;
            if (cartItem.length < 1) {
                alert('주문 상품을 선택하여 주세요');
                return false;
            }

            var cartItems = [];
            cartItem.each(function(i) {

                cartItems.push({
                        'order_count': $(this).data('count'),
                        'goods_seq': $(this).data('goodsSeq'),
                        'goods_option_seq': $(this).data('goodsOptionSeq'),
                        'company_seq': companySeq
                    }
                )

            })
            var cartItemsStringToJson = JSON.stringify(cartItems);
            cart.getCartForm(cartItemsStringToJson, companySeq).submit();

        },

        /**
         * 장바구니에서 주문으로 갈때 submit 하는 창 가져오기, 없다면 생성해서 가져오기
         * @param string cartItemsStringToJson
         * @param int companySeq
         * @returns $Element
         */
        getCartForm : function(cartItemsStringToJson, companySeq)
        {
            var cart = this;

            if (!cart.cartForm) {

                var comapanySeqElement = $(document.createElement('input')).
                    attr('name', 'company_seq').
                    attr('type', 'hidden').
                    val(companySeq);

                var cartItemsStringElement = $(document.createElement('input')).
                    attr('name', 'cartItemsString').
                    attr('type', 'hidden').
                    val(cartItemsStringToJson);

                var is_basket = $(document.createElement('input')).
                    attr('name', 'is_basket').
                    attr('type', 'hidden').
                    val('Y');

                cart.cartForm = $(document.createElement('form')).
                    attr('method', 'POST').
                    attr('action', '/mall_new/purchase/').
                    append(comapanySeqElement).
                    append(cartItemsStringElement).
                    append(is_basket).appendTo('body');

            }
            return cart.cartForm;
        },

        removeGoodsInCart: function(removeItem) {
            var self = this;
            var parent = $(removeItem[0]).closest('.cart-box')
            var companySeq = parent.data('companySeq');
            $.each(removeItem, function() {
                var goodsSeq = $(this).data('goodsSeq');
                //self.cartBox에서 삭제
                self.cartBox[companySeq].totalPrice = Number(self.cartBox[companySeq].totalPrice) - Number(self.cartBox[companySeq][goodsSeq].price);
                delete self.cartBox[companySeq][goodsSeq];

            });

            //배송비 있는지 체크 후 반영
            var currentShipPrice = 0;
            $.each(self.cartBox[companySeq], function() {
                if (currentShipPrice < this.shipPrice) {
                    currentShipPrice = this.shipPrice;
                }
            })
            self.cartBox[companySeq].shipPrice = currentShipPrice;
            parent.find('.ship_price').html(w.numberWithCommas(self.cartBox[companySeq].shipPrice) + '원');

            //실제 토탈가격 체크
            parent.find('.totalPrice').html(w.numberWithCommas(self.cartBox[companySeq].totalPrice) + '원');
            //배송비 + 토탈가격
            parent.find('.total_price_include_ship').html(w.numberWithCommas(self.cartBox[companySeq].totalPrice + self.cartBox[companySeq].shipPrice) + '원');

            //self.cartBox에서 상품이 몇개 있는 지 체크 - 한개도 없다면 폼 전체 remove
            var itemLenght = Number(Object.keys(self.cartBox[companySeq]).length - 2);
            if (itemLenght === 0) {
                delete self.cartBox[companySeq];
                parent.closest('form').remove();

                if (!Object.keys(self.cartBox).length) {
                    alert('장바구니에 담겨있는 상품이 없습니다.');
                    location.href = '/mall_new/products/';
                }
            }
            //각 아이템 삭제
            self.doRemoveGoodsItem(removeItem);


        },
        /**
         * 장바구니에서 삭제
         */
        doRemoveGoodsItem : function(removeItem) {
            var removeItems = [];

            removeItem.each(function(i) {
                removeItems.push($(this).data('goodsSeq') + '_' + $(this).data('goodsOptionSeq'));
            });

            var removeItemsToString = removeItems.join('::');
            removeItem.closest('tr').remove();

            $.ajax({
                url : '/mall_new/cart/remove/',
                data : {'removeItemsToString': removeItemsToString},
                type : 'POST',
                dataType : 'json'
            })
        }

    }

})(window);


$(document).ready(function() {
    Cart.event = (function(cart) {


        /** 장바구니 넣기 이벤트 */
        $('.bring-to-cart').on('click', function (e) {
            e.preventDefault();
            var order_count = $('.product-stock').val();
            var company_seq = $(this).data('companySeq');
            var goods_seq = $(this).data('goodsSeq');
            var goods_option_seq = $(this).data('goodsOptionSeq');
            cart.bringToCart(order_count, company_seq, goods_seq, goods_option_seq);

        })

        /** 상점별 전체 결제 **/
        $('.cart-btn-gopurchase').on('click', function (e) {
            e.preventDefault();
            var item = $(this).closest('form').find('input.cart-item');
            var companySeq = $(this).closest('form').find('input[name=company_seq]').val();

            cart.goPurchase(item, companySeq);

        }),

        /** 상점별 선택 상품 결제 */
        $('.cart-btn-gopurchase-selected').on('click', function (e) {
            e.preventDefault();
            var item = $(this).closest('form').find('input.cart-item:checked');
            var companySeq = $(this).closest('form').find('input[name=company_seq]').val();

            cart.goPurchase(item, companySeq);

        })

        /** 장바구니에서 삭제 **/
        $('.chkDel').on('click', function(e) {
            e.preventDefault();
            var closest_parent = $(this).closest('.box');
            var removeItem = closest_parent.find('.cart-item:checked')
            if (removeItem.length < 1 ) {
                alert('삭제할 상품을 선택 해 주세요');
                return false;
            }

            cart.removeGoodsInCart(removeItem);
        })

        /** 장바구니 상품 체크박스 전체 선택 **/
        $(".chkAll").on('click', function(e) {
            e.preventDefault();
            var closest_parent = $(this).closest('.box');
            var checkItem = closest_parent.find('.cart-item:checkbox');

            if(checkItem.prop("checked") == false) {
                checkItem.prop("checked",true);
            } else {
                checkItem.prop("checked",false);
            }
        })

    })(Cart);
})



