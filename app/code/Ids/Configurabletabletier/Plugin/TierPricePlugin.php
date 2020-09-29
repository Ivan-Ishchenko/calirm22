<?php

namespace Ids\Configurabletabletier\Plugin;

class TierPricePlugin {

    public function beforeSetTemplate(\Magento\Catalog\Pricing\Price\TierPrice $subject, $template) {




        echo '<pre>';
        print_r('4444');
        echo '</pre>';
        exit;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var $helper \Ids\Configurabletabletier\Helper\Data
         */
        $helper = $objectManager->get(\Ids\Configurabletabletier\Helper\Data::class);
        if ($helper->isModuleActive()) {
            //if ($template == 'Magento_Catalog::product/price/final_price.phtml') {
            return ['Ids_Configurabletabletier::product/price/tier_prices.phtml'];
            //}
            /*else
            {
                return [$template];
            }*/
        } else {
            return [$template];
        }
    }
}