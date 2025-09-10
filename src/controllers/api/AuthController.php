<?php

namespace app\controllers\api;

use Yii;
use app\models\Forms\Api\LoginForm;

/**
 * API Authentication Controller
 * Handles login for existing users only
 */
class AuthController extends BaseApiController
{
    /**
     * Login endpoint
     * POST /api/auth/login
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $data = Yii::$app->request->getBodyParams();

        if (!$model->load($data, '') || !$model->validate()) {
            return $this->errorResponse('Validation failed', $model->errors, 422);
        }

        if (!$model->login()) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        $user = $model->getUser();
        $token = $this->generateJwtToken($user->id);

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->name,
                'email' => $user->email ?? null
            ]
        ], 'Login successful');
    }

    /**
     * Verify token endpoint
     * GET /api/auth/verify
     */
    public function actionVerify()
    {
        try {
            $user = $this->verifyJwtToken();

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->name,
                    'email' => $user->email ?? null
                ]
            ], 'Token valid');
        } catch (\Exception $e) {
            return $this->errorResponse('Invalid token', null, 401);
        }
    }

    /**
     * Refresh token endpoint
     * POST /api/auth/refresh
     */
    public function actionRefresh()
    {
        try {
            $user = $this->verifyJwtToken();
            $newToken = $this->generateJwtToken($user->id);

            return $this->successResponse([
                'token' => $newToken
            ], 'Token refreshed');
        } catch (\Exception $e) {
            return $this->errorResponse('Invalid token', null, 401);
        }
    }
}
