<?php
namespace ActiveGenerator\base;
use ActiveGenerator\Criteria;

/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 11.07.2018
 * Time: 10:06
 */
trait RichActiveMethods {

    public function __call($name, $params) {
        if (strpos($name,'filterBy')===0) {
            return $this->_filterBy(substr($name, 8), $params);
        }
        if (strpos($name, 'specify')===0) {
            return $this->_specify(substr($name, 7), $params);
        }
        if (strpos($name,'orderBy')===0) {
            return $this->_orderBy(substr($name, 7), $params);
        }
        if (strpos($name,'with')===0) {
            return $this->_with(substr($name, 4), $params);
        }
        if (strpos($name,'joinWith')===0) {
            return $this->_joinWith(substr($name, 8), $params);
        }
        return parent::__call($name, $params);
    }

    private function _filterBy($name, $params) {
        $fieldName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        list(,$alias) = $this->getTableNameAndAlias();
        return $this->filterByField(
            $alias.'.'.$fieldName,
            $params[0],
            isset($params[1]) ? $params[1] : null
        );
    }

    private function _orderBy($name, $params) {
        $fieldName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        list(,$alias) = $this->getTableNameAndAlias();
        return $this->addOrderBy([
             $alias.'.'.$fieldName => ($params?$params[0]:SORT_ASC)
        ]);
    }

    private function _with($name, $params) {
        $fieldName = lcfirst($name);
        return $this->with([$fieldName => isset($params[0]) ? $params[0] : []]);
    }

    private function _joinWith($name, $params) {
        $fieldName = lcfirst($name);
        $param0 = (isset($params[0]) ? $params[0] : null);
        $joinType = 'LEFT JOIN';
        if (isset($params[1])) {
            $joinType = $params[1];
        }
        return $this->joinWith(
            [$fieldName => $param0],
            true,
            $joinType
        );
    }

    private function _specify($name, $params) {
        $parts = explode('\\',$this->modelClass);
        $class = array_pop($parts);
        $parts[] = 'Specification';
        $parts[] = $class;
        $parts[] = $name;
        $filterClass = implode('\\', $parts);
        if (!class_exists($filterClass)) {
            throw new \Exception('Specification class '.$filterClass.' not found');
        }
        /** @var Specification $filter */
        $filter = new $filterClass();
        if (!($filter instanceof Specification)) {
            throw new \Exception($filterClass.' must be instance of FilterQuery');
        }
        $filter->specify($this, $params);
        return $this;
    }

    public function filterByField($field, $value, $criteria = null)  {
        if (is_null($criteria)) {
            if (is_array($value)) {
                $criteria = Criteria::IN;
            } else {
                $criteria = Criteria::EQUAL;
            }
        }
        switch ($criteria) {
            case Criteria::IN:
                $this->andWhere(['IN',$field, $value]);
                break;
            case Criteria::EQUAL:
                $this->andWhere(['=',$field, $value]);
                break;
            case Criteria::GREATER_THAN:
                $this->andWhere(['>',$field, $value]);
                break;
            case Criteria::ISNULL:
                $this->andWhere($field.' is null');
                break;
            case Criteria::NOT_EQUAL:;
                $this->andWhere(['<>',$field, $value]);
                break;
            case Criteria::LESS_THAN;
                $this->andWhere(['<',$field, $value]);
                break;
            case Criteria::GREATER_EQUAL;
                $this->andWhere(['>=',$field, $value]);
                break;
            case Criteria::LESS_EQUAL;
                $this->andWhere(['<=',$field, $value]);
                break;
            case Criteria::LIKE;
                $this->andWhere(['LIKE',$field, $value]);
                break;
            case Criteria::NOT_LIKE;
                $this->andWhere(['NOT LIKE',$field, $value]);
                break;
            case Criteria::CUSTOM;
                $this->andWhere($value);
                break;
            case Criteria::NOT_IN;
                $this->andWhere(['NOT IN',$field, $value]);
                break;
            case Criteria::ISNOTNULL;
                $this->andWhere($field.' is not null');
                break;
            default:
                throw new \Exception('Unknown operator:'.$criteria);
        }
        return $this;
    }


    /**
     * Returns the table name and the table alias for [[modelClass]].
     * @return array the table name and the table alias.
     * @internal
     */
    private function getTableNameAndAlias()
    {
        if (empty($this->from)) {
            /* @var $modelClass ActiveRecord */
            $modelClass = $this->modelClass;
            $tableName = $modelClass::tableName();
        } else {
            $tableName = '';
            foreach ($this->from as $alias => $tableName) {
                if (is_string($alias)) {
                    return [$tableName, $alias];
                } else {
                    break;
                }
            }
        }

        if (preg_match('/^(.*?)\s+({{\w+}}|\w+)$/', $tableName, $matches)) {
            $alias = $matches[2];
        } else {
            $alias = $tableName;
        }

        return [$tableName, $alias];
    }
}