<?php

namespace Ids\Configurabletabletier\Block\Product\View\Type\Configurable;

use Magento\Catalog\Model\Product;

class Table extends \Magento\Framework\View\Element\Template {

    /**
     * @var Product
     */
    protected $_product;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product object
     *
     * @return Product
     * @throws \LogicExceptions
     */
    public function getProduct() {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }

    /**
     * Get custom attribute label
     *
     * @param $_product \Magento\Catalog\Model\Product
     * @param $attributeCode string
     * @return string
     */
    public function getCustomAttributeLabel($_product, $attributeCode) {
        $customAttributeLabel = '';

        if (strlen($attributeCode) > 0) {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $customAttributesLabelsByCategory = array(
                'gown_size_us' => array(
                    8 => array(
                        'category_id' => 8, /* 8 - Category Id of "Kindergarten / Preschool" */
                        'attribute_label' => 'Height'
                    )
                )
            );

            $productCategoryIds = $_product->getCategoryIds();

            foreach ($productCategoryIds as $categoryId) {
                /**
                 * @var $category \Magento\Catalog\Model\Category
                 */
                $category = $_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);

                $categoryParentIds = $category->getParentIds();

                if (is_array($categoryParentIds) && count($categoryParentIds) > 0) {
                    foreach ($categoryParentIds as $categoryParentId) {
                        if ($categoryParentId > 2 && isset($customAttributesLabelsByCategory[$attributeCode][$categoryParentId]['attribute_label'])) {
                            $customAttributeLabel = $customAttributesLabelsByCategory[$attributeCode][$categoryParentId]['attribute_label'];
                            break;
                        }
                    }
                }
            }
        }

        return $customAttributeLabel;
    }

}