<?php

namespace app\models\Forms;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class SignUpForm extends Model
{
    public $name;
    public $email;
    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['name', 'email', 'password'], 'required', 'message' => 'O campo {attribute} é obrigatório.'],
            // email has to be a valid email address
            ['email', 'email', 'message' => 'Por favor, insira um endereço de e-mail válido.'],
            // email must be unique
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este endereço de e-mail já está em uso.'],
            // password minimum length
            ['password', 'string', 'min' => 6, 'message' => 'A senha deve ter no mínimo 6 caracteres.'],
            // password need to have at least one number
            ['password', 'match', 'pattern' => '/\d/', 'message' => 'A senha deve conter pelo menos um número.'],
            // password need to have at least one uppercase letter
            ['password', 'match', 'pattern' => '/[A-Z]/', 'message' => 'A senha deve conter pelo menos uma letra maiúscula.'],
            // password need to have at least one lowercase letter
            ['password', 'match', 'pattern' => '/[a-z]/', 'message' => 'A senha deve conter pelo menos uma letra minúscula.'],
            // password need to have at least one special character
            ['password', 'match', 'pattern' => '/[\W_]/', 'message' => 'A senha deve conter pelo menos um caractere especial.'],
            // name is validated
            ['name', 'string', 'max' => 255, 'message' => 'O nome deve ter no máximo 255 caracteres.'],
        ];
    }
}
