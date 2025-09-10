<?php

namespace app\models\Forms\Api;

use app\models\Enums\ExpenseCategoriesEnum;
use yii\base\Model;

class ExpenseForm extends Model
{
    public $description;
    public $category;
    public $value;
    public $amount; // Alias for value (API compatibility)
    public $date;
    public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // description, category, value/amount and date are required
            [['description', 'category', 'date'], 'required', 'message' => 'O campo {attribute} é obrigatório.'],
            // Either value or amount must be provided
            ['value', 'required', 'when' => function ($model) {
                return empty($model->amount);
            }, 'message' => 'O campo Value é obrigatório.'],
            ['amount', 'required', 'when' => function ($model) {
                return empty($model->value);
            }, 'message' => 'O campo Amount é obrigatório.'],
            // description max length
            ['description', 'string', 'max' => 255, 'message' => 'A descrição não pode exceder 255 caracteres.'],
            // category must be integer and valid enum value
            ['category', 'integer', 'message' => 'Categoria deve ser um número válido.'],
            ['category', 'in', 'range' => ExpenseCategoriesEnum::getValues(), 'message' => 'Categoria inválida.'],
            // value/amount must be a positive number
            ['value', 'number', 'min' => 0.01, 'message' => 'O valor deve ser um número positivo.'],
            ['amount', 'number', 'min' => 0.01, 'message' => 'O valor deve ser um número positivo.'],
            // date must be a valid date in 'Y-m-d' format
            ['date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Por favor, insira uma data válida no formato AAAA-MM-DD.'],
        ];
    }

    /**
     * Process amount field for API compatibility
     */
    public function beforeValidate()
    {
        // If amount is provided but value is not, copy amount to value
        if (!empty($this->amount) && empty($this->value)) {
            $this->value = $this->amount;
        }
        return parent::beforeValidate();
    }
}
