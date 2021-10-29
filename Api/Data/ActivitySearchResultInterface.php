<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ActivitySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return ActivityInterface[]
     */
    public function getItems();

    /**
     * @param ActivityInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
