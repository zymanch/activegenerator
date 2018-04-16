<?php

namespace Model\Base;



/**
 * This is the model class for table "gtf.file_object_type_family".
 *
 * @property string $file_object_type_family_id
 * @property string $name
 * @property string $constant
 * @property string $description
 * @property string $category
 *
 * @property \Model\FileObjectType[] $fileObjectTypes
 */
class CFileObjectTypeFamily extends \ActiveRecord\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gtf.file_object_type_family';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID, CFileObjectTypeFamilyPeer::NAME, CFileObjectTypeFamilyPeer::CONSTANT], 'required'],
            [[CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID], 'integer'],
            [[CFileObjectTypeFamilyPeer::CATEGORY], 'string'],
            [[CFileObjectTypeFamilyPeer::NAME], 'string', 'max' => 30],
            [[CFileObjectTypeFamilyPeer::CONSTANT], 'string', 'max' => 45],
            [[CFileObjectTypeFamilyPeer::DESCRIPTION], 'string', 'max' => 100],
            [[CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID => 'File Object Type Family ID',
            CFileObjectTypeFamilyPeer::NAME => 'Name',
            CFileObjectTypeFamilyPeer::CONSTANT => 'Constant',
            CFileObjectTypeFamilyPeer::DESCRIPTION => 'Description',
            CFileObjectTypeFamilyPeer::CATEGORY => 'Category',
        ];
    }
    /**
     * @return \Model\FileObjectTypeQuery
     */
    public function getFileObjectTypes() {
        return $this->hasMany(\Model\FileObjectType::className(), [CFileObjectTypePeer::FILE_OBJECT_TYPE_FAMILY_ID => CFileObjectTypeFamilyPeer::FILE_OBJECT_TYPE_FAMILY_ID]);
    }
    
    /**
     * @inheritdoc
     * @return \Model\FileObjectTypeFamilyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \Model\FileObjectTypeFamilyQuery(get_called_class());
    }
}
