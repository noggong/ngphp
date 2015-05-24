/**
 * Created by noggong on 15. 5. 5..
 */

var Order = window.Order || Order;

Order = (function(w) {

    return {


        itemToggle: function(selector) {
            selector.closest('tr').next().toggle();
            selector.toggleClass('glyphicon-plus');
            selector.toggleClass('glyphicon-minus');
        },

        /**
         * 주문상세 보기
         * @param orderId
         */
        showDetailInfoLayer : function(orderNumber) {
            var order = this;
            order.detailInfoLayer = w.useSmartPop('/mall_new/admin/purchases/detailview/' + orderNumber + '/', null, '750');
        },

        /**
         * 레이어 팝업 닫기
         * @param popKind
         */
        closeLayer : function() {
            parent.Order.detailInfoLayer.close();
        }

    }
})(window)


$(document).ready(function() {

    Order.event = (function(order) {


        /** 자세히 보기 버튼 클릭 */
        $('.order-detailview-btn').click(function(e) {
            e.preventDefault();
            var orderNumber = $(this).data('orderNumber');
            order.showDetailInfoLayer(orderNumber);
        });

        /** 자세히보기 팝업 닫기 **/
        $('#btn_x').on('click', function(e) {
            e.preventDefault();
            order.closeLayer();
        });

        /** 주문 상세 내역 펼쳐보기 **/
        $('.item-view-btn').on('click', function(e) {
            e.preventDefault();
            order.itemToggle($(this));
        });

    })(Order, window)
})

