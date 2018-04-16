<?php

namespace Model\Base;
use ActiveGenerator\Criteria;
use Model\VideoQuery;

/**
 * This is the ActiveQuery class for [[Model\Video]].
 * @method VideoQuery filterByVideoId($value, $criteria = null)
 * @method VideoQuery filterByUploadId($value, $criteria = null)
 * @method VideoQuery filterByFileObjectTypeId($value, $criteria = null)
 * @method VideoQuery filterByCompanyId($value, $criteria = null)
 * @method VideoQuery filterByUserId($value, $criteria = null)
 * @method VideoQuery filterByContentId($value, $criteria = null)
 * @method VideoQuery filterBySecondaryContentId($value, $criteria = null)
 * @method VideoQuery filterByStorageId($value, $criteria = null)
 * @method VideoQuery filterBySubpath($value, $criteria = null)
 * @method VideoQuery filterByUrl($value, $criteria = null)
 * @method VideoQuery filterByVideoOrder($value, $criteria = null)
 * @method VideoQuery filterByCreated($value, $criteria = null)
 * @method VideoQuery filterByStatus($value, $criteria = null)
 * @method VideoQuery filterBySubscriptionNewsState($value, $criteria = null)
  * @method VideoQuery orderByVideoId($order = Criteria::ASC)
  * @method VideoQuery orderByUploadId($order = Criteria::ASC)
  * @method VideoQuery orderByFileObjectTypeId($order = Criteria::ASC)
  * @method VideoQuery orderByCompanyId($order = Criteria::ASC)
  * @method VideoQuery orderByUserId($order = Criteria::ASC)
  * @method VideoQuery orderByContentId($order = Criteria::ASC)
  * @method VideoQuery orderBySecondaryContentId($order = Criteria::ASC)
  * @method VideoQuery orderByStorageId($order = Criteria::ASC)
  * @method VideoQuery orderBySubpath($order = Criteria::ASC)
  * @method VideoQuery orderByUrl($order = Criteria::ASC)
  * @method VideoQuery orderByVideoOrder($order = Criteria::ASC)
  * @method VideoQuery orderByCreated($order = Criteria::ASC)
  * @method VideoQuery orderByStatus($order = Criteria::ASC)
  * @method VideoQuery orderBySubscriptionNewsState($order = Criteria::ASC)
  * @method VideoQuery withFileObjectType($params = [])
  * @method VideoQuery joinWithFileObjectType($params = null, $joinType = 'LEFT JOIN')
  * @method VideoQuery withVideoFiles($params = [])
  * @method VideoQuery joinWithVideoFiles($params = null, $joinType = 'LEFT JOIN')
 */
class CVideoQuery extends \ActiveGenerator\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Model\Video[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Model\Video|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \Model\VideoQuery     */
    public static function model()
    {
        return new \Model\VideoQuery(\Model\Video::class);
    }
}
