<?php

namespace Ids\Configurabletabletier\Plugin\Block\Product;

class ListProduct {

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        return $result . $this->getDetailsAttributesHtml($product);
    }


    /**
     * @param $product \Magento\Catalog\Model\Product
     * @return string
     */
    public function getDetailsAttributesHtml($product) {
        $html = '';
        $attributeColorCode = 'color';
        $productColorsIdsString = $product->getData($attributeColorCode);
        $productColorsLabelsString = $product->getResource()->getAttribute($attributeColorCode)->getFrontend()->getValue($product);

        if (strlen($productColorsIdsString) > 0) {
            $productColorsIds = array_map('trim', explode(',', $productColorsIdsString));
            $productColorsLabels = array_map('trim', explode(',', $productColorsLabelsString));

            if (is_array($productColorsIds) && count($productColorsIds) > 0 && is_array($productColorsLabels) && count($productColorsLabels) > 0 && count($productColorsIds) == count($productColorsLabels)) {
                $productColors = array_combine($productColorsIds, $productColorsLabels);

                if (is_array($productColors) && count($productColors) > 0) {
                    $colorSwatchesData = $this->getColorSwatchesData();

                    $swatchAttributeInfo = $colorSwatchesData['attribute'];
                    $allSwatchAttributeOptions = $colorSwatchesData['attribute_options'];
                    $swatches = $colorSwatchesData['swatches'];

                    /* Prepare images */
                    $productImagesLabelUrl = array();
                    $productImages = $this->getProductImages($product);
                    if (isset($productImages[$product->getId()]) && is_array($productImages[$product->getId()]) && count($productImages[$product->getId()]) > 0) {
                        foreach ($productImages[$product->getId()] as $productImage) {
                            if (isset($productImage['caption']) && isset($productImage['img'])) {
                                $productImgAlterText = trim($productImage['caption']);
                                $productImgUrl = trim($productImage['img']);
                                if (strlen($productImgAlterText) > 0 && strlen($productImgUrl) > 0) {
                                    $productImagesLabelUrl[$productImgAlterText] = $productImgUrl;
                                }
                            }
                        }
                    }
                    /* END Prepare images */


                    if (count($allSwatchAttributeOptions) > 0 && count($swatches) > 0) {
                        $html .= '<div class="swatch-opt-' . $product->getId() . '">'; ?>

                        <?php /* TODO Delete
                        <div class="swatch-attribute color_swatch" attribute-code="color_swatch" attribute-id="364">
                            <div aria-activedescendant="" tabindex="0" aria-invalid="false" aria-required="true"
                                 role="listbox" aria-label="Color" class="swatch-attribute-options clearfix">
                                <div class="swatch-option color" id="option-label-color_swatch-364-item-284"
                                     aria-checked="false" aria-describedby="option-label-color_swatch-364" tabindex="0"
                                     option-type="1" option-id="284" option-label="Black" aria-label="Black"
                                     option-tooltip-thumb="" option-tooltip-value="#000000" role="option"
                                     style="background: #000000 no-repeat center; background-size: initial;"></div>
                                <div class="swatch-option color" id="option-label-color_swatch-364-item-299"
                                     aria-checked="false" aria-describedby="option-label-color_swatch-364" tabindex="0"
                                     option-type="1" option-id="299" option-label="White" aria-label="White"
                                     option-tooltip-thumb="" option-tooltip-value="#ffffff" role="option"
                                     style="background: #ffffff no-repeat center; background-size: initial;"></div>
                                <div class="swatch-option color" id="option-label-color_swatch-364-item-295"
                                     aria-checked="false" aria-describedby="option-label-color_swatch-364" tabindex="0"
                                     option-type="1" option-id="295" option-label="Red" aria-label="Red"
                                     option-tooltip-thumb="" option-tooltip-value="#ff0000" role="option"
                                     style="background: #ff0000 no-repeat center; background-size: initial;"></div>
                            </div>
                        </div> */ ?>

                        <?php $html .= '<div class="swatch-attribute ' . $swatchAttributeInfo->getData('attribute_code') . '"';
                        $html .= 'attribute-code="' . $swatchAttributeInfo->getData('attribute_code') . '"';
                        $html .= 'attribute-id="' . $swatchAttributeInfo->getId() . '">';

                        $html .= '<div class"swatch-label">' . __('Colors') . '</div>';

                        $html .= '<div aria-activedescendant="" tabindex="0" aria-invalid="false" aria-required="true" role="listbox" aria-label="' . $swatchAttributeInfo->getData('frontend_label') . '" class="swatch-attribute-options clearfix">';

                        foreach ($productColors as $productColorOptionId => $productColorOptionLabel) {
                            if (in_array($productColorOptionLabel, $allSwatchAttributeOptions)) {
                                $optionLabel = $productColorOptionLabel;
                                $optionColorHex = '#ffffff';

                                $swatchOptionId = array_search($optionLabel, $allSwatchAttributeOptions);
                                if (isset($swatches[$swatchOptionId])) {
                                    if (isset($swatches[$swatchOptionId]['value'])) {
                                        $optionColorHex = $swatches[$swatchOptionId]['value'];
                                    }
                                }

                                $html .= '<div class="swatch-option color" ';
                                $html .= 'id="option-label-' . $swatchAttributeInfo->getData('attribute_code') . '-' . $swatchAttributeInfo->getId() . '-item-' . $productColorOptionId . '" aria-checked="false" ';
                                $html .= 'aria-describedby="option-label-' . $swatchAttributeInfo->getData('attribute_code') . '-' . $swatchAttributeInfo->getId() . '" tabindex="0" option-type="1" ';
                                $html .= 'option-id="' . $productColorOptionId . '" option-label="' . $optionLabel . '" aria-label="' . $optionLabel . '" option-tooltip-thumb="" ';
                                $html .= 'option-tooltip-value="' . $optionColorHex . '" role="option" ';
                                $html .= 'option-image="' . (isset($productImagesLabelUrl[$optionLabel]) ? $productImagesLabelUrl[$optionLabel] : '') . '" role="option" ';
                                $html .= 'style="background: ' . $optionColorHex . ' no-repeat center; background-size: initial;"></div>';
                            }
                        } ?>

                        <?php $html .= '</div>'; ?>
                        <?php $html .= '</div>'; ?>
                        <?php $html .= '</div>';
                    }
                }
            }
        } ?>

        <?php return $html;
    }

    /**
     * Get color swatches data
     *
     * @return array
     */
    public function getColorSwatchesData() {
        $allSwatchAttributeOptions = array();
        $swatches = array();

        /**
         * Get attribute info by attribute code and entity type
         */
        $attributeCodeSwatch = 'color_swatch';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $entityType = $objectManager->create(
            \Magento\Eav\Model\Entity::class
        )->setType(
            \Magento\Catalog\Model\Product::ENTITY
        )->getTypeId();


        $swatchAttributeInfo = $objectManager->create(
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
        )->loadByCode(
            $entityType,
            $attributeCodeSwatch
        );

        if ($swatchAttributeInfo->getId()) {
            /**
             * @var $swatchHelper \Magento\Swatches\Helper\Data
             */
            $swatchHelper = $objectManager->get(\Magento\Swatches\Helper\Data::class);

            if ($swatchAttributeInfo->usesSource()) {
                $attributeOptions = $swatchAttributeInfo->getSource()->getAllOptions();
                foreach ($attributeOptions as $key => $option) {
                    $optionId = trim($option['value']);
                    $optionLabel = trim($option['label']);
                    if (strlen($optionId) > 0 && strlen($optionLabel) > 0) {
                        $allSwatchAttributeOptions[$optionId] = $optionLabel;
                    }
                }
            }

            $attributeOptionIds = array_keys($allSwatchAttributeOptions);
            $swatches = $swatchHelper->getSwatchesByOptionsId($attributeOptionIds);
        }

        return array(
            'attribute' => $swatchAttributeInfo,
            'attribute_options' => $allSwatchAttributeOptions,
            'swatches' => $swatches,
        );
    }

    /**
     * Get product images
     *
     * @param $product \Magento\Catalog\Model\Product
     * @return array
     */
    protected function getProductImages($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /**
         * @var $configurableProductHelper \Magento\ConfigurableProduct\Helper\Data
         */
        $configurableProductHelper = $objectManager->get(\Magento\ConfigurableProduct\Helper\Data::class);

        $images = [];

        $product->load('media_gallery');

        $productImages = $configurableProductHelper->getGalleryImages($product) ?: [];
        foreach ($productImages as $image) {
            $images[$product->getId()][] =
                [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => $image->getLabel(),
                    'position' => $image->getPosition(),
                    'isMain' => $image->getFile() == $product->getImage(),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ];
        }


        return $images;
    }

}