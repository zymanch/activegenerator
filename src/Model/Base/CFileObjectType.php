<?php

namespace Model\Base;



/**
 * This is the model class for table "gtf.file_object_type".
 *
 * @property string $file_object_type_id
 * @property string $content_type_id
 * @property string $secondary_content_type_id
 * @property string $media_type
 * @property string $file_object_type_family_id
 * @property string $name
 * @property string $constant
 * @property string $description
 * @property string $options
 * @property string $share_files
 * @property string $wm_embedding
 * @property integer $fetch_video_duration_priority
 *
 * @property \Model\FileObjectTypeFamily $fileObjectTypeFamily
 * @property \Model\Video[] $videos
 */
class CFileObjectType extends \ActiveGenerator\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gtf.file_object_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[CFileObjectTypePeer::CONTENT_TYPE_ID, CFileObjectTypePeer::SECONDARY_CONTENT_TYPE_ID, CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID, CFileObjectTypePeer::FETCH_VIDEO_DURATION_PRIORITY], 'integer'],
            [[CFileObjectTypePeer::MEDIA_TYPE, CFileObjectTypePeer::NAME, CFileObjectTypePeer::CONSTANT, CFileObjectTypePeer::DESCRIPTION], 'required'],
            [[CFileObjectTypePeer::MEDIA_TYPE, CFileObjectTypePeer::SHARE_FILES, CFileObjectTypePeer::WM_EMBEDDING], 'string'],
            [[CFileObjectTypePeer::NAME], 'string', 'max' => 30],
            [[CFileObjectTypePeer::CONSTANT], 'string', 'max' => 255],
            [[CFileObjectTypePeer::DESCRIPTION], 'string', 'max' => 100],
            [[CFileObjectTypePeer::OPTIONS], 'string', 'max' => 4096],
            [[CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID], 'exist', 'skipOnError' => true, 'targetClass' => CFileObjectTypeFamily::className(), 'targetAttribute' => [CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID => CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            CFileObjectTypePeer::FILE_OBJECT_TYPE_ID => 'File Object Type ID',
            CFileObjectTypePeer::CONTENT_TYPE_ID => 'Content Type ID',
            CFileObjectTypePeer::SECONDARY_CONTENT_TYPE_ID => 'Secondary Content Type ID',
            CFileObjectTypePeer::MEDIA_TYPE => 'Media Type',
            CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID => 'File Object Type Family ID',
            CFileObjectTypePeer::NAME => 'Name',
            CFileObjectTypePeer::CONSTANT => 'Constant',
            CFileObjectTypePeer::DESCRIPTION => 'Description',
            CFileObjectTypePeer::OPTIONS => 'Options',
            CFileObjectTypePeer::SHARE_FILES => 'Share Files',
            CFileObjectTypePeer::WM_EMBEDDING => 'Wm Embedding',
            CFileObjectTypePeer::FETCH_VIDEO_DURATION_PRIORITY => 'Fetch Video Duration Priority',
        ];
    }
    /**
     * @return \Model\FileObjectTypeFamilyQuery
     */
    public function getFileObjectTypeFamily() {
        return $this->hasOne(\Model\FileObjectTypeFamily::className(), [CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID => CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID]);
    }
        /**
     * @return \Model\VideoQuery
     */
    public function getVideos() {
        return $this->hasMany(\Model\Video::className(), [CVideoPeer::FILE_OBJECT_TYPE_ID => CFileObjectTypePeer::FILE_OBJECT_TYPE_ID]);
    }
    
    /**
     * @inheritdoc
     * @return \Model\FileObjectTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \Model\FileObjectTypeQuery(get_called_class());
    }
}
