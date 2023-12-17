<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property float|null $rate
 */
class Currency extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'currency';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['rate'], 'number'],
      [['name', 'code'], 'string', 'max' => 255],
      [['code'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Name',
      'code' => 'Code',
      'rate' => 'Rate',
    ];
  }

  public function save($runValidation = true, $attributeNames = null)
  {
    if ($this->getIsNewRecord()) {
      return $this->insert($runValidation, $attributeNames);
    } else {
      return $this->update($runValidation, $attributeNames) !== false;
    }
  }
}
