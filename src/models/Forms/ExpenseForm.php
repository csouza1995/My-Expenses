<?php

namespace app\models\Forms;

use app\models\Enums\ExpenseCategoriesEnum;
use yii\base\Model;

class ExpenseForm extends Model
{
    public $description;
    public $category;
    public $value;
    public $date;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // description, category, value and date are required
            [['description', 'category', 'value', 'date'], 'required', 'message' => 'O campo {attribute} é obrigatório.'],
            // description max length
            ['description', 'string', 'max' => 255, 'message' => 'A descrição não pode exceder 255 caracteres.'],
            // category must be integer and valid enum value
            ['category', 'integer', 'message' => 'Categoria deve ser um número válido.'],
            ['category', 'in', 'range' => ExpenseCategoriesEnum::getValues(), 'message' => 'Categoria inválida.'],
            // value must be a positive number
            ['value', 'number', 'min' => 0.01, 'message' => 'O valor deve ser um número positivo.'],
            // date must be a valid date in 'Y-m-d' format
            ['date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Por favor, insira uma data válida no formato AAAA-MM-DD.'],
        ];
    }
}
