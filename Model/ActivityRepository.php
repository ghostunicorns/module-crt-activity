<?php
/*
  * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtActivity\Model;

use Exception;
use GhostUnicorns\CrtActivity\Api\ActivityRepositoryInterface;
use GhostUnicorns\CrtActivity\Api\Data\ActivityInterface;
use GhostUnicorns\CrtActivity\Api\Data\ActivitySearchResultInterface;
use GhostUnicorns\CrtActivity\Api\Data\ActivitySearchResultInterfaceFactory;
use GhostUnicorns\CrtActivity\Model\ActivityModelFactory as ActivityFactory;
use GhostUnicorns\CrtActivity\Model\ResourceModel\Activity\ActivityCollection;
use GhostUnicorns\CrtActivity\Model\ResourceModel\Activity\ActivityCollectionFactory;
use GhostUnicorns\CrtActivity\Model\ResourceModel\ActivityResourceModel;
use GhostUnicorns\CrtBase\Model\Config;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class ActivityRepository implements ActivityRepositoryInterface
{
    /**
     * @var ActivityFactory
     */
    private $activityFactory;

    /**
     * @var ActivityCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ActivitySearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ActivityResourceModel
     */
    private $activityResourceModel;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ActivityModelFactory $activityFactory
     * @param ActivityCollectionFactory $collectionFactory
     * @param ActivitySearchResultInterfaceFactory $activitySearchResultInterfaceFactory
     * @param ActivityResourceModel $activityResourceModel
     * @param Config $config
     */
    public function __construct(
        ActivityFactory $activityFactory,
        ActivityCollectionFactory $collectionFactory,
        ActivitySearchResultInterfaceFactory $activitySearchResultInterfaceFactory,
        ActivityResourceModel $activityResourceModel,
        Config $config
    ) {
        $this->activityFactory = $activityFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $activitySearchResultInterfaceFactory;
        $this->activityResourceModel = $activityResourceModel;
        $this->config = $config;
    }

    /**
     * @param int $id
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getById(int $id): ActivityInterface
    {
        $activity = $this->activityFactory->create();
        $this->activityResourceModel->load($activity, $id);
        if (!$activity->getId()) {
            throw new NoSuchEntityException(__('Unable to find CrtActivity with ID "%1"', $id));
        }
        return $activity;
    }

    /**
     * @param string $type
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getFirstCollectedByType(string $type): ActivityInterface
    {
        return $this->getOneByTypeAndStatus(
            $type,
            ActivityStateInterface::COLLECTED,
            Collection::SORT_ORDER_ASC
        );
    }

    /**
     * @param string $type
     * @param string $status
     * @param string $order
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getOneByTypeAndStatus(string $type, string $status, string $order): ActivityInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ActivityModel::TYPE, ['eq' => $type]);
        $collection->addFieldToFilter(ActivityModel::STATUS, ['eq' => $status]);
        $collection->addOrder(ActivityModel::CREATED_AT, $order);

        /** @var ActivityInterface $activity */
        $activity = $collection->getFirstItem();

        if (!$activity->getId()) {
            throw new NoSuchEntityException(
                __(
                    'Zero CrtActivity record found with status "%1" and type "%2"',
                    $status,
                    $type
                )
            );
        }

        return $activity;
    }

    /**
     * @param string $type
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getLastCollectedOrTransferedByType(string $type): ActivityInterface
    {
        return $this->getOneByTypeAndStatuses(
            $type,
            [
                ActivityStateInterface::COLLECTED,
                ActivityStateInterface::TRANSFERED
            ],
            Collection::SORT_ORDER_DESC
        );
    }

    /**
     * @param string $type
     * @param array $statuses
     * @param string $order
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getOneByTypeAndStatuses(string $type, array $statuses, string $order): ActivityInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ActivityModel::TYPE, ['eq' => $type]);
        $collection->addFieldToFilter(ActivityModel::STATUS, ['in' => $statuses]);
        $collection->addOrder(ActivityModel::CREATED_AT, $order);

        /** @var ActivityInterface $activity */
        $activity = $collection->getFirstItem();

        if (!$activity->getId()) {
            throw new NoSuchEntityException(
                __(
                    'Zero CrtActivity record found with status "%1" and type "%2"',
                    implode(',', $statuses),
                    $type
                )
            );
        }

        return $activity;
    }

    /**
     * @param string $type
     * @return ActivityInterface|ActivityModelFactory
     * @throws NoSuchEntityException
     */
    public function getFirstRefinedByType(string $type): ActivityInterface
    {
        return $this->getOneByTypeAndStatus($type, ActivityStateInterface::REFINED, Collection::SORT_ORDER_ASC);
    }

    /**
     * @param ActivityInterface $activity
     * @return ActivityInterface
     * @throws AlreadyExistsException
     */
    public function save(ActivityInterface $activity)
    {
        $this->activityResourceModel->save($activity);
        return $activity;
    }

    /**
     * @param ActivityInterface $activity
     * @throws Exception
     */
    public function delete(ActivityInterface $activity)
    {
        $this->activityResourceModel->delete($activity);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ActivitySearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ActivitySearchResultInterface
    {
        $collection = $this->collectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ActivityCollection $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, ActivityCollection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ActivityCollection $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, ActivityCollection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ActivityCollection $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, ActivityCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ActivityCollection $collection
     * @return ActivitySearchResultInterface
     */
    private function buildSearchResult(
        SearchCriteriaInterface $searchCriteria,
        ActivityCollection $collection
    ): ActivitySearchResultInterface {
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
