<?php

namespace Model\Base;
use ActiveGenerator\Criteria;
use Model\FileObjectTypeFamilyQuery;

/**
 * This is the ActiveQuery class for [[Model\FileObjectTypeFamily]].
 * @method FileObjectTypeFamilyQuery filterByFileObjectTypeFamilyId($value, $criteria = null)
 * @method FileObjectTypeFamilyQuery filterByName($value, $criteria = null)
 * @method FileObjectTypeFamilyQuery filterByConstant($value, $criteria = null)
 * @method FileObjectTypeFamilyQuery filterByDescription($value, $criteria = null)
 * @method FileObjectTypeFamilyQuery filterByCategory($value, $criteria = null)
  * @method FileObjectTypeFamilyQuery orderByFileObjectTypeFamilyId($order = Criteria::ASC)
  * @method FileObjectTypeFamilyQuery orderByName($order = Criteria::ASC)
  * @method FileObjectTypeFamilyQuery orderByConstant($order = Criteria::ASC)
  * @method FileObjectTypeFamilyQuery orderByDescription($order = Criteria::ASC)
  * @method FileObjectTypeFamilyQuery orderByCategory($order = Criteria::ASC)
  * @method FileObjectTypeFamilyQuery withFileObjectTypes($params = [])
  * @method FileObjectTypeFamilyQuery joinWithFileObjectTypes($params = null, $joinType = 'LEFT JOIN')
 */
class CFileObjectTypeFamilyQuery extends \ActiveGenerator\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Model\FileObjectTypeFamily[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Model\FileObjectTypeFamily|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \Model\FileObjectTypeFamilyQuery     */
    public static function model()
    {
        return new \Model\FileObjectTypeFamilyQuery(\Model\FileObjectTypeFamily::class);
    }
}
