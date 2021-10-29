<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model;

use GhostUnicorns\CrtActivity\Api\Data\ActivitySearchResultInterface;
use Magento\Framework\Api\Search\SearchResult;

class ActivitySearchResult extends SearchResult implements ActivitySearchResultInterface
{

}
