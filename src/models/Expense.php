<?php

namespace app\models;

use app\models\Enums\ExpenseCategoriesEnum;
use Yii;

class Expense extends \yii\db\ActiveRecord
{
    use Traits\UseTimestamps;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expenses';
    }

    public function attributes()
    {
        return [
            'id',
            'user_id',
            'description',
            'category',
            'value',
            'date',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'category', 'value', 'date', 'user_id'], 'required'],
            [['value'], 'number', 'min' => 0.01],
            [['user_id'], 'integer'],
            [['category'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['date', 'created_at', 'updated_at'], 'safe'],
            [['category'], 'in', 'range' => ExpenseCategoriesEnum::getValues()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Usuário',
            'description' => 'Descrição',
            'category' => 'Categoria',
            'value' => 'Valor',
            'date' => 'Data',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getCategoryName()
    {
        return ExpenseCategoriesEnum::getLabel($this->category);
    }

    public function getDateFormatted()
    {
        return Yii::$app->formatter->asDate($this->date, 'php:d/m/Y');
    }

    public function getCreatedAtFormatted()
    {
        return Yii::$app->formatter->asDatetime($this->created_at, 'php:d/m/Y H:i');
    }

    public function getUpdatedAtFormatted()
    {
        return Yii::$app->formatter->asDatetime($this->updated_at, 'php:d/m/Y H:i');
    }
}
