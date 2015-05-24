<?php

return array (

    array (
        'title' => '전체 관리자 카테고리',
        'item'  => array (
            array (
                'title' => '회원관리',
                'keyword' => 'members',
                'link'  => '',
                'item' => array (
                    array (
                        'link'=> '/admin/member/member_join.php',
                        'title'=> '가입명단'
                    ),
                    array (
                        'link'=> '/admin/member/member_quit.php',
                        'title'=> '탈퇴명단'
                    )
                )
            ),
            array (
                'title' => '게시판관리',
                'keyword' => 'boards',
                'link'  => '',
                'item'  => array (
                    array (
                        'link' => '/admin/board/notice.php',
                        'title' => '공지사항'
                    ),
                    array (
                        'link' => '/admin/board/qna.php',
                        'title' => 'Q&amp;A'
                    ),
                    array (
                        'link' => '/admin/board/100.php',
                        'title' => '한우백문백답'
                    ),
                    array (
                        'link' => '/admin/event/event.php',
                        'title' => '이벤트'
                    ),
                )
            ),
            array (
                'title' => '한우서포터즈',
                'keyword' => 'supporters',
                'link'  => '',
                'item'  => array (
                    array (
                        'link' => '/admin/experience/notice_supporters.php',
                        'title' => '한우 서포터즈'
                    ),
                )
            ),
            array (
                'title' => '커뮤니티관리',
                'keyword' => 'community',
                'link'  => '',
                'item'  => array (
                    array (
                        'link' => '/admin/community/cooking.php',
                        'title' => '한우 쿠킹클래스'
                    ),
                    array (
                        'link' => '/admin/community/chef_class.php',
                        'title' => '쉐프의 요리교실'
                    ),
                    array (
                        'link' => '/admin/community/coupon.php',
                        'title' => '한우 맛집탐방'
                    ),
                    array (
                        'link' => '/admin/community/store.php',
                        'title' => '우리한우 판매선정점'
                    ),
                    array (
                        'link' => '/admin/community/magazine.php',
                        'title' => '한우 매거진'
                    ),
                    array (
                        'link' => '/admin/community/event.php',
                        'title' => '한우 행사 안내'
                    ),
                )
            ),
            array (
                'title' => '구할인몰 관리자',
                'keyword' => 'oldmall',
                'link'  => '',
                'item'  => array (
                    array (
                        'link' => '/admin/mall/products.php',
                        'title' => '상품관리'
                    ),
                    array (
                        'link' => '/admin/mall/vendors.php',
                        'title' => '업체관리'
                    ),
                    array (
                        'link' => '/admin/mall/sales.php',
                        'title' => '판매관리'
                    ),
                )
            ),
            array (
                'title' => '팝업관리',
                'keyword' => 'popups',
                'link'  => '',
                'item'  => array (
                    array (
                        'link' => '/admin/popup/popup_create.php',
                        'title' => '팝업생성'
                    ),
                    array (
                        'link' => '/admin/popup/popup_list.php',
                        'title' => '팝업리스트'
                    ),
                )
            ),
        ),
    ),

    array(
        'title' => '쇼핑몰 카테고리',
        'item' => array (
            array (
                'title' => '상점 관리',
                'keyword' => 'companies',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/companies',
                        'title' => '상점 관리'
                    ),
                    /**
                     * todo: 상점 고정이미지 등록 기능은 추후에 개발
                     */
                    /**
                    array (
                        'link' => '/mall_new/admin/fiximage',
                        'title' => '상품 고정이미지 관리'
                    ),
                     */
                )
            ),
            array (
                'title' => '카테고리 관리',
                'keyword' => 'categories',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/categories',
                        'title' => '카테고리 관리'
                    ),
                )
            ),
            array (
                'title' => '상품 관리',
                'keyword' => 'products',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/products',
                        'title' => '상품 관리'
                    ),
                )
            ),
            array (
                'title' => '주문 관리',
                'keyword' => 'purchases',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/purchases',
                        'title' => '주문 관리'
                    ),
                    array (
                        'link' => '/mall_new/admin/purchases/cancels',
                        'title' => '주문 반품/취소'
                    ),
                    array (
                        'link' => '/mall_new/admin/purchases/receipt/request',
                        'title' => '현금영수증 신청'
                    ),
                    array (
                        'link' => '/mall_new/admin/purchases/deliveryview',
                        'title' => '배송예정 상품'
                    ),

                    array (
                        'link' => '/mall_new/admin/purchases/sales',
                        'title' => '매출 통계'
                    ),
                )
            ),
            array (
                'title' => '이미지 관리',
                'keyword' => 'images',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/images',
                        'title' => '이미지 관리'
                    ),
                )
            ),
            array (
                'title' => '상품 Q&A',
                'keyword' => 'customer',
                'link' => '',
                'item' => array(
                    array (
                        'link' => '/mall_new/admin/customer/contents/',
                        'title' => '상품 Q&amp;A'
                    ),
                )
            ),
        )
    ),

);