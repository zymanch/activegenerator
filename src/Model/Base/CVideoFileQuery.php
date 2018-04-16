<?php

namespace Model\Base;
use ActiveRecord\Criteria;
use Model\VideoFileQuery;

/**
 * This is the ActiveQuery class for [[Model\VideoFile]].
 * @method VideoFileQuery filterByVideoFileId($value, $criteria = null)
 * @method VideoFileQuery filterByVideoId($value, $criteria = null)
 * @method VideoFileQuery filterByMediaId($value, $criteria = null)
 * @method VideoFileQuery filterByVideoEncodingId($value, $criteria = null)
 * @method VideoFileQuery filterBySourceVideoFileId($value, $criteria = null)
 * @method VideoFileQuery filterByCreated($value, $criteria = null)
 * @method VideoFileQuery filterByFilename($value, $criteria = null)
 * @method VideoFileQuery filterByFilesize($value, $criteria = null)
 * @method VideoFileQuery filterByDuration($value, $criteria = null)
 * @method VideoFileQuery filterByWidth($value, $criteria = null)
 * @method VideoFileQuery filterByHeight($value, $criteria = null)
 * @method VideoFileQuery filterByFrameCount($value, $criteria = null)
 * @method VideoFileQuery filterByFrameRate($value, $criteria = null)
 * @method VideoFileQuery filterByBitrate($value, $criteria = null)
 * @method VideoFileQuery filterByEncodeStart($value, $criteria = null)
 * @method VideoFileQuery filterByEncodeFinish($value, $criteria = null)
 * @method VideoFileQuery filterByEncodeStatus($value, $criteria = null)
 * @method VideoFileQuery filterByPreparedForWmEmbedding($value, $criteria = null)
 * @method VideoFileQuery filterByVideoOrder($value, $criteria = null)
 * @method VideoFileQuery filterByRatio($value, $criteria = null)
 * @method VideoFileQuery filterByStatus($value, $criteria = null)
 * @method VideoFileQuery filterByIsExpirable($value, $criteria = null)
  * @method VideoFileQuery orderByVideoFileId($order = Criteria::ASC)
  * @method VideoFileQuery orderByVideoId($order = Criteria::ASC)
  * @method VideoFileQuery orderByMediaId($order = Criteria::ASC)
  * @method VideoFileQuery orderByVideoEncodingId($order = Criteria::ASC)
  * @method VideoFileQuery orderBySourceVideoFileId($order = Criteria::ASC)
  * @method VideoFileQuery orderByCreated($order = Criteria::ASC)
  * @method VideoFileQuery orderByFilename($order = Criteria::ASC)
  * @method VideoFileQuery orderByFilesize($order = Criteria::ASC)
  * @method VideoFileQuery orderByDuration($order = Criteria::ASC)
  * @method VideoFileQuery orderByWidth($order = Criteria::ASC)
  * @method VideoFileQuery orderByHeight($order = Criteria::ASC)
  * @method VideoFileQuery orderByFrameCount($order = Criteria::ASC)
  * @method VideoFileQuery orderByFrameRate($order = Criteria::ASC)
  * @method VideoFileQuery orderByBitrate($order = Criteria::ASC)
  * @method VideoFileQuery orderByEncodeStart($order = Criteria::ASC)
  * @method VideoFileQuery orderByEncodeFinish($order = Criteria::ASC)
  * @method VideoFileQuery orderByEncodeStatus($order = Criteria::ASC)
  * @method VideoFileQuery orderByPreparedForWmEmbedding($order = Criteria::ASC)
  * @method VideoFileQuery orderByVideoOrder($order = Criteria::ASC)
  * @method VideoFileQuery orderByRatio($order = Criteria::ASC)
  * @method VideoFileQuery orderByStatus($order = Criteria::ASC)
  * @method VideoFileQuery orderByIsExpirable($order = Criteria::ASC)
  * @method VideoFileQuery withVideo($params = [])
  * @method VideoFileQuery joinWithVideo($params = null, $joinType = 'LEFT JOIN')
  * @method VideoFileQuery withSourceVideoFile($params = [])
  * @method VideoFileQuery joinWithSourceVideoFile($params = null, $joinType = 'LEFT JOIN')
  * @method VideoFileQuery withVideoFiles($params = [])
  * @method VideoFileQuery joinWithVideoFiles($params = null, $joinType = 'LEFT JOIN')
 */
class CVideoFileQuery extends \ActiveRecord\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Model\VideoFile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Model\VideoFile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \Model\VideoFileQuery     */
    public static function model()
    {
        return new \Model\VideoFileQuery(\Model\VideoFile::class);
    }
}
