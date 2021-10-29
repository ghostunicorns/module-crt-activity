<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model\Config\Source;

use GhostUnicorns\CrtActivity\Model\ActivityStateInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach (ActivityStateInterface::ALL as $status) {
            $options[] = [
                'value' => $status,
                'label' => $status
            ];
        }

        return $options;
    }
}
