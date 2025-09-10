<?php

namespace app\controllers;

use app\models\Forms\Web\LoginForm;
use app\models\Forms\Web\RegisterForm;
use app\models\Entities\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Register action.
     */
    public function actionRegister()
    {
        // If already logged in, redirect to home
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Sign up the user
            $user = new User();
            $user->name = $model->name;
            $user->email = $model->email;
            $user->setPassword($model->password);
            $user->generateAuthKey();
            $user->generateAccessToken();

            if ($user->save()) {
                // Auto-login the user after successful registration
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('success', 'Registro realizado com sucesso! Bem-vindo!');
                return $this->redirect(['/expense']);
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao criar conta. Tente novamente.');
            }
        }

        $model->password = '';

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action (alias for register - backward compatibility)
     */
    public function actionSignup()
    {
        return $this->actionRegister();
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        // If already logged in, redirect to home
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
