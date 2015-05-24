/**
 * Created by noggong on 15. 4. 20..
 */
var Admin =

$(document).ready(function () {
    var admin = window.admin || admin;

    Admin.event = (function (w) {

        'use strict';

        /** 폼을 ajax 로 보내는 메소드 **/
        $('.form-ajax').on('submit', function () {

            var submitEvent;
            var result;

            if (submitEvent = $(this).data('submitEvent')) {

                /** todo: eval 비추천: 추후에 다른 방법을 찾는다 */
                eval('result = w.' + submitEvent + '($(this))');

                if (!result['res']) {
                    alert(result['msg']);
                    return false;
                }
            }

            if ($('input[required]').filter(function() { return $(this).val() == ""; }).length > 0 ) {
                alert('필수 값을 입력해 주세요');
                return false;
            }

        })

        /** ajax form 전송 **/
        $('.form-ajax').ajaxForm({
            dataType : 'json',
            success : function(data) {
                if (data.rst == 200) {
                    alert('처리되었습니다.');
                    location.reload();
                } else {
                    alert(data.msg);
                }
            }
        })

        /** 스위치 체크박스 **/
        $('input.switch-checkbox').bootstrapSwitch();

        /** 스위치 체크박스 클릭시 이벤트 **/
        $('input.switch-checkbox').on('switchChange.bootstrapSwitch', function(event, state) {

            /** todo: eval 비추천: 추후에 다른 방법을 찾는다 */
            if ($(this).data('onSwitch')) {
                eval($(this).data('onSwitch') + "($(this), state)");

            }

        });

    })(window)
})