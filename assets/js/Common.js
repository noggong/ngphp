/**
 * Created by noggong on 15. 4. 24..
 */


function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}

/**
 * 스마트팝 실행
 * @param string url
 * @param int width
 * @param int height
 * @param bool bodyClose
 */
function useSmartPop(url, html, width, height, bodyClose) {

    width = (width) ? width : 400;

    bodyClose = (bodyClose === false) ? bodyClose : true;

    /** bodyClose 무조건 false로 해놓음 */
    var options = {
        bodyClose: false,
        background: '#000',
        border: 0,
        padding: 0,
        width: width,
        closeImg: false
    };

   if (height) {
       options['height'] = height;
   }

    if (url) {
        options['url'] = url;
    }

    if (html) {
        options['html'] = html;
        html.show();
    }
    return smartpop = $.smartPop.open(options);
}


$(document).ready(function() {

    $('.smart-pop-layer').hide();
    $('body').on('click', '.smartpop-go-close', function () {
        smartpop.close();
    })

    $( ".datepicker" ).datepicker();


});

/** S: 폼검사 object **/

function Validation() {};

Validation.prototype.trimNvl = function(str) {

    var tmp = str.replace(/(^[\s\xA0]+|[\s\xA0]+$)/g, '');

    if ( tmp == "" ) {
        return true;
    }else{
        return false;
    }
};
var Validation = new Validation();

/** E: 폼검사 object **/

/** text 숫자변환 **/

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/** text 숫자변환 **/

Date.prototype.getFullTime = function() {
    var month = (this.getMonth() + 1);
    if (month < 10) {
        month = '0' + month;
    }
    return this.getFullYear() + '-' + month + '-' + this.getDate()
}

/**
 * 날짜 검색 기능
 * 1주일, 15일, 1개월, 3개월 */

$.fn.searchDate = function() {
    $(this).find('label').on('click', function() {

        var option = $(this).find('input').val();
        var date = new Date();



        if (option == 1) {
            new_date = date.setDate(date.getDate() - 7);
        } else if (option == 2) {
            new_date = date.setDate(date.getDate() - 15);
        } else if (option == 3) {
            new_date = date.setMonth(date.getMonth() - 1);

        } else if (option == 4) {
            new_date = date.setMonth(date.getMonth() - 3);

        }
        var start_date = new Date(new_date);
        var end_date = new Date();
        $('#start_dt').val(start_date.getFullTime());
        $('#end_dt').val(end_date.getFullTime());



    });
}