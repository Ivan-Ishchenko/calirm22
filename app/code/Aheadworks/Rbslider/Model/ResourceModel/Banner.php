<?php
namespace Aheadworks\Rbslider\Model\ResourceModel;

/**
 * Class Banner
 * @package Aheadworks\Rbslider\Model\ResourceModel
 */
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_rbslider_banner', 'id');
    }
}
