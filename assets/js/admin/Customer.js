/**
 * Created by inus-c on 2015-05-06.
 */
/**
 * Created by inus-c on 2015-05-06.
 */
var Customer = window.Customer || Customer;

Customer = (function(w) {

    return {
        detailInfoLayer : null,
        showDetailInfoLayer : function(orderId) {
            var order = this;
            order.detailInfoLayer = w.useSmartPop('/mall_new/admin/customer/answer/' + orderId + '/', null,750,800);
        },

        /**
         * 답변등록 서브밋
         */
        answerSubmit: function() {

            $("#answerform").submit();
        },

        closepop: function() {
            parent.Customer.detailInfoLayer.close();
        }


    }

})(window)

$(document).ready(function() {
    Customer.event = (function(customer, w) {
        $('.call-qnacreate').css('cursor', 'pointer');
        $('.call-qnacreate').on('click', function (e) {

            e.preventDefault();
            var orderId = $(this).data('qnaId');
            customer.showDetailInfoLayer(orderId);

        })

        $('.qna_close_btn').on('click', function (e) {
            customer.closepop();
        })




        $('.btn-answer').on('click', function (e) {
            customer.answerSubmit();

        })


    })(Customer, window)
})
