<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Api\Data;

use DateTime;
use Exception;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;

interface ActivityInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status);

    /**
     * @param array $value
     * @return void
     */
    public function addExtraArray(array $value);

    /**
     * @param DataObject $value
     * @return void
     */
    public function setExtra(DataObject $value);

    /**
     * @return DataObject
     */
    public function getExtra(): DataObject;

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getCreatedAt(): DateTime;

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getUpdatedAt(): DateTime;
}
