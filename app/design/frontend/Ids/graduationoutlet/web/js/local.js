define([
        "jquery",
        "jquery/ui",
        "Magento_Swatches/js/swatch-renderer",
        "domReady!"
    ],
    function ($j) {
        "use strict";

        /* Add on "mouseover mouseout" listener to toggle hover class to menu item */
        $j('body').delegate('.menu .menu-dropdown-icon', 'mouseover mouseout', function (e) {
            /*var allAnchorElements = $j('.menu .menu-dropdown-icon').find('a');
            if (typeof allAnchorElements !== 'undefined' && allAnchorElements.length > 0) {
                allAnchorElements.removeClass('hover');
            }*/

            var anchorEl = $j(this).find('a:first');
            if (typeof anchorEl !== 'undefined' && anchorEl.length > 0) {
                anchorEl.toggleClass('hover');
            }

            if ($j(window).width() > 943) {
                //adjustMenuUlItemPosition($j(this).find('ul:first'));
                adjustMenuUlItemPosition($j(this));
            }
        });
        /* END Add on "mouseover mouseout" listener to toggle hover class to menu item */


        /* Add active class to menu items */
        var url = window.location.href;
        var url_array = url.split("?");
        var activeurl = url_array[0];
        $j('.menu a[href="' + activeurl + '"]').parents('li').addClass('active');
        /* END Add active class to menu items */


        /* Adjust menu ul item position */
        function adjustMenuUlItemPosition(item, clearRight) {
            var mainContainerClass = '.menu';
            /* .outerWidth() takes into account border and padding. */
            var widthMenuMainContainer = $j(mainContainerClass).outerWidth();
            /*var widthItem = $j(item).outerWidth();
            var widthRightSwimLinksPosition = $j(item).closest('li').offset();*/
            var itemPosition = $j(item).offset();
            var itemPositionLeft = parseInt(itemPosition.left);
            var itemUl = $j(item).find('ul:first');
            var itemUlWidth = itemUl.outerWidth();

            if (((itemPositionLeft - (($j(window).width() - widthMenuMainContainer) / 2)) + itemUlWidth) > widthMenuMainContainer) {
                itemUl.css({
                    right: 0
                });
            }
        }

        /* END Adjust menu ul item position */


        /* Auto resizing the search SELECT element according to selected OPTION's width */
        $j('body').delegate('.search-in-selector .search-in-cat', 'change', function (e) {
            var searchInCatTmpSelectEl = $j(".search-in-cat-tmp-select-width");
            if (typeof searchInCatTmpSelectEl !== 'undefined' && searchInCatTmpSelectEl.length > 0) {
                searchInCatTmpSelectEl.find(".tmp-select-option").html($j('.search-in-selector .search-in-cat option:selected').text());

                var self = $j(this);
                var searchInCatTmpSelectWidth = searchInCatTmpSelectEl.width();

                if (searchInCatTmpSelectWidth < 0) {
                    var myVar = setInterval(function () {
                        searchInCatTmpSelectWidth = searchInCatTmpSelectEl.width();
                        if (searchInCatTmpSelectWidth > 0) {
                            clearInterval(myVar);
                            self.change();
                        }
                    }, 1000);
                }


                if (searchInCatTmpSelectWidth > 0) {
                    self.width(searchInCatTmpSelectWidth);

                    var inputSearchEl = $j(".block-search input#search");
                    if (typeof inputSearchEl !== 'undefined' && inputSearchEl.length > 0) {
                        inputSearchEl.css({
                            paddingLeft: searchInCatTmpSelectEl.outerWidth() + 5
                        });
                    }
                }
            }
        });


        var searchInCatEl = $j(".search-in-selector .search-in-cat");
        if (typeof searchInCatEl !== 'undefined' && searchInCatEl.length > 0) {
            if ($j.trim(searchInCatEl.val()) == "") {
                searchInCatEl.find("option:first").attr('selected', true);
            }
            searchInCatEl.change();
        }
        /* END Auto resizing the search SELECT element according to selected OPTION's width */


        /* Scroll to add new product review block on product view page */
        $j('body').delegate('.product.media .reviews-actions a', 'click', function (event) {
            event.preventDefault();
            var acnchor = $j(this).attr('href').replace(/^.*?(#|$)/, '');
            $j(".product.data.items [data-role='content']").each(function (index) {
                if ($j(this).attr('id') == 'reviews') {
                    $j('.product.data.items').tabs('activate', index);
                    $j('html, body').animate({
                        scrollTop: $j('#' + acnchor).offset().top - 50
                    }, 300);
                }
            });
        });
        /* END Scroll to add new product review block on product view page */


        /* Move options title inside options from above */
        var productOptionsWrapperEl = $j('.product-options-wrapper');
        if (typeof productOptionsWrapperEl !== 'undefined' && productOptionsWrapperEl.length > 0) {
            productOptionsWrapperEl.find(".field").each(function () {
                var selectEl = $j(this).find('.control select');

                if (selectEl.attr('id')) {
                    var selectElId = selectEl.attr('id');
                    var selectLabelEl = $j(this).find('label.label[for="' + selectElId + '"]');

                    if (typeof selectLabelEl !== 'undefined' && selectLabelEl.length > 0) {
                        selectLabelEl.hide();
                        var labelText = $j.trim(selectLabelEl.text());
                        if (labelText.length > 0) {
                            selectEl.find("option[value='']").text(labelText);
                        }
                    }
                }
            });
        }
        /* END Move options title inside options from above */


        /* Fix header on scroll */
        var pageHeaderSelector = '.page-header';
        var navSectionsSelector = '.nav-sections';
        var headerFixedClassname = 'page-header-fixed';

        $j(window).scroll(function () {
            var header = $j(pageHeaderSelector),
                navSections = $j(navSectionsSelector);

            if (header && header.length > 0 && navSections && navSections.length > 0) {
                var headerHeight = header.outerHeight() + navSections.outerHeight() - (navSections.outerHeight() / 2),
                    scroll = $j(window).scrollTop();
                if (scroll > 0 && scroll >= headerHeight) {
                    navSections.stop(true, true).addClass(headerFixedClassname);
                } else {
                    navSections.stop(true, true).removeClass(headerFixedClassname);
                }
            }
        });
        /* END Fix header on scroll */


        /* Set same height for blocks */
        function setSameHeightForBlocks() {
            var selectorsToSetSameHeight = [
                ".listing_prod_cont3 .list_main_cate .cate_img_box",
                /*".listing_prod_cont3 .list_main_cate .cate_bt_title",*/
                ".listing_prod_cont3 .list_main_cate h3",
                ".page-products .products-grid .product-image-container",
                ".page-products .products-grid .product-item .product-item-name",
                ".page-products .products-grid .product-item"
            ];

            $j.each(selectorsToSetSameHeight, function (i, val) {
                var elements = $j(val);
                if (typeof elements !== 'undefined' && elements.length > 0) {

                    if ($j(window).width() >= 463) {
                        var maxHeight = 0;
                        elements.each(function () {
                            maxHeight = Math.max(maxHeight, $j(this).outerHeight());
                        });
                        elements.css({height: maxHeight + 'px'});
                    } else {
                        elements.css({height: ''});
                    }
                }
            });
        }

        setSameHeightForBlocks();

        $j(window).resize(function () {
            setSameHeightForBlocks();
        });

        var i = setInterval(setSameHeightForBlocks, 1000);
        setTimeout(function () {
            clearInterval(i);
        }, 100000);
        /* END Set same height for blocks */


        /* Add Tooltip to color attribute as swatches */
        $j('.product-item-info .swatch-option.color')
            .SwatchRendererTooltip();
        /* END Add Tooltip to color attribute as swatches */

        /* Change image on swatch color click on product list */
        $j('body').delegate('.product-item-info .swatch-attribute.color_swatch .swatch-option', 'click mouseover', function (event) {
            var self = $j(this);
            if (self.attr('option-image')) {
                var swatchImageUrl = $j.trim($j(this).attr('option-image'));
                var productImagePhotoEl = self.parents('.product-item-info').find('.product-image-photo');
                if (swatchImageUrl.length > 0 && typeof productImagePhotoEl !== 'undefined' && productImagePhotoEl.length > 0) {
                    /*enableProductMediaLoader(self);*/

                    productImagePhotoEl.attr('src', swatchImageUrl);

                    /*setTimeout(function () {
                        disableProductMediaLoader(self);
                    }, 500);*/
                }
            }
        });
        /* END Change image on swatch color click on product list */


        var loaderClassName = 'swatch-option-loading';

        /**
         * Enable loader
         *
         * @param $this
         */
        function enableProductMediaLoader($this) {
            var productImagePhotoEl = $this.parents('.product-item-info').find('.product-image-photo');
            if (typeof productImagePhotoEl !== 'undefined' && productImagePhotoEl.length > 0) {
                productImagePhotoEl.addClass(loaderClassName);
            }
        }

        /**
         * Disable loader
         *
         * @param $this
         */
        function disableProductMediaLoader($this) {
            var productImagePhotoEl = $this.parents('.product-item-info').find('.product-image-photo');
            if (typeof productImagePhotoEl !== 'undefined' && productImagePhotoEl.length > 0) {
                productImagePhotoEl.removeClass(loaderClassName);
            }
        }


        /* Set image as bg in cms blocks */
        function setBgToCmsBlockFromImg() {
            var cmsBlocksClassToImageBg = ["static-block-12", "static-block-13"];
            $j.each(cmsBlocksClassToImageBg, function (i, val) {
                $j('.' + val).find("ul li").each(function (n) {
                    var firstImageEl = $j(this).find('img:first');
                    if (typeof firstImageEl !== 'undefined' && firstImageEl.length > 0) {
                        var getImageSrc = firstImageEl.attr('src');

                        if ($j.trim(getImageSrc).length > 0) {
                            $j(this).css({
                                'background-image': 'url("' + getImageSrc + '")',
                                'background-repeat': 'no-repeat',
                                'background-size': 'cover'
                            });

                            firstImageEl.css({
                                'display': 'none'
                            });
                        }
                    }
                });
            });
        }

        setBgToCmsBlockFromImg();
        /* END Set image as bg in cms blocks */


        return;
    });

