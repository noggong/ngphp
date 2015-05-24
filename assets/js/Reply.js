/**
 * Created by inus-c on 2015-05-08.
 */

var Reply = window.Reply || Reply;

Reply = (function(w){
    return {

        replySubmit: function() {
            if (!w.User.isLogin()) {
                var chk = confirm("로그인 하신후 참여하실 수 있습니다.\n로그인 하시겠습니까?") ;
                if(chk) {
                    parent.location.href = "/member/login.php?return_url="+location.href;
                }else{
                    return false;
                }
            }else{
                var reply = this;
                var options = {
                    type:'POST',
                    url:'/mall_new/qna/forreply/',
                    dataType : 'JSON',
                    success: function(data, textStatus) {
                        if (data.rst == '200') {
                            alert('등록 되었습니다.');
                            var reply_info = data.data;
                            reply.insertToTableForPart(reply_info.user_name, reply_info.question, reply_info.created_at );
                        }else{
                            alert(data.msg);
                        }


                    }
                };
                $("#replyform").ajaxSubmit(options) ;
            }
        },

        /**
         *
         * @param int id
         * @param string title
         * @param string unit
         */
        insertToTableForPart: function(user_name, question, created_at) {
            var div = $('.part-table');

            var ul = document.createElement('ul');


            var li_s =  '<li><div class="txt_reple">';
            var uname = '<strong class="name">'+ user_name +'</strong>';
            var rdate = '<span class="date">'+ created_at +'</span>';
            var cmt = '<div>'+ question +'</div></li>';
            var li_e =  '</div></li>';

            div.prepend(
                $(ul).append(li_s, uname, rdate, cmt,li_e)
            );
        },



        login_confirm_btn: function() {

            if (!w.User.isLogin()) {
                var chk = confirm("로그인 하신후 참여하실 수 있습니다.\n로그인 하시겠습니까?") ;
                if(chk) {
                    parent.location.href = "/member/login.php?return_url="+location.href;
                }else{
                    return false;
                }
            }
        }

    }
})(window);

$(document).ready(function() {
    Reply.event = (function(reply, w) {

        $('.btnReple').on('click', function(e) {
            reply.replySubmit();
        });

        $('#question').on('click', function(e) {
            reply.login_confirm_btn();
        });


    })(Reply, window);
});



