<?php

namespace Model\Base;



/**
 * This is the model class for table "gtf.video_file".
 *
 * @property string $video_file_id
 * @property string $video_id
 * @property string $media_id
 * @property string $video_encoding_id
 * @property string $source_video_file_id
 * @property string $created
 * @property string $filename
 * @property string $filesize
 * @property double $duration
 * @property string $width
 * @property string $height
 * @property string $frame_count
 * @property double $frame_rate
 * @property string $bitrate
 * @property string $encode_start
 * @property string $encode_finish
 * @property string $encode_status
 * @property string $prepared_for_wm_embedding
 * @property string $video_order
 * @property string $ratio
 * @property string $status
 * @property string $is_expirable
 *
 * @property \Model\Video $video
 * @property \Model\VideoFile $sourceVideoFile
 * @property \Model\VideoFile[] $videoFiles
 */
class CVideoFile extends \ActiveGenerator\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gtf.video_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[CVideoFilePeer::VIDEO_ID, CVideoFilePeer::MEDIA_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME, CVideoFilePeer::FILESIZE, CVideoFilePeer::DURATION, CVideoFilePeer::WIDTH, CVideoFilePeer::HEIGHT, CVideoFilePeer::FRAME_COUNT, CVideoFilePeer::FRAME_RATE, CVideoFilePeer::BITRATE, CVideoFilePeer::ENCODE_START, CVideoFilePeer::ENCODE_FINISH, CVideoFilePeer::ENCODE_STATUS, CVideoFilePeer::VIDEO_ORDER], 'required'],
            [[CVideoFilePeer::VIDEO_ID, CVideoFilePeer::MEDIA_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::SOURCE_VIDEO_FILE_ID, CVideoFilePeer::FILESIZE, CVideoFilePeer::WIDTH, CVideoFilePeer::HEIGHT, CVideoFilePeer::FRAME_COUNT, CVideoFilePeer::BITRATE, CVideoFilePeer::VIDEO_ORDER], 'integer'],
            [[CVideoFilePeer::CREATED, CVideoFilePeer::ENCODE_START, CVideoFilePeer::ENCODE_FINISH], 'safe'],
            [[CVideoFilePeer::DURATION, CVideoFilePeer::FRAME_RATE], 'number'],
            [[CVideoFilePeer::ENCODE_STATUS, CVideoFilePeer::PREPARED_FOR_WM_EMBEDDING, CVideoFilePeer::RATIO, CVideoFilePeer::STATUS, CVideoFilePeer::IS_EXPIRABLE], 'string'],
            [[CVideoFilePeer::FILENAME], 'string', 'max' => 255],
            [[CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME], 'unique', 'targetAttribute' => [CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME]],
            [[CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME], 'unique', 'targetAttribute' => [CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME]],
            [[CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME], 'unique', 'targetAttribute' => [CVideoFilePeer::VIDEO_ID, CVideoFilePeer::VIDEO_ENCODING_ID, CVideoFilePeer::FILENAME]],
            [[CVideoFilePeer::VIDEO_ID], 'exist', 'skipOnError' => true, 'targetClass' => CVideo::className(), 'targetAttribute' => [CVideoFilePeer::VIDEO_ID => CVideoPeer::VIDEO_ID]],
            [[CVideoFilePeer::SOURCE_VIDEO_FILE_ID], 'exist', 'skipOnError' => true, 'targetClass' => CVideoFile::className(), 'targetAttribute' => [CVideoFilePeer::SOURCE_VIDEO_FILE_ID => CVideoFilePeer::VIDEO_FILE_ID]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            CVideoFilePeer::VIDEO_FILE_ID => 'Video File ID',
            CVideoFilePeer::VIDEO_ID => 'Video ID',
            CVideoFilePeer::MEDIA_ID => 'Media ID',
            CVideoFilePeer::VIDEO_ENCODING_ID => 'Video Encoding ID',
            CVideoFilePeer::SOURCE_VIDEO_FILE_ID => 'Source Video File ID',
            CVideoFilePeer::CREATED => 'Created',
            CVideoFilePeer::FILENAME => 'Filename',
            CVideoFilePeer::FILESIZE => 'Filesize',
            CVideoFilePeer::DURATION => 'Duration',
            CVideoFilePeer::WIDTH => 'Width',
            CVideoFilePeer::HEIGHT => 'Height',
            CVideoFilePeer::FRAME_COUNT => 'Frame Count',
            CVideoFilePeer::FRAME_RATE => 'Frame Rate',
            CVideoFilePeer::BITRATE => 'Bitrate',
            CVideoFilePeer::ENCODE_START => 'Encode Start',
            CVideoFilePeer::ENCODE_FINISH => 'Encode Finish',
            CVideoFilePeer::ENCODE_STATUS => 'Encode Status',
            CVideoFilePeer::PREPARED_FOR_WM_EMBEDDING => 'Prepared For Wm Embedding',
            CVideoFilePeer::VIDEO_ORDER => 'Video Order',
            CVideoFilePeer::RATIO => 'Ratio',
            CVideoFilePeer::STATUS => 'Status',
            CVideoFilePeer::IS_EXPIRABLE => 'Is Expirable',
        ];
    }
    /**
     * @return \Model\VideoQuery
     */
    public function getVideo() {
        return $this->hasOne(\Model\Video::className(), [CVideoPeer::VIDEO_ID => CVideoFilePeer::VIDEO_ID]);
    }
        /**
     * @return \Model\VideoFileQuery
     */
    public function getSourceVideoFile() {
        return $this->hasOne(\Model\VideoFile::className(), [CVideoFilePeer::VIDEO_FILE_ID => CVideoFilePeer::SOURCE_VIDEO_FILE_ID]);
    }
        /**
     * @return \Model\VideoFileQuery
     */
    public function getVideoFiles() {
        return $this->hasMany(\Model\VideoFile::className(), [CVideoFilePeer::SOURCE_VIDEO_FILE_ID => CVideoFilePeer::VIDEO_FILE_ID]);
    }
    
    /**
     * @inheritdoc
     * @return \Model\VideoFileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \Model\VideoFileQuery(get_called_class());
    }
}
