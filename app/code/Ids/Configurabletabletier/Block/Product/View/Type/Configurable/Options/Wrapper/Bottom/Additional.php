<?php

namespace Ids\Configurabletabletier\Block\Product\View\Type\Configurable\Options\Wrapper\Bottom;

use Magento\Catalog\Model\Product;

class Additional extends \Magento\Framework\View\Element\Template {

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
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }

}