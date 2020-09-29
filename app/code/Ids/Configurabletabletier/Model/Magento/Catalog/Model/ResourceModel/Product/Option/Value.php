<?php

namespace Ids\Configurabletabletier\Model\Magento\Catalog\Model\ResourceModel\Product\Option;

class Value extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value {

    /**
     * Save option value price data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _saveValuePrices(\Magento\Framework\Model\AbstractModel $object) {
        $priceTable = $this->getTable('catalog_product_option_type_price');

        $price = (double)sprintf('%F', $object->getPrice());
        $priceType = $object->getPriceType();

        /* IDS Commented
        <--- if ($object->getPrice() && $priceType) { --->

        Bug explanation:

        When saving product with options, Magento saves prices of these options in the table ‘catalog_product_options_type_price’.
        Code for this operation can be found in \Magento\Catalog\Model\ResourceModel\Product\Option\Value method _saveValuePrices.
        On the line 98, it check condition “$object->getPrice() $priceType“ and saves price only if it is true.
        Because for a free options price is zero, it causes that prices for free options are not saved in the table.

        When reindexing product prices, prices of custom options are applied in the
        Magento\Catalog\Model\ResourceModel\Product\Indexer\Price\DefaultPrice method _applyCustomOption.
        At the line 457 it join the table ‘catalog_product_options_type_price’ in order to get minimal values of prices.
        Because originally this table does not contain zero prices, they are not applied to the minimal price calculation and,
        thus, not-minimal value of price is used instead.

        END IDS Commented */

        if ($object && $priceType) { /* IDS Added */
            //save for store_id = 0
            $select = $this->getConnection()->select()->from(
                $priceTable,
                'option_type_id'
            )->where(
                'option_type_id = ?',
                (int)$object->getId()
            )->where(
                'store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
            $optionTypeId = $this->getConnection()->fetchOne($select);

            if ($optionTypeId) {
                if ($object->getStoreId() == '0') {
                    $bind = ['price' => $price, 'price_type' => $priceType];
                    $where = [
                        'option_type_id = ?' => $optionTypeId,
                        'store_id = ?' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ];

                    $this->getConnection()->update($priceTable, $bind, $where);
                }
            } else {
                $bind = [
                    'option_type_id' => (int)$object->getId(),
                    'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    'price' => $price,
                    'price_type' => $priceType,
                ];
                $this->getConnection()->insert($priceTable, $bind);
            }
        }

        $scope = (int)$this->_config->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE
            && $priceType
            && $object->getPrice()
            && $object->getStoreId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID
        ) {
            $baseCurrency = $this->_config->getValue(
                \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                'default'
            );

            $storeIds = $this->_storeManager->getStore($object->getStoreId())->getWebsite()->getStoreIds();
            if (is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    if ($priceType == 'fixed') {
                        $storeCurrency = $this->_storeManager->getStore($storeId)->getBaseCurrencyCode();
                        /** @var $currencyModel \Magento\Directory\Model\Currency */
                        $currencyModel = $this->_currencyFactory->create();
                        $currencyModel->load($baseCurrency);
                        $rate = $currencyModel->getRate($storeCurrency);
                        if (!$rate) {
                            $rate = 1;
                        }
                        $newPrice = $price * $rate;
                    } else {
                        $newPrice = $price;
                    }

                    $select = $this->getConnection()->select()->from(
                        $priceTable,
                        'option_type_id'
                    )->where(
                        'option_type_id = ?',
                        (int)$object->getId()
                    )->where(
                        'store_id = ?',
                        (int)$storeId
                    );
                    $optionTypeId = $this->getConnection()->fetchOne($select);

                    if ($optionTypeId) {
                        $bind = ['price' => $newPrice, 'price_type' => $priceType];
                        $where = ['option_type_id = ?' => (int)$optionTypeId, 'store_id = ?' => (int)$storeId];

                        $this->getConnection()->update($priceTable, $bind, $where);
                    } else {
                        $bind = [
                            'option_type_id' => (int)$object->getId(),
                            'store_id' => (int)$storeId,
                            'price' => $newPrice,
                            'price_type' => $priceType,
                        ];

                        $this->getConnection()->insert($priceTable, $bind);
                    }
                }
            }
        } else {
            if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE
                && !$object->getPrice()
                && !$priceType
            ) {
                $storeIds = $this->_storeManager->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                foreach ($storeIds as $storeId) {
                    $where = [
                        'option_type_id = ?' => (int)$object->getId(),
                        'store_id = ?' => $storeId,
                    ];
                    $this->getConnection()->delete($priceTable, $where);
                }
            }
        }
    }

}