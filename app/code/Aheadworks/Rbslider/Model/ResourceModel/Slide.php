<?php
namespace Aheadworks\Rbslider\Model\ResourceModel;

/**
 * Class Slide
 * @package Aheadworks\Rbslider\Model\ResourceModel
 */
class Slide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_rbslider_slide', 'id');
    }
}
