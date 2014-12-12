<?php

namespace Api43\FeedBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * FeedRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedRepository extends DocumentRepository
{
    /**
     * Find feeds ordered by updated date
     *
     * @param integer|null $limit Items to retrieve
     *
     * @return \Doctrine\ODM\MongoDB\EagerCursor
     */
    public function findAllOrderedByDate($limit = null)
    {
        $q = $this->createQueryBuilder()
            ->eagerCursor(true)
            ->sort('updated_at', 'DESC');

        if (null !== $limit) {
            $q->limit($limit);
        }

        return $q->getQuery()->execute();
    }

    /**
     * Find feeds for public display
     *
     * @return \Doctrine\ODM\MongoDB\EagerCursor
     */
    public function findForPublic()
    {
        $q = $this->createQueryBuilder()
            ->eagerCursor(true)
            ->field('is_private')->equals(false)
            ->sort('last_item_cached_at', 'DESC');

        return $q->getQuery()->execute();
    }

    /**
     * Find feed by ids.
     * Used in FetchItemCommand to retrieve feed that have / or not items
     *
     * @param Array  $ids  An array of MongoID
     * @param string $type in or notIn
     *
     * @return \Doctrine\ODM\MongoDB\EagerCursor|bool
     */
    public function findByIds($ids, $type = 'in')
    {
        $q = $this->createQueryBuilder()
            ->field('id');

        if ('in' == $type) {
            $q->in($ids);
        } else {
            $q->notIn($ids);
        }

        return $q->eagerCursor(true)
            ->getQuery()
            ->execute();
    }
}