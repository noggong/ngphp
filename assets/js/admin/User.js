/**
 * Created by noggong on 15. 4. 24..
 */

var User = window.User || User;

User = (function(w){


    return {
        isSuperUser : false,
        companyId   : 0,

        /**
         * 회원 생성자
         * @param bool isLogin
         * @param array userInfo
         */
        init : function(isLogin, userInfo) {
            var user = this;
            $.extend(this.userInfo, userInfo);

            user.referrer = document.referrer;
        }

    }
})();

$(document).ready(function() {
    User.event = (function(user, w) {



    })(User, window);
})
