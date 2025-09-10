<?php

namespace app\controllers\api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use app\models\Entities\User;

/**
 * Base API Controller with JWT Authentication
 */
class BaseApiController extends Controller
{
    /**
     * JWT Secret Key - In production, use environment variable
     */
    const JWT_SECRET = 'your-secret-key-change-in-production';
    const JWT_ALGORITHM = 'HS256';
    const JWT_EXPIRATION = 3600; // 1 hour

    public function init()
    {
        parent::init();

        // Set response format to JSON
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Enable CORS
        $this->enableCors();
    }

    /**
     * Enable CORS for API access
     */
    protected function enableCors()
    {
        $headers = Yii::$app->response->headers;
        $headers->set('Access-Control-Allow-Origin', '*');
        $headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Handle preflight requests
        if (Yii::$app->request->method === 'OPTIONS') {
            Yii::$app->response->setStatusCode(200);
            Yii::$app->end();
        }
    }

    /**
     * Generate JWT token for user
     */
    protected function generateJwtToken($userId)
    {
        $payload = [
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + self::JWT_EXPIRATION
        ];

        return JWT::encode($payload, self::JWT_SECRET, self::JWT_ALGORITHM);
    }

    /**
     * Verify JWT token and get user
     */
    protected function verifyJwtToken()
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');

        if (!$authHeader) {
            throw new UnauthorizedHttpException('Authorization header missing');
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            throw new UnauthorizedHttpException('Invalid authorization header format');
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(self::JWT_SECRET, self::JWT_ALGORITHM));

            $user = User::findOne($decoded->user_id);
            if (!$user) {
                throw new UnauthorizedHttpException('User not found');
            }

            return $user;
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid token: ' . $e->getMessage());
        }
    }

    /**
     * Standard success response
     */
    protected function successResponse($data = null, $message = 'Success', $statusCode = 200)
    {
        Yii::$app->response->setStatusCode($statusCode);

        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * Standard error response
     */
    protected function errorResponse($message = 'Error', $errors = null, $statusCode = 400)
    {
        Yii::$app->response->setStatusCode($statusCode);

        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    /**
     * Behaviors for API
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Remove authenticator for auth endpoints
        unset($behaviors['authenticator']);

        return $behaviors;
    }
}
