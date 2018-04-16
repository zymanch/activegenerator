<?php

namespace Model\Base;



/**
 * This is the model class for table "gtf.video".
 *
 * @property string $video_id
 * @property string $upload_id
 * @property string $file_object_type_id
 * @property string $company_id
 * @property string $user_id
 * @property string $content_id
 * @property string $secondary_content_id
 * @property string $storage_id
 * @property string $subpath
 * @property string $url
 * @property string $video_order
 * @property string $created
 * @property string $status
 * @property string $subscription_news_state
 *
 * @property \Model\FileObjectType $fileObjectType
 * @property \Model\VideoFile[] $videoFiles
 */
class CVideo extends \ActiveRecord\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gtf.video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[CVideoPeer::UPLOAD_ID, CVideoPeer::FILE_OBJECT_TYPE_ID, CVideoPeer::COMPANY_ID, CVideoPeer::USER_ID, CVideoPeer::STORAGE_ID, CVideoPeer::SUBPATH, CVideoPeer::CREATED], 'required'],
            [[CVideoPeer::UPLOAD_ID, CVideoPeer::FILE_OBJECT_TYPE_ID, CVideoPeer::COMPANY_ID, CVideoPeer::USER_ID, CVideoPeer::CONTENT_ID, CVideoPeer::SECONDARY_CONTENT_ID, CVideoPeer::STORAGE_ID, CVideoPeer::VIDEO_ORDER], 'integer'],
            [[CVideoPeer::CREATED], 'safe'],
            [[CVideoPeer::STATUS, CVideoPeer::SUBSCRIPTION_NEWS_STATE], 'string'],
            [[CVideoPeer::SUBPATH], 'string', 'max' => 20],
            [[CVideoPeer::URL], 'string', 'max' => 255],
            [[CVideoPeer::FILE_OBJECT_TYPE_ID], 'exist', 'skipOnError' => true, 'targetClass' => CFileObjectType::className(), 'targetAttribute' => [CVideoPeer::FILE_OBJECT_TYPE_ID => CFileObjectTypePeer::FILE_OBJECT_TYPE_ID]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            CVideoPeer::VIDEO_ID => 'Video ID',
            CVideoPeer::UPLOAD_ID => 'Upload ID',
            CVideoPeer::FILE_OBJECT_TYPE_ID => 'File Object Type ID',
            CVideoPeer::COMPANY_ID => 'Company ID',
            CVideoPeer::USER_ID => 'User ID',
            CVideoPeer::CONTENT_ID => 'Content ID',
            CVideoPeer::SECONDARY_CONTENT_ID => 'Secondary Content ID',
            CVideoPeer::STORAGE_ID => 'Storage ID',
            CVideoPeer::SUBPATH => 'Subpath',
            CVideoPeer::URL => 'Url',
            CVideoPeer::VIDEO_ORDER => 'Video Order',
            CVideoPeer::CREATED => 'Created',
            CVideoPeer::STATUS => 'Status',
            CVideoPeer::SUBSCRIPTION_NEWS_STATE => 'Subscription News State',
        ];
    }
    /**
     * @return \Model\FileObjectTypeQuery
     */
    public function getFileObjectType() {
        return $this->hasOne(\Model\FileObjectType::className(), [CFileObjectTypePeer::FILE_OBJECT_TYPE_ID => CVideoPeer::FILE_OBJECT_TYPE_ID]);
    }
        /**
     * @return \Model\VideoFileQuery
     */
    public function getVideoFiles() {
        return $this->hasMany(\Model\VideoFile::className(), [CVideoFilePeer::VIDEO_ID => CVideoPeer::VIDEO_ID]);
    }
    
    /**
     * @inheritdoc
     * @return \Model\VideoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \Model\VideoQuery(get_called_class());
    }
}
