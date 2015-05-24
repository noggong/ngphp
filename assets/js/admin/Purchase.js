/**
 * Created by inus-c on 2015-05-15.
 */


var Purchase = window.Purchase || Purchase;

Purchase = (function(w){


    return {

        searchKeyword:function() {
            var purchases_search_category = $("select[name=purchases_search_category]").val();
            var purchases_search_text = $('#purchases_search_text').val();



            alert(purchases_search_category+purchases_search_text);

        }



    }
})(window)


$(document).ready(function() {

    Purchase.event = (function(w, purchase)
    {
        ///** 주문관리 검색*/
        //$('#btn_search').on('click', function(e) {
        //    var f = document.applyform ;
        //    if( f.purchases_search_text.value.length < 1) {
        //        alert("검색어를 입력해 주세요.") ;
        //        f.purchases_search_text.focus() ;
        //        return false ;
        //    }
        //    e.preventDefault();
        //    purchase.searchKeyword();
        //});


    })(window, Purchase)
})


