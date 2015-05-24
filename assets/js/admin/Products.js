/**
 * Created by noggong on 15. 5. 5..
 */

var Products = window.Products || Products;

Products = (function (w) {

    return {

        /** 이미지 선택에서 이미지를 새로 끌어올 db 접속 텀 **/
        loadImageTerm: 200,

        /** 이미지 검색 키워드 **/
        keywordForImage: '',

        /** 이미지 검색 setINsertver **/
        intervalFunction: null,

        /** 구성품 오브젝트 **/
        category: {},

        optionPrototype: null,

        options: [],

        currentImageSearchBox: null,

        /**
         * 리스트 페이지에서 활성화 비활성화 스위치
         * @param {element} element
         * @param boolean state 스위치 상태
         */
        switch: function (element, state) {
            var isActive;
            var product_id = element.data('productId');

            if (state) {
                isActive = 'Y';
            } else {
                isActive = 'N';
            }

            $.ajax({
                url: '/mall_new/admin/product/' + product_id + '/active/',
                data: 'is_active=' + isActive,
                type: 'post',
                dataType: 'json'
            })
        },

        /**
         * 관리자가 이미지 검색
         * @param int companyId
         * @param string keyword
         */
        searchImage: function (companyId, keyword) {
            var products = this;

            /** 기존 검색어와 현재 검색어가 같으면 검색하지 않는다 **/
            if (keyword == products.keywordForImage) {
                return false;
            }

            products.keywordForImage = keyword;
            $.ajax({
                url: '/mall_new/admin/images/company/' + companyId + '/imagerepository/',
                data: {
                    keyword: keyword
                },
                type: 'GET',
                dataType: 'JSON',
                success: products.searchImageSuccess
            })
        },

        /**
         * 이미지 검색 성공시 이미지 목록 노출
         * @param data
         */
        searchImageSuccess: function (data) {
            var products = Products;
            var resultBox = products.currentImageSearchBox.find('.product-gallery');
            resultBox.html('');

            if (data.rst == 200) {
                $.each(data.data, function (i) {
                    var src = '/mall_new/image/' + data.data[i] + '/type/3';
                    resultBox.append(
                        '<div class="col-xs-4 col-md-2 col-lg-1" >' +
                        '    <a href="#none" class="thumbnail" data-image-seq="' + data.data[i] + '">' +
                        '    <img src="' + src + '" alt="...">' +
                        '    </a>' +
                        '</div>');


                })
            }
        },


        /**
         * @param int companyId
         * @param string keyword
         */
        startSearchTimer: function (companyId, keyword) {
            var products = this;

            products.intervalFunction = setTimeout(
                function () {
                   products.searchImage(companyId, keyword)
                }, products.loadImageTerm);
        },

        stopSearchTimer: function () {
            var products = this;

            clearTimeout(products.intervalFunction);
            products.intervalFunction = null;

        },

        /** 상품 타입 변경 **/
        changeProductType: function (type) {
            var products = this;
            if (type == 'B') {
                $('#category-add-btn').hide();
                $('[data-category-idx]').filter(function () {
                    if ($(this).data('categoryIdx') > 1) {
                        return true;
                    }
                }).hide();


            } else {
                $('#category-add-btn').show();
                $('[data-category-idx]').show();
            }

            categoryAction.setTotalWeight()
        },

        setOptionPrototype: function (options) {
            var products = this;
            products.optionPrototype = $('.option-section').find('li:first').clone();
            $('.option-section li').remove();

            if (options) {
                options = JSON.parse(options);
                $.each(options, function(i, data) {
                    products.addOption(data);
                })
            } else {
                products.addOption();
            }
        },

        addOption: function (data) {
            var products = this;

            var key = products.options.length;
            products.options[key] = products.optionPrototype.clone().attr('data-seq', key);
            if (key === 0) {
                products.options[key].find('.remove-option-btn').parent().remove();
                products.options[key].find('.glyphicon-arrow-down').parent().remove();
            }
            products.options[key].find('.option-money-box').moneyBox({
                inputName: 'price_apply[]'
            })

            if (data) {
                products.options[key].find('[name="option_grade[]"]').val(data.grade);
                products.options[key].find('.option-weight').val(data.weight);
                products.options[key].find('[name="option-count[]"]').val(data.current_count);
                products.options[key].find('[name="price_apply[]"]').val(data.price_diff);

            }
            $('.option-section ul').append(products.options[key]);
        },

        removeOption: function (listElement) {
            var products = this;
            listElement.remove();
            products.inputTotalWeight();
            products.inputTotalCount();
        },

        moveUpOption: function (listElement) {

            var target = listElement.prev();
            if (target.data('seq') === 0) {
                alert('기본 옵션 위로 올라갈 수 없습니다.');
            } else {
                listElement.insertBefore(target);
            }
        },

        moveDownOption: function (listElement) {
            var target = listElement.next();
            if (target.length < 1) {
                alert('옵션 중 가장 하단으로 내려 왔습니다.');
            } else {
                listElement.insertAfter(target);
            }

        },

        inputTotalWeight: function () {
            var total_weight = 0
            $.each($('.option-weight'), function() {
                total_weight += Number($(this).val());
            })
            //$('#total_weight').val(total_weight);
        },

        inputTotalCount: function () {
            var total_count = 0;
            $.each($('.option-count'), function() {
                total_count += Number($(this).val());
            })
            $('#count').val(total_count);
        },

        insertDcPrice: function() {
            if ($('#equlPrice').is(':checked')) {
                $('#price_discount').val($('#price').val());
            };
        },

        insertWeightPerH: function() {
            var price = Number($('#price_discount').val());
            var weight = Number($('#total_weight').val());

            var i =  weight / 100;
            var pricePerHundred = price / i;

            if (isNaN(pricePerHundred)) {
                pricePerHundred = 0;
            }
            $('#price_per_hundred').val(Math.ceil(pricePerHundred));

        },

        formSubmit: function(form) {
            var ret = {};
            var imageSeq = $(".image-choice").closest('a').data('imageSeq');
            if (!imageSeq) {
                ret = {
                    res : false,
                    msg : '메인 이미지를 선택하세요'
                }
            } else {
                form.append(
                    $('<input/>').
                        attr('type', 'hidden').
                        attr('name', 'image-repository-seq').
                        val(imageSeq)
                );

                ret = {
                    res : true
                }
            }
            oEditor.updateElement();

            return ret;
        },

        infoPopup: function() {
            var company_seq =  $("select[name=type]").val();

            alert(company_seq);

        }



    }
})(window)


$(document).ready(function () {

    Products.event = (function (products, w) {

        /** 이미지 검색 키를 누르면 기존 이벤트는 없어지고 2초후에 검색어로 검색 시작**/
        $('.image_select').keyup(function () {
            products.currentImageSearchBox = $(this).closest('.image-search-section');
            products.stopSearchTimer();
            products.startSearchTimer($(this).data('companyId'), $(this).val());

        });

        /** 상품 타입 변경 이벤트 */
        $('.product-type').change(function () {
            var type = $(this).val();
            products.changeProductType(type);
        })


        $('#category-add-btn').click(function () {
            categoryAction.addCategory();
        })

        $('.option-section').on('click', '.add-option-btn', function () {
            products.addOption();
        })

        $('.option-section').on('click', '.remove-option-btn', function () {
            products.removeOption($(this).closest('li'));
        })

        $('.option-section').on('click', '.glyphicon-arrow-up', function () {
            products.moveUpOption($(this).closest('li'));
        })

        $('.option-section').on('click', '.glyphicon-arrow-down', function () {
            products.moveDownOption($(this).closest('li'));
        })

        $('.option-section').on('blur', '.option-weight', function () {
            products.inputTotalWeight();
        })

        $('.option-section').on('blur', '.option-count', function () {
            products.inputTotalCount();
        })

        $('#price').blur(function() {
            products.insertDcPrice();
        })

        $('#equlPrice').click(function() {
            products.insertDcPrice();
        })

        $('#price_discount, .option-weight').change(function() {
            products.insertWeightPerH();

        })

        $('.description-image').on('click', '.thumbnail', function() {
            var imagtag = '<p style="margin:0; padding: 0"><img src="/mall_new/image/' + $(this).data('imageSeq') + '/type/7/" /></p>';
            var newElement = CKEDITOR.dom.element.createFromHtml( imagtag, oEditor.document );
            oEditor.insertElement(newElement);

        })

        $('.main-image').on('click', '.thumbnail', function() {
            $('.thumbnail').find('img').removeClass('image-choice');
            $('.thumbnail').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('img').addClass('image-choice');
        })

        $('.btn-info').click(function() {
            products.infoPopup();
        });


    })(Products, window)
});

/**
 * 여기부터는 구성상품에 대한 처리
 */
CategoryAction = (function () {


    function CategoryAction(category) {

        this.category = category;
        this.categorySize = 1;
        this.item = {};

        var categoryAction = this;

        /**
         *
         * @param idx
         */
        this.addCategory = function (options) {
            categoryAction.item[this.categorySize] = new this.ItemDetail(this.categorySize, options);
            categoryAction.categorySize++;
        };

        /**
         * 상품 총가격 자동 입력
         */
        this.setTotalWeight = function () {
            var totalWeight = 0;
            $.each(categoryAction.item, function () {
                if (this.weightInput.is(":visible")) {
                    totalWeight += Number(this.weightInput.val());
                }

            });
            $('input[name=total_weight]').val(totalWeight);

        }
        /**
         *
         * @param idx
         */
        this.ItemDetail = (function () {
            this.useSection = null;
            this.userSelect = null;
            this.partSection = null;
            this.partSelect = null;
            this.weightSection = null;
            this.weightInput = null;
            this.removeButtonsSection = null;
            this.removeButtons = null;
            this.currentIdx = null;

            function ItemDetail(idx, options) {
                var itemDetail = this;
                itemDetail.currentIdx = idx;

                /**
                 * 용도 선택시 부위 옵션 표
                 * @param useId
                 */
                itemDetail.changeUse = function (useId) {
                    var itemDetail = this;
                    itemDetail.partSelect.html('');
                    itemDetail.partSelect.append('<option value="">부위선택</option>');



                    $.each(category.mapping, function () {
                        //console.log(useId);
                        //console.log(this.use_category_seq);

                        if (this.use_category_seq == useId) {

                            var part = category.parts[this.part_category_seq];
                            itemDetail.partSelect.append('<option value="' + part.seq + '">' + part.title + '</option>');
                        }
                    })
                }

                itemDetail.createSectionHtml(idx, options);
                itemDetail.useSelect.change(function () {
                    itemDetail.changeUse($(this).val());
                })

                itemDetail.removeBtn.click(function () {
                    itemDetail.removeCategory();
                })

                itemDetail.weightInput.blur(function () {
                    categoryAction.setTotalWeight();
                })

            }

            /**
             *
             * @param idx
             */
            ItemDetail.prototype.createSectionHtml = function (idx, options) {
                var itemDetail = this;
                categoryAction.item[idx] = itemDetail;

                /** 용도 선택 부분 셋팅 **/
                itemDetail.useSection = $('<div></div>').addClass('col-sm-4').addClass('col-xs-10').addClass('form-group').attr('data-category-idx', idx);
                itemDetail.useSelect = $('<select></select>').addClass('form-control').attr('required', true);


                /** 부위 선택 부분 셋팅 **/
                itemDetail.partSection = itemDetail.useSection.clone();
                itemDetail.partSelect = itemDetail.useSelect.clone();
                itemDetail.useSection.append(itemDetail.useSelect.attr('name', 'use_category_seq[]'));
                itemDetail.partSection.append(itemDetail.partSelect.addClass('part-select').attr('name', 'part_category_seq[]'));

                /** 용도 option append **/
                itemDetail.useSelect.append('<option value="">용도선택</option>');
                $.each(Category.uses, function () {
                    itemDetail.useSelect.append('<option value=' + this.seq + '>' + this.title + '</option>')
                })

                /** 부위 선택 옵션 **/
                itemDetail.partSelect.append('<option value="">부위선택</option>');

                /** 삭제 버튼 **/
                itemDetail.removeBtnSection = $('<div></div>').addClass('col-sm-2').addClass('col-xs-2').addClass('form-group').attr('data-category-idx', idx);
                itemDetail.removeBtn = $('<button>').addClass('btn').addClass('btn-warning').html('삭제');

                /** 용량 **/
                itemDetail.weightSection = itemDetail.removeBtnSection.clone();
                itemDetail.weightInput = $('<input/>').addClass('form-control').attr('type', 'text').attr('name', 'weight[]').attr('data-category-idx', idx).attr('placeholder', '용량 (g)').attr('required', true);

                itemDetail.weightSection.append(itemDetail.weightInput);
                itemDetail.removeBtnSection.append(itemDetail.removeBtn);


                if (options) {

                    itemDetail.useSelect.val(options.use_category_seq);
                    itemDetail.changeUse(options.use_category_seq);
                    itemDetail.partSelect.val(options.part_category_seq);
                    itemDetail.weightInput.val(options.weight);
                }

                $('.category-section').append(itemDetail.useSection).append(itemDetail.partSection).append(itemDetail.weightSection);
                if (idx > 1) {
                    $('.category-section').append(itemDetail.removeBtnSection);
                }
            }

            /**
             *
             */
            ItemDetail.prototype.removeCategory = function () {
                var itemDetail = this;

                $('[data-category-idx=' + itemDetail.currentIdx + ']').remove();
                delete categoryAction.item[itemDetail.currentIdx];
            }


            return ItemDetail;
        })()


    }

    return CategoryAction;

})();

var categoryAction = new CategoryAction(Category);


/** 옵션 가격 선택 영역 기능 **/
$.fn.moneyBox = function (options) {

    var moneyBoxElement = $(this);

    function MoneyBox(element, options) {
        this.buttonWrap = null;
        this.price = 0;
        this.currentSign = 'plus';

        var buttonPrototype = $('<button></button>').attr('type', 'button').addClass('btn').addClass('btn-xs').css('margin-right', '5px');
        this.buttonItem = {
            'plus': buttonPrototype.clone().addClass('sign').addClass('btn-danger').val('plus').html('+'),
            'minus': buttonPrototype.clone().addClass('sign').val('minus').html('-'),
            'one': buttonPrototype.clone().addClass('money').val('100').html('100'),
            'five': buttonPrototype.clone().addClass('money').val('500').html('500'),
            'ten': buttonPrototype.clone().addClass('money').val('1000').html('1,000'),
            'fifty': buttonPrototype.clone().addClass('money').val('5000').html('5,000'),
            'hundred': buttonPrototype.clone().addClass('money').val('10000').html('10,000'),
            'fivehundred': buttonPrototype.clone().addClass('money').val('50000').html('50,000')
        };

        if (options.inputName) {
            this.inputName = options.inputName;
        }
        this.buttonWrap = $('<p></p>');
        this.element = element;
        this.inputElement = $('<input />').addClass('form-control').attr('placeholder', '가격차이').attr('required', true).attr('readonly', true).attr('name', this.inputName);


        this.initStructure();


    };


    MoneyBox.prototype.initStructure = function () {
        var moneyBox = this;

        $.each(moneyBox.buttonItem, function (i, val) {
            moneyBox.buttonWrap.append(val);
        })


        moneyBox.element.append(moneyBox.buttonWrap).append(moneyBox.inputElement);
    }

    MoneyBox.prototype.inputPrice = function () {
        var moneyBox = this;
        var price = moneyBox.price;

        moneyBox.inputElement.val(numberWithCommas(price));
    }


    MoneyBox.prototype.eventCollection = function () {
        var moneyBox = this;

        moneyBox.buttonWrap.find('.sign').click(function () {
            moneyBox.buttonWrap.find('.sign').removeClass('btn-danger');
            $(this).addClass('btn-danger');
            moneyBox.currentSign = $(this).val();
            moneyBox.inputPrice();
        })

        moneyBox.buttonWrap.find('.money').click(function () {
            var money = Number($(this).val());

            if (moneyBox.currentSign == 'plus') {
                moneyBox.price += money;
            } else {
                moneyBox.price -= money;
            }

            moneyBox.inputPrice();
        })

        moneyBox.buttonWrap.find('.money').mousedown(function () {
            $(this).addClass('btn-danger');

        })

        moneyBox.buttonWrap.find('.money').mouseup(function () {
            $(this).removeClass('btn-danger');

        })
    }


    var moneyBox = new MoneyBox($(this), options);
    moneyBox.eventCollection();


    return moneyBox;
    //<p>

    //</p>
    //<input type="text" name="price_apply" class="form-control" placeholder="가격차이" readonly/>

}


