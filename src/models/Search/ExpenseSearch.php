<?php

namespace app\models\Search;

use app\models\Entities\Expense;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExpenseSearch extends Model
{
    public $description;
    public $category;
    public $categories; // Array for multiple categories
    public $value;
    public $date;
    public $date_from;
    public $date_to;

    public function rules()
    {
        return [
            [['description'], 'string', 'max' => 255],
            [['category'], 'integer'],
            [['categories'], 'each', 'rule' => ['integer', 'min' => 1]], // Validate each category ID
            [['value'], 'number', 'min' => 0],
            [['date', 'date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
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
                    'date' => SORT_DESC,
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
            ->andFilterWhere(['value' => $this->value]);

        // Filter by multiple categories
        if (!empty($this->categories) && is_array($this->categories)) {
            // Validate each category is a valid integer
            $validCategories = array_filter($this->categories, function ($cat) {
                return is_numeric($cat) && (int)$cat > 0;
            });
            if (!empty($validCategories)) {
                $query->andWhere(['IN', 'category', array_map('intval', $validCategories)]);
            }
        }

        // Filter by period
        if ($this->date_from) {
            $query->andWhere(['>=', 'date', $this->date_from]);
        }
        if ($this->date_to) {
            $query->andWhere(['<=', 'date', $this->date_to]);
        }

        // Filter by specific date (if not using period)
        if ($this->date && !$this->date_from && !$this->date_to) {
            $query->andFilterWhere(['date' => $this->date]);
        }

        return $dataProvider;
    }
}
