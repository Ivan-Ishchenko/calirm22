<?php

namespace Ids\Configurabletabletier\Plugin\Block\Cart;


class Image {

    /**
     * @param $item \Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable
     * @param $result
     * @return mixed
     */
    public function afterGetImage($item, $result) {
        $_product = $item->getProduct();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $customOptionsDataToUseForChangeImg = array();

        /**
         * @var $customOptions \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
         */
        $customOptions = $objectManager->get(\Magento\Catalog\Model\Product\Option::class)->getProductOptionCollection($_product);
        $customOptions->addFieldToFilter(\Ids\Configurabletabletier\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::FIELD_USE_FOR_CHANGE_IMG_NAME, 1);
        if ($customOptions && $customOptions->getSize() > 0) {
            foreach ($customOptions as $customOption) {
                $optionId = (int)$customOption->getData('option_id');
                $optionUseForChangeImg = (int)$customOption->getData(\Ids\Configurabletabletier\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::FIELD_USE_FOR_CHANGE_IMG_NAME);

                if ($optionId > 0 && $optionUseForChangeImg == 1) {
                    $customOptionsDataToUseForChangeImg[$optionId] = array(
                        'option_id' => $optionId,
                    );
                }
            }
        }

        $optionValueForCustomImage = '';
        $optionList = $item->getOptionList();
        if (count($optionList) > 0) {
            foreach ($optionList as $optionData) {
                if (isset($optionData['option_id'])) {
                    $optionId = $optionData['option_id'];
                    if (strlen($optionId) > 0 && isset($customOptionsDataToUseForChangeImg[$optionId])) {
                        if (isset($optionData['value'])) {
                            $optionValueForCustomImage = trim($optionData['value']);
                            if (strlen($optionValueForCustomImage) > 0) {
                                break;
                            }
                        }
                    }
                }
            }
        }


        if (mb_strlen($optionValueForCustomImage, 'UTF-8') > 0) {
            $productImages = $this->getProductImages($_product);
            if (isset($productImages[$_product->getId()]) && is_array($productImages[$_product->getId()]) && count($productImages[$_product->getId()]) > 0) {
                foreach ($productImages[$_product->getId()] as $productImage) {
                    if (isset($productImage['caption']) && isset($productImage['img'])) {
                        $productImgAlterText = trim($productImage['caption']);
                        $productImgUrl = trim($productImage['img']);

                        if (strlen($productImgAlterText) > 0 && strlen($productImgUrl) > 0 && $productImgAlterText == $optionValueForCustomImage) {
                            $result->setImageUrl($productImgUrl);
                        }
                    }
                }
            }
        }
        return $result;
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