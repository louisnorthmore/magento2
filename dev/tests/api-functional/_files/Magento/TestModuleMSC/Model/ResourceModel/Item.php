<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleMSC\Model\ResourceModel;

/**
 * Sample resource model
 */
class Item extends \Magento\Framework\Model\ModelResource\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dummy_item', 'dummy_item_id');
    }
}
