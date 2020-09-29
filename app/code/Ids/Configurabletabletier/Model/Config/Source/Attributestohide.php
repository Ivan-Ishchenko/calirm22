<?php

namespace Ids\Configurabletabletier\Model\Config\Source;

class Attributestohide implements \Magento\Framework\Option\ArrayInterface {

    public function toOptionArray() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$attributeSetOptions = $objectManager->get(\Magento\Catalog\Model\Product\AttributeSet\Options::class); /* @var $attributeSetOptions \Magento\Catalog\Model\Product\AttributeSet\Options*/

        /** @var  $coll \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection */
        $coll = $objectManager->get(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::class);
        // add filter by entity type to get product attributes only
        // '4' is the default type ID for 'catalog_product' entity - see 'eav_entity_type' table)
        // or skip the next line to get all attributes for all types of entities
        //$coll->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, \Magento\Catalog\Model\Product::ENTITY);
        $coll->addFieldToFilter('is_user_defined', 1);
        $coll->addFieldToFilter('entity_type_id', 4);
        //$attributes = $coll->load()->toOptionArray();
        $attributes = $coll->load()->toArray();
        //$attributes = $coll->load()->getItems();

        $res = array();
        if(count($attributes) > 0 && isset($attributes['totalRecords']) && $attributes['totalRecords'] > 0
            && isset($attributes['items']) && is_array($attributes['items']) && count($attributes['items']) > 0) {

            foreach ($attributes['items'] as $item) {
                $valueField = isset($item['attribute_id']) ? trim($item['attribute_id']) : '';

                $labelFieldParts = array();
                if(isset($item['frontend_label']) && mb_strlen(trim($item['frontend_label']), 'UTF-8') > 0) {
                    $labelFieldParts[] = $item['frontend_label'];
                }
                if(isset($item['attribute_code']) && mb_strlen(trim($item['attribute_code']), 'UTF-8') > 0) {
                    $labelFieldParts[] = $item['attribute_code'];
                }

                $labelField = implode(' - ', $labelFieldParts);

                if(strlen($valueField) > 0 && mb_strlen($labelField, 'UTF-8') > 0) {
                    $res[] = array(
                        'value' => $valueField,
                        'label' => $labelField,
                    );
                }
            }
        }

        return $res;
    }

}