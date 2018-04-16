<?php

namespace Model\Base;
use ActiveGenerator\Criteria;
use Model\FileObjectTypeQuery;

/**
 * This is the ActiveQuery class for [[Model\FileObjectType]].
 * @method FileObjectTypeQuery filterByFileObjectTypeId($value, $criteria = null)
 * @method FileObjectTypeQuery filterByContentTypeId($value, $criteria = null)
 * @method FileObjectTypeQuery filterBySecondaryContentTypeId($value, $criteria = null)
 * @method FileObjectTypeQuery filterByMediaType($value, $criteria = null)
 * @method FileObjectTypeQuery filterByFileObjectTypeFamilyId($value, $criteria = null)
 * @method FileObjectTypeQuery filterByName($value, $criteria = null)
 * @method FileObjectTypeQuery filterByConstant($value, $criteria = null)
 * @method FileObjectTypeQuery filterByDescription($value, $criteria = null)
 * @method FileObjectTypeQuery filterByOptions($value, $criteria = null)
 * @method FileObjectTypeQuery filterByShareFiles($value, $criteria = null)
 * @method FileObjectTypeQuery filterByWmEmbedding($value, $criteria = null)
 * @method FileObjectTypeQuery filterByFetchVideoDurationPriority($value, $criteria = null)
  * @method FileObjectTypeQuery orderByFileObjectTypeId($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByContentTypeId($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderBySecondaryContentTypeId($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByMediaType($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByFileObjectTypeFamilyId($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByName($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByConstant($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByDescription($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByOptions($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByShareFiles($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByWmEmbedding($order = Criteria::ASC)
  * @method FileObjectTypeQuery orderByFetchVideoDurationPriority($order = Criteria::ASC)
  * @method FileObjectTypeQuery withFileObjectTypeFamily($params = [])
  * @method FileObjectTypeQuery joinWithFileObjectTypeFamily($params = null, $joinType = 'LEFT JOIN')
  * @method FileObjectTypeQuery withVideos($params = [])
  * @method FileObjectTypeQuery joinWithVideos($params = null, $joinType = 'LEFT JOIN')
 */
class CFileObjectTypeQuery extends \ActiveGenerator\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Model\FileObjectType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Model\FileObjectType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \Model\FileObjectTypeQuery     */
    public static function model()
    {
        return new \Model\FileObjectTypeQuery(\Model\FileObjectType::class);
    }
}
