<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\ResourceModel\Theme\Customization;

/**
 * Theme customization link resource model
 */
class Update extends \Magento\Framework\Model\ModelResource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('theme_file_update', 'file_update_id');
    }
}
