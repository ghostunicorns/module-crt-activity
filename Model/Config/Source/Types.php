<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model\Config\Source;

use GhostUnicorns\CrtBase\Api\CrtListInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Types implements OptionSourceInterface
{
    /**
     * @var CrtListInterface
     */
    private $crtList;

    /**
     * @param CrtListInterface $crtList
     */
    public function __construct(
        CrtListInterface $crtList
    ) {
        $this->crtList = $crtList;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        $allDownlaoderList = $this->crtList->getAllCollectorList();

        $types = array_keys($allDownlaoderList);

        foreach ($types as $type) {
            $options[] = [
                'value' => $type,
                'label' => $type
            ];

        }

        return $options;
    }
}
