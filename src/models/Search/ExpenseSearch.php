<?php

namespace app\models\Search;

use app\models\Expense;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExpenseSearch extends Model
{
    public $description;
    public $category;
    public $value;
    public $date;

    public function rules()
    {
        return [
            [['description', 'category'], 'safe'],
            [['value'], 'number'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params)
    {
        $query = Expense::find()->where(['user_id' => Yii::$app->user->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // If validation fails, return the data provider without any filtering
            return $dataProvider;
        }

        // Apply filtering conditions
        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['category_id' => $this->category])
            ->andFilterWhere(['value' => $this->value])
            ->andFilterWhere(['date' => $this->date]);

        return $dataProvider;
    }
}
