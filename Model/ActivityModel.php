<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model;

use DateTime;
use Exception;
use GhostUnicorns\CrtActivity\Api\Data\ActivityInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Serialize\Serializer\Json;

class ActivityModel extends AbstractExtensibleModel implements ActivityInterface
{
    const ID = 'activity_id';
    const TYPE = 'type';
    const STATUS = 'status';
    const EXTRA = 'extra';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const CACHE_TAG = 'crt_activity';
    protected $_cacheTag = 'crt_activity';
    protected $_eventPrefix = 'crt_activity';

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->setData(self::TYPE, $type);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    /**
     * @param $value
     */
    public function setStatus($value)
    {
        $this->setData(self::STATUS, $value);
    }

    /**
     * @param array $value
     */
    public function addExtraArray(array $value)
    {
        $newValue = new DataObject($this->getExtra()->getData());
        $newValue->addData($value);
        $this->setData(self::EXTRA, $newValue->toJson());
    }

    /**
     * @param DataObject $value
     */
    public function setExtra(DataObject $value)
    {
        $this->setData(self::EXTRA, $value->toJson());
    }

    /**
     * @return DataObject
     */
    public function getExtra(): DataObject
    {
        $serializer = new Json();
        $data = $this->getData(self::EXTRA) ? $serializer->unserialize($this->getData(self::EXTRA)) : [];
        return new DataObject($data);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->getData(self::CREATED_AT));
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getUpdatedAt(): DateTime
    {
        return new DateTime($this->getData(self::UPDATED_AT));
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\ActivityResourceModel::class);
    }
}
