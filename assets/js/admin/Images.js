/**
 * Created by noggong on 15. 4. 21..
 */
var Images = window.Images || Images;

Images = (function(w) {

    return {
        saveLayer: null,

        openSaveLayer: function() {
            var images = this;

            var url = '/mall_new/admin/image/';
            if ($(this).data('companyId')) {
                url += $(this).data('companyId') + '/';

            }
            images.saveLayer = w.useSmartPop(url);
        },

        closeSaveLayer: function() {
            parent.Images.saveLayer.close();
        }

    }

})(window)

$(document).ready(function() {

    Images.event = (function(images, w) {
        $('.call-imagecreate').css('cursor', 'pointer');

        $('.call-imagecreate').on('click', function () {

            images.openSaveLayer();
        })

        $('.close_btn').on('click', function () {
            images.closeSaveLayer();
        })

        $('.image-company').change(function() {

            if(company_seq = $(this).val()) {
                location.href='/mall_new/admin/images/company/' + company_seq + '/';
            } else {
                location.href='/mall_new/admin/images/';
            }
        })
    })(Images, window)
})
