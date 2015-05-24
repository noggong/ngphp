/**
 * Created by noggong on 15. 4. 24..
 */

var User = window.User || User;

User = (function(w){


    return {

        loginStatus : false,

        loginPopLayer : null,

        loginOlderPopLayer : null,

        agreeLayer : null,

        referrer : null,
        /**
         * 로그인 되어있는지 체크
         * @returns {boolean}
         */
        isLogin : function() {
            return this.loginStatus;
        },

        /**
         * 회원 정보
         */
        userInfo : {
            userNum: null,
            userName: null,
            userId: null,
            isMobile: null
        },

        /**
         * 회원 생성자
         * @param bool isLogin
         * @param array userInfo
         */
        init : function(isLogin, userInfo) {
            var user = this;
            user.loginStatus = isLogin;
            $.extend(this.userInfo, userInfo);

            user.referrer = document.referrer;
        },
        /**
         * 구매내역 로긴 팝업 호출
         */
        callOlderLoginLayer : function()
        {
            var user = this;
            user.loginOlderPopLayer = useSmartPop('/mall_new/order/login/', null, 400, 300);
        },
        /**
         * 로긴 팝업 호출
         */
        callLoginLayer : function()
        {
            var user = this;
            user.loginPopLayer = useSmartPop('/mall_new/login/', null, 400, 300, false);
        },

        /**
         * 비회원 등록 팝업 호출
         */
        callAgreeLayer : function()
        {
            var user = this;
            user.agreeLayer = useSmartPop('/mall_new/login/temp/', null, 600, false);
        },

        /**
         * 로그인 상태가 아니면 회원 로긴창 호출
         */
        checkAndLoadLayer : function()
        {
            var user = this;
            if (!user.isLogin()) {
                user.callLoginLayer();
            }
        },

        /**
         * 로그인
         * @param data
         */
        loginSubmit : function(data) {
            if (data.success) {
                parent.location.reload();
            } else {
                alert(data.message);
            }
        },

        /**
         * 비회원 등록
         * @param data
         */
        saveTempUser : function(data) {
            if (data.rst == 200) {
                parent.location.reload();
            } else {
                alert(data.message);
            }
        },

        /**
         * 뒤로가기 버튼 클릭
         */
        backAction: function() {
            var user = this;
            parent.location.href = user.referrer;
        },
        /**
         * 구매내역 페이지 로그인 상태가 아니면 회원 로긴창 호출
         */
        olderCheckAndLoadLayer : function()
        {
            var user = this;
            if (!user.isLogin()) {
                user.callOlderLoginLayer();
            }
        }
    }
})();

$(document).ready(function() {
    User.event = (function(user, w) {

        /** 구매내역 로그인 팝업 뒤로가기 **/
        $('#order-back-button').on('click', function (e) {
            e.preventDefault();
            //parent.User.loginOlderPopLayer.close();
            parent.history.back();

        })

        /** 로그인 팝업 뒤로가기 **/
        $('#login-back-button').on('click', function (e) {
            e.preventDefault();
            parent.User.backAction();

        })

        /** 비회원 구매 **/
        $('#login-goagree-button').on('click', function(e) {
            //e.preventDefault();
            //console.log('1');
            parent.User.loginPopLayer.close();
            parent.User.callAgreeLayer();

            return false;

        });

        /** 로그인 이벤트 */
        $('#login-form').ajaxForm({
            dataType : 'json',

            beforeSubmit: function(){
                if (Validation.trimNvl($('#user_id').val())) {
                    alert('아이디를 입력해 주세요.');
                    $(this).find('#user_id').focus();
                    return false;
                }

                if (Validation.trimNvl($('#passwd').val())) {
                    alert('비밀번호를 입력해 주세요.');
                    $(this).find('#passwd').focus();
                    return false;
                }
            },
            success: user.loginSubmit
        })

        /** 비회원 등록 이벤트 */
        $('#temp-agree-form').ajaxForm({
            dataType : 'json',

            beforeSubmit: function(){
                if (Validation.trimNvl($('#name').val())) {
                    alert('구매자를 입력해 주세요.');
                    $(this).find('#name').focus();
                    return false;
                }

                if (Validation.trimNvl($('#passwd').val())) {
                    alert('비밀번호를 입력해 주세요.');
                    $(this).find('#passwd').focus();
                    return false;
                }

                if (Validation.trimNvl($('#email').val())) {
                    alert('이메일을 입력해 주세요.');
                    $(this).find('#email').focus();
                    return false;
                }

                if (Validation.trimNvl($('#tel').val())) {
                    alert('연락처를 입력해 주세요.');
                    $(this).find('#tel').focus();
                    return false;
                }

                if ($('#agree1:checked').length < 1) {
                    alert('이용약관을 동의 하여 주세요.');
                    $(this).find('#agree1').focus();
                    return false;
                }

                if ($('#agree2:checked').length < 1) {
                    alert('개인정보 수집 / 이용에 동의 하여 주세요.');
                    $(this).find('#agree1').focus();
                    return false;
                }
            },
            success: user.saveTempUser
        })

        /** 비회원 구매 **/

        /** 비회원 구매 뒤로가기*/
        $('#agree-back-button').on('click', function() {
            parent.User.agreeLayer.close();
            parent.User.callLoginLayer();
        })

    })(User, window);
})