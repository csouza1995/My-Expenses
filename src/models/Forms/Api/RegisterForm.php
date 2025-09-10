<?php

namespace app\models\Forms\Api;

use app\models\Entities\User;
use Yii;
use yii\base\Model;

/**
 * RegisterForm for API user registration
 */
class RegisterForm extends Model
{
    public $name;
    public $email;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
            ['password', 'string', 'min' => 6],
            ['name', 'string', 'max' => 255],
        ];
    }

    /**
     * Creates a new user
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateAccessToken();

        if ($user->save()) {
            return $user;
        }

        return null;
    }
}
