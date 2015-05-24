/**
 * Created by noggong on 15. 5. 5..
 */

var OrderAction = window.OrderAction || OrderAction;

OrderAction = (function(w) {

    return {

        purchaseStatus: {},
        deliveryLayer: null,

        setPurchaseStatus: function(jsonString) {
            var orderAction = this;

            orderAction.purchaseStatus = JSON.parse(jsonString);
        },

        doAction: function(orderNumber, status, callback) {

            var option = {
                url: '/mall_new/admin/orderAction/' + status + '/' + orderNumber + '/',
                type: 'POST',
                dataType: 'JSON'
            }

            if (callback) {
                option['success'] = callback;
            }
            $.ajax(option);
        },

        actionCallback: function(data) {

        },

        openApplyDeliveryLayer: function(orderNumber) {

            var orderAction = this;
            orderAction.deliveryLayer = w.useSmartPop('/mall_new/admin/deliveryLayer/' + orderNumber + '/');
        },

        successApplyDelivery: function() {
            var orderAction = this;
            orderAction.closeDelivery();
            orderAction.actionCallback();

        },

        closeDelivery: function() {
            parent.OrderAction.deliveryLayer.close();
        }
    }
})(window)


$(document).ready(function() {

    OrderAction.event = (function(orderAction) {

        /** 결제 완료 버튼 클릭 */
        $('#purchase-list-table').on('click', '.order-action-purchased', function() {

            var orderNumber = $(this).closest('tr').data('orderNumber');
            orderAction.doAction(orderNumber, 'purchase', orderAction.actionCallback);
        })

        /** 주문 취소 버튼 클릭 */
        $('#purchase-list-table').on('click', '.order-action-order-cancel', function() {

            var orderNumber = $(this).closest('tr').data('orderNumber');
            orderAction.doAction(orderNumber, 'orderCancel', orderAction.actionCallback);
        })

        /** 결제 취소 버튼 클릭 */
        $('#purchase-list-table').on('click', '.order-action-purchase-cancel', function() {

            if (confirm('결제 취소시 이니시스를 이용한 결제라면 실제 결제가 취소됩니다. 취소하시겠습니까?')) {
                var orderNumber = $(this).closest('tr').data('orderNumber');

                orderAction.doAction(orderNumber, 'purchaseCancel', orderAction.actionCallback);
            }
        })

        /** 배송 완료 버튼 클릭 */
        $('#purchase-list-table').on('click', '.order-action-appldelivery', function() {

            var orderNumber = $(this).closest('tr').data('orderNumber');

            orderAction.openApplyDeliveryLayer(orderNumber);
        })

        /** 배송 정보 버튼 클릭 - 배송완료클릭과 동일함 */
        $('#purchase-list-table').on('click', '.order-action-deliver-info', function() {

            var orderNumber = $(this).closest('tr').data('orderNumber');

            orderAction.openApplyDeliveryLayer(orderNumber);
        })

        $('#btn_close_delevery').click(function() {
            orderAction.closeDelivery();
        })

    })(OrderAction, window)
})

