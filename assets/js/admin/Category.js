/**
 * Created by inus-c on 2015-05-04.
 */


var Category = window.Category || Category;

Category = (function(w){
    return {
        /** 부위와 용도의 맵핑 객체 **/
        mapping : null,
        parts: null,
        uses: null,

        /**
         * 부위 등록 서브밋 기능
         */
        partSubmit: function() {
            var category = this;
            var options = {
                type:'POST',
                url:'/mall_new/admin/categories/forpart/',
                dataType : 'JSON',
                success: function(data, textStatus) {
                    if (data.rst == '200') {
                        var part_info = data.data;
                        category.insertToTableForPart(part_info.seq, part_info.title, part_info.unit);
                    }


                }
            };
            $("#partform").ajaxSubmit(options) ;
        },

        /**
         *
         * @param int id
         * @param string title
         * @param string unit
         */
        insertToTableForPart: function(id, title, unit) {
            var table = $('.part-table');

            var tr = document.createElement('tr');
            var idTd = document.createElement('td');
            var titleTd = document.createElement('td');
            var unitTd = document.createElement('td');

            table.prepend(
                $(tr).append($(idTd).html(id + '.')).
                    append($(titleTd).html(title)).
                    append($(unitTd).html(unit))
            );
        },


        useSubmit:function(){
            var category = this;
            var options = {
                type:'POST',
                url:'/mall_new/admin/categories/foruse/',
                dataType : 'JSON',
                success: function(data, textStatus) {
                    if (data.rst == '200') {
                        var part_info = data.data;
                        category.insertToTableForPart2(part_info.seq, part_info.title);
                    }


                }
            };
            $("#useform").ajaxSubmit(options) ;
        },


        insertToTableForPart2: function(id, title) {
            var table = $('.use-table');

            var tr = document.createElement('tr');
            var idTd = document.createElement('td');
            var titleTd = document.createElement('td');

            table.prepend(
                $(tr).append($(idTd).html(id + '.')).
                    append($(titleTd).html(title))
            );
        },

        selSubmit:function(){
            var category = this;
            var options = {
                type:'POST',
                url:'/mall_new/admin/categories/forsel/',
                dataType : 'JSON',
                success: function(data, textStatus) {
                    if (data.rst == '200') {
                        alert('등록 되었습니다.');
                        $('#part_item_box option:eq()').attr('selected', 'selected');
                        $('#use_item_box option:eq()').attr('selected', 'selected');
                    }else {
                        alert('이미 등록된 데이터 입니다.');
                    }
                }
            };
            $("#selform").ajaxSubmit(options) ;
        },

        /**
         * 부위와 용도의 연결 정보
         * @param string jsonString
         */
        applyMap: function(jsonString) {
            var category = this;
            category.mapping = JSON.parse(jsonString);
            //console.log(category.mapping);
        },

        /**
         * 부위별 정보
         * @param jsonString
         */
        applyParts: function(jsonString) {
            var category = this;
            category.parts = JSON.parse(jsonString);
        },

        /**
         * 용도별 정보
         * @param jsonString
         */
        applyUses: function(jsonString) {
            var category = this;
            category.uses = JSON.parse(jsonString);
            //console.log(category.mapping);
        },

        partInfo: function(currentSeq) {
            var category = this;
            //var currentSeq = $(this).data('partSeq');
            $('.use_box').removeClass('info');
            $('.part_box').removeClass('info');
            $.each(category.mapping, function() {
                var data = $(this)[0];
                if (data.part_category_seq == currentSeq) {
                    $('.use_box[data-use-seq=' + data.use_category_seq +']').addClass('info');
                }
            })
        },

        useInfo: function(currentSeq) {
            var category = this;
            $('.part_box').removeClass('info');
            $('.use_box').removeClass('info');
            $.each(category.mapping, function() {
                var data = $(this)[0];
                if (data.use_category_seq == currentSeq) {
                    $('.part_box[data-part-seq=' + data.part_category_seq +']').addClass('info');
                }
            })
        },

        /**
         *
         * @param string jsonString
         */
        applyMap: function(jsonString) {
            var category = this;
            category.mapping = JSON.parse(jsonString);
            //console.log(category.mapping);
        }
    }
})(window);

$(document).ready(function() {
    Category.event = (function(category, w) {

        /**
         *  부위 등록 클릭시 submit 이벤트
         */
        $('#frm-cate1').click(function(e) {
            category.partSubmit();
        });

        $('#frm-cate2').click(function(e) {
            category.useSubmit();
        });

        $('#frm-sel').click(function(e) {
            category.selSubmit();
        });

        $('.part_box').click(function(e) {
            category.partInfo($(this).data('partSeq'));
        });

        /**
         * 용도 카테고리 클릭시 이벤트
         */
        $('.use_box').click(function(e) {
            category.useInfo($(this).data('useSeq'));
        });

    })(Category, window);
});

