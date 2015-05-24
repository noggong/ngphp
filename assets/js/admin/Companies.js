var Companies = window.Companies || Companies;

Companies = (function(w) {

    return {

        /**
         * 상점 등록시 실행
         * @param {element} f form
         * @returns {*}
         */
        submit : function(f) {
            var fEl = f.find('.password:eq(0)')
            var first = fEl.val();
            var second = f.find('.password:eq(1)').val();

            if (first.length < 6 || first != second) {
                fEl.focus();
                return {'res' : false, 'msg' :'비밀번호를 확인해 주세요'};

            }
            return {'res' : true};
        },

        /**
         * 패스워드 입력 체크 (비밀번호와, 비밀번호 확인이 같은지, 6자리 이상인지)
         * @param element
         */
        checkPassword: function(element) {

            var first = $('.password:eq(0)').val();
            var second = $('.password:eq(1)').val();

            if (first.length >= 6 && first == second) {
                element.closest('.form-group').removeClass('has-error');
                element.closest('.form-group').addClass('has-success');
            } else {
                element.closest('.form-group').addClass('has-error');
                element.closest('.form-group').removeClass('has-success');

            }
        },

        /**
         * 리스트 페이지에서 활성화 비활성화 스위치
         * @param {element} element
         * @param boolean state 스위치 상태
         */
        switch: function(element, state) {
            var isActive;

            if (state) {
                isActive = 'Y';
            } else {
                isActive = 'N';
            }

            var company_id = element.data('companyId');
            $.ajax({
                url : '/mall_new/admin/company/' + company_id + '/active/',
                data : 'is_active=' + isActive,
                type : 'post',
                dataType : 'json',
            })
        }
    }

})(window)


$(document).ready(function() {

    Companies.event = (function(companies, w) {

        /** 우편번호 찾기 로드 */
        $(".postcodify_postcode6").postcodifyPopUp();

        /** 우편번호기 찾기 호출 */
        $(".postcodify_postcode6").on('focus', function() {
            $(this).trigger('click');
        })


        /**
         * 비밀번호에 키보드 입력시 이벤트
         */
        $('.password').keyup(function () {

            companies.checkPassword($(this));

        })
    })(Companies, window)
})

