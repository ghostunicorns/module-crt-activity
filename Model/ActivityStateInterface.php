<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model;

interface ActivityStateInterface
{
    const COLLECTING = 'collecting';
    const COLLECTED = 'collected';
    const COLLECT_ERROR = 'collect_error';

    const REFINING = 'refining';
    const REFINED = 'refined';
    const REFINE_ERROR = 'refine_error';

    const TRANSFERING = 'transfering';
    const TRANSFERED = 'transfered';
    const TRANSFER_ERROR = 'transfer_error';

    public const ALL = [
        self::COLLECTING,
        self::COLLECTED,
        self::COLLECT_ERROR,
        self::REFINING,
        self::REFINED,
        self::REFINE_ERROR,
        self::TRANSFERING,
        self::TRANSFERED,
        self::TRANSFER_ERROR
    ];
}
