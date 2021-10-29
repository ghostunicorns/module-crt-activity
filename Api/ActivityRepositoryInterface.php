<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Api;

use GhostUnicorns\CrtActivity\Api\Data\ActivityInterface;
use GhostUnicorns\CrtActivity\Api\Data\ActivitySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface ActivityRepositoryInterface
{
    /**
     * @param int $id
     * @return ActivityInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ActivityInterface;

    /**
     * @param string $type
     * @return ActivityInterface
     * @throws NoSuchEntityException
     */
    public function getFirstCollectedByType(string $type): ActivityInterface;

    /**
     * @param string $type
     * @return ActivityInterface
     * @throws NoSuchEntityException
     */
    public function getLastCollectedOrTransferedByType(string $type): ActivityInterface;

    /**
     * @param string $type
     * @return ActivityInterface
     * @throws NoSuchEntityException
     */
    public function getFirstRefinedByType(string $type): ActivityInterface;

    /**
     * @param ActivityInterface $activity
     * @return ActivityInterface
     */
    public function save(ActivityInterface $activity);

    /**
     * @param ActivityInterface $activity
     * @return void
     */
    public function delete(ActivityInterface $activity);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ActivitySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ActivitySearchResultInterface;
}
