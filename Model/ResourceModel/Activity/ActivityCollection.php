<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model\ResourceModel\Activity;

use GhostUnicorns\CrtActivity\Model\ActivityModel;
use GhostUnicorns\CrtActivity\Model\ResourceModel\ActivityResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ActivityCollection extends AbstractCollection
{
    protected $_idFieldName = 'activity_id';
    protected $_eventPrefix = 'crt_activity_collection';
    protected $_eventObject = 'activity_collection';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ActivityModel::class, ActivityResourceModel::class);
    }
}
