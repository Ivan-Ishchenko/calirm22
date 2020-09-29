define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'mage/template',
    'jquery/ui',
    'priceBox'
], function ($, utils, _, mageTemplate) {
    'use strict';

    $.widget('configurabletabletier.priceBox', $.mage.priceBox, {

        updatePrice: function updatePrice(newPrices) {
            var prices = this.cache.displayPrices,
                additionalPrice = {},
                pricesCode = [],
                priceValue, origin, finalPrice;

            this.cache.additionalPriceObject = this.cache.additionalPriceObject || {};

            if (newPrices) {
                $.extend(this.cache.additionalPriceObject, newPrices);
            }

            if (!_.isEmpty(additionalPrice)) {
                pricesCode = _.keys(additionalPrice);
            } else if (!_.isEmpty(prices)) {
                pricesCode = _.keys(prices);
            }

            _.each(this.cache.additionalPriceObject, function (additional) {
                if (additional && !_.isEmpty(additional)) {
                    pricesCode = _.keys(additional);
                }
                _.each(pricesCode, function (priceCode) {
                    priceValue = additional[priceCode] || {};
                    priceValue.amount = +priceValue.amount || 0;
                    priceValue.adjustments = priceValue.adjustments || {};

                    additionalPrice[priceCode] = additionalPrice[priceCode] || {
                            'amount': 0,
                            'adjustments': {}
                        };
                    additionalPrice[priceCode].amount = 0 + (additionalPrice[priceCode].amount || 0) +
                        priceValue.amount;
                    _.each(priceValue.adjustments, function (adValue, adCode) {
                        additionalPrice[priceCode].adjustments[adCode] = 0 +
                            (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                    });
                });
            });

            if (_.isEmpty(additionalPrice)) {
                this.cache.displayPrices = utils.deepClone(this.options.prices);

                /* IDS */
                idsConfigProductOptionAmount = 0;
                /* END IDS */

            } else {
                _.each(additionalPrice, function (option, priceCode) {
                    origin = this.options.prices[priceCode] || {};
                    finalPrice = prices[priceCode] || {};
                    option.amount = option.amount || 0;
                    origin.amount = origin.amount || 0;
                    origin.adjustments = origin.adjustments || {};
                    finalPrice.adjustments = finalPrice.adjustments || {};

                    finalPrice.amount = 0 + origin.amount + option.amount;

                    /* IDS */
                    idsConfigProductOptionAmount = option.amount;
                    /* END IDS */

                    _.each(option.adjustments, function (pa, paCode) {
                        finalPrice.adjustments[paCode] = 0 + (origin.adjustments[paCode] || 0) + pa;
                    });
                }, this);
            }

            /* IDS */
            clearTimeout(idsConfigProductCalcPriceTimer);
            idsConfigProductCalcPriceTimer = setTimeout(function () {
                if ($('#superattr').length > 0) {
                    $('#superattr').find('.txt').change();
                }

                var boxToCartInputTextQtyObj = $('.box-tocart .input-text.qty');
                if (typeof boxToCartInputTextQtyObj !== 'undefined' && boxToCartInputTextQtyObj.length > 0) {
                    boxToCartInputTextQtyObj.change();
                }

            }, 200);
            /* END IDS */

            this.element.trigger('reloadPrice');
        }


    });

    return $.configurabletabletier.priceBox;
});