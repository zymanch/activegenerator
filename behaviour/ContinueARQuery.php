<?php
/**
 * Created by PhpStorm.
 * User: AmorPro
 * Date: 24.08.2017
 * Time: 22:08
 *
 *******************************************
 * EXAMPLE HO USE:
 *
 * $query = Good::model()->
 *   filterByStatus('disabled')->
 *   filterByCreated(time()-3600*24*100, Criteria::GREATER_THAN)->
 *   limit(20);
 *
 * $this->_continue('disabled_goods', $query, function(Good $good) {
 *   printf("Good %d processed\n", $good->good_id);
 * });
 *
 *******************************************
 */
trait Core_Behaviour_ContinueARQuery
{
    
    /**
     * @return self
     */
    protected function _continue($cacheKey, \ActiveRecord\db\ActiveQuery $query, $callback) {
        $normalizedCacheKey = $this->_getNormalizedCacheKey($cacheKey);

        $pkColumn = $this->_getPk($query);
        $this->_sortQuery($query, $pkColumn);
        $lastPkValue = $this->_getLastPk($normalizedCacheKey);
        if ($lastPkValue) {
            $this->_appendFilterByLastPk($query, $pkColumn, $lastPkValue);
        }
        $models = $this->_getModelsToProcess($query);
        foreach ($models as $model) {
            $callback($model);
            $this->_saveProcessedPk($normalizedCacheKey, $model->getPrimaryKey());
        }
    }

    /**
     * @param \ActiveRecord\db\ActiveQuery $query
     * @return string
     */
    private function _getPk(\ActiveRecord\db\ActiveQuery $query) {
        /** @var string $class */
        $class = $query->modelClass;
        $pks = $class::primaryKey();
        if (count($pks)!==1) {
            throw new Core_Exception_Loggable('PK columns can`t be multiple for continue_propel_query');
        }
        return reset($pks);
    }

    private function _sortQuery(\ActiveRecord\db\ActiveQuery $query, $pkColumn) {
        $query->orderBy($pkColumn);
    }

    private function _getLastPk($normalizedCacheKey) {
        return Core_Registry::cacheFast()->get($normalizedCacheKey);
    }

    private function _getNormalizedCacheKey($cacheKey) {
        return sprintf('continue:%s_%s',static::class, $cacheKey);
    }

    private function _appendFilterByLastPk(\ActiveRecord\db\ActiveQuery $query, $pkColumn, $lastPkValue) {
        if (!$lastPkValue) {
            return;
        }
        list(,$alias) = $this->_getTableNameAndAlias($query);
        $query->andWhere($alias.'.'.$pkColumn.'>'.(int)$lastPkValue);
    }

    /**
     * @param \ActiveRecord\db\ActiveQuery $query
     * @return \ActiveRecord\db\ActiveRecord[]
     */
    private function _getModelsToProcess(\ActiveRecord\db\ActiveQuery $query) {
        return $query->all();
    }

    private function _saveProcessedPk($normalizedCacheKey, $pkValue) {
        Core_Registry::cacheFast()->set($normalizedCacheKey, $pkValue, 3600*24*365*10);
    }

    private function _getTableNameAndAlias(\ActiveRecord\db\ActiveQuery $query)
    {
        if (empty($query->from)) {
            /* @var $modelClass \ActiveRecord\db\ActiveRecord */
            $modelClass = $query->modelClass;
            $tableName = $modelClass::tableName();
        } else {
            $tableName = '';
            foreach ($query->from as $alias => $tableName) {
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