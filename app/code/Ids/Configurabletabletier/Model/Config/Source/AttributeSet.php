<?php

namespace Ids\Configurabletabletier\Model\Config\Source;

class AttributeSet implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeSetOptions = $objectManager->get(\Magento\Catalog\Model\Product\AttributeSet\Options::class); /* @var $attributeSetOptions \Magento\Catalog\Model\Product\AttributeSet\Options*/

        return $attributeSetOptions->toOptionArray();
    }

}