<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model;

use DateInterval;
use DateTime;
use Exception;
use GhostUnicorns\CrtActivity\Model\ResourceModel\Activity\ActivityCollectionFactory;
use GhostUnicorns\CrtBase\Model\Config;

class HasRunningActivity
{

    /**
     * @var ActivityCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ActivityCollectionFactory $collectionFactory
     * @param Config $config
     */
    public function __construct(
        ActivityCollectionFactory $collectionFactory,
        Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function execute(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::COLLECTING,
            ActivityStateInterface::COLLECTED,
            ActivityStateInterface::REFINING,
            ActivityStateInterface::REFINED,
            ActivityStateInterface::TRANSFERING
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function hasCollecting(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::COLLECTING
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function hasRefining(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::REFINING
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function hasTransfering(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::TRANSFERING
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function hasCollected(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::COLLECTED
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function hasRefined(string $type): bool
    {
        $statuses = [
            ActivityStateInterface::REFINED
        ];
        return $this->check($statuses, $type);
    }

    /**
     * @param array $statuses
     * @param string $type
     * @return bool
     * @throws Exception
     */
    private function check(array $statuses, string $type): bool
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ActivityModel::TYPE, ['eq' => $type]);
        $collection->addFieldToFilter(ActivityModel::STATUS, ['in' => $statuses]);

        $activities = $collection->getItems();

        $hasRunningActivity = false;

        if (count($activities)) {
            $semaphoreThreshold = $this->config->getSemaphoreThreshold();
            $expireDateTime = new DateTime('now');
            $expireDateTime->sub(new DateInterval('PT' . $semaphoreThreshold . 'M'));
            /** @var ActivityModel $activity */
            foreach ($activities as $activity) {
                if ($activity->getCreatedAt() > $expireDateTime) {
                    $hasRunningActivity = true;
                    break;
                }
            }
        }

        return $hasRunningActivity;
    }
}
