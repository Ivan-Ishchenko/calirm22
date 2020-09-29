<?php

namespace Ids\Configurabletabletier\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->addFileColumnToCatalogProductOptionTable_1_0_0($setup);
        }

        $installer->endSetup();
    }


    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addFileColumnToCatalogProductOptionTable_1_0_0(SchemaSetupInterface $setup) {

        $setup->getConnection()->addColumn(
            $setup->getTable('catalog_product_option'),
            \Ids\Configurabletabletier\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::FIELD_USE_FOR_CHANGE_IMG_NAME,
            array(
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Use for change image',
            )
        );

    }

}