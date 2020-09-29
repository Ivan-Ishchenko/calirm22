<?php

namespace Ids\Configurabletabletier\Block\Rewrite\Product\Renderer;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable {

    /**
     * Get renderer template
     *
     * @return string
     */
    protected function getRendererTemplate() {
        $this->getProduct();
        if($this->product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
            /* @var $helper \Ids\Configurabletabletier\Helper\Data */
            $attributeSets = $helper->geAttributeSetsFromConfig();
            $productAttributeSetId = $this->product->getAttributeSetId();

            if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
                //TODO return $this->isProductHasSwatchAttribute() ? self::SWATCH_RENDERER_TEMPLATE : 'Ids_Configurabletabletier::product/view/type/options/configurable.phtml';
                return 'Ids_Configurabletabletier::product/view/type/options/configurable.phtml';
            }
        }

        return parent::getRendererTemplate();
    }

}