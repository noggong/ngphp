/**
 * Created by noggong on 15. 5. 21..
 */


var SearchShip = window.SearchShip || SearchShip;

SearchShip = (function(w) {

    return {
        searchDaehan: function(deliveryNum) {
            var self = SearchShip;
            var form = $('<form>')[0];
            form.action = 'https://www.doortodoor.co.kr/parcel/doortodoor.do';
            form.method = 'post';


            $(form).append($('<input />').attr('name', 'fsp_action').val('PARC_ACT_002')).
                append($('<input />').attr('name', 'fsp_cmd').val('retrieveInvNoACT')).
                append($('<input />').attr('name', 'invc_no').val(deliveryNum));

            self.sumbmitSearchForm(form);

        },
        searchDongbu: function(deliveryNum) {
            alert('준비중입니다.');
        },
        searchKgb: function(deliveryNum) {
            var self = SearchShip;
            var form = $('<form>')[0];
            form.action = 'https://www.kgbls.co.kr/sub5/trace.asp';
            form.method = 'post';

            $(form).append($('<input />').attr('name', 'f_slipno').val(deliveryNum));

            self.sumbmitSearchForm(form);
        },
        searchPostoffice: function(deliveryNum) {
            var self = SearchShip;
            var form = $('<form>')[0];
            form.action = 'https://www.epost.go.kr/usr.trace.RetrieveDomRigiTraceList.comm';
            form.method = 'post';


            $(form).append($('<input />').attr('name', 'sid1').val(deliveryNum));

            self.sumbmitSearchForm(form);
        },
        searchYellow: function(deliveryNum) {
            var self = SearchShip;
            var form = $('<form>')[0];
            form.action = 'http://www.yellowcap.co.kr/custom/inquiry_result.asp';
            form.method = 'get';

            $(form).append($('<input />').attr('name', 'invoice_no').val(deliveryNum));

            self.sumbmitSearchForm(form);
        },

        searchLogen: function(deliveryNum)
        {
            var self = SearchShip;
            var form = $('<form>')[0];
            form.action = 'http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx';
            form.method = 'get';


            $(form).append($('<input />').attr('name', 'gubun').val('slipno')).
                append($('<input />').attr('name', 'slipno').val(deliveryNum));

            self.sumbmitSearchForm(form);
        },

        sumbmitSearchForm: function(form) {

            var w = window.open('', 'searchShip');
            form.target = 'searchShip';
            $(form).appendTo('body').submit();

        }

    }
})(window)

$(document).ready(function() {

    /** 상품 관련 이벤트 모음 **/
    SearchShip.event = (function(searchShip) {
        $('body').on('click', '.search-ship', function() {
            var agencieId = $(this).data('agencieId');
            var deliveryNum = $(this).data('deliveryNum');

            if (!agencieId || !deliveryNum) {
                alert('택배 배송정보가 정확하지 않습니다.');
                return false;
            }

            if (agencieId == 1) {
                searchShip.searchYellow(deliveryNum);
            } else if (agencieId == 2) {
                searchShip.searchDaehan(deliveryNum);
            }  else if (agencieId == 3) {
                searchShip.searchPostoffice(deliveryNum);
            } else if (agencieId == 4) {
                searchShip.searchKgb(deliveryNum);
            } else if (agencieId == 5) {
                searchShip.searchLogen(deliveryNum);
            } else if (agencieId == 6) {
                searchShip.searchDongbu(deliveryNum);
            }
        })
    })(SearchShip)
})

