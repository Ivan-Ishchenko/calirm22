<?php

namespace Ids\Configurabletabletier\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Catalog\Model\Locator\LocatorInterface;

class CustomOptions {

    const FIELD_USE_FOR_CHANGE_IMG_NAME = 'use_for_change_img';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject,
        $meta
    ) {

        $product = $this->locator->getProduct();

        if ($product) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
            /* @var $helper \Ids\Configurabletabletier\Helper\Data */
            $attributeSets = $helper->geAttributeSetsFromConfig();
            $productAttributeSetId = $product->getAttributeSetId();

            if ($helper->isModuleActive() && in_array($productAttributeSetId, $attributeSets)) {
                //TODO Use $productTypeId = $this->locator->getProduct()->getTypeId();

                $meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children'][self::FIELD_USE_FOR_CHANGE_IMG_NAME] = $this->getGetForChangeImageByLabelConfig();
            }
        }

        return $meta;
    }


    /**
     * Get config for "Use for change image by Label" field
     *
     * @return array
     */
    protected function getGetForChangeImageByLabelConfig() {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Use for change image'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => self::FIELD_USE_FOR_CHANGE_IMG_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => 200,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }

}