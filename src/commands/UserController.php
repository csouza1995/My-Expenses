<?php

namespace app\commands;

use app\models\Entities\User;
use yii\console\Controller;
use Yii;

/**
 * Command to create test user for API testing
 */
class UserController extends Controller
{
    /**
     * Creates a test user for API testing
     */
    public function actionCreate($email = 'apitest', $password = '123456')
    {
        // Check if user already exists
        if (User::findByEmail($email)) {
            echo "User '$email' already exists!\n";
            return;
        }

        $user = new User();
        $user->name = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateAccessToken();

        if ($user->save()) {
            echo "User created successfully!\n";
            echo "Email: {$user->email}\n";
            echo "Password: $password\n";
            echo "You can now use these credentials with the API.\n";
        } else {
            echo "Failed to create user!\n";
            print_r($user->errors);
        }
    }

    /**
     * Lists all users
     */
    public function actionList()
    {
        $users = User::find()->all();

        if (empty($users)) {
            echo "No users found.\n";
            return;
        }

        echo "Users in the system:\n";
        echo "ID\tUsername\tEmail\n";
        echo "------------------------\n";

        foreach ($users as $user) {
            echo "{$user->id}\t{$user->name}\t{$user->email}\n";
        }
    }

    /**
     * Creates the default test user for testing
     * Usage: php yii user/create-test
     */
    public function actionCreateTest()
    {
        $email = 'tester@example.com';
        $password = 'ABCdef123!@#';

        // Check if user already exists
        $existingUser = User::findByEmail($email);
        if ($existingUser) {
            echo "Test user '$email' already exists!\n";
            echo "Password: $password\n";
            return;
        }

        $user = new User();
        $user->name = 'Test User';
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateAccessToken();

        if ($user->save()) {
            echo "✅ Test user created successfully!\n";
            echo "📧 Email: {$user->email}\n";
            echo "🔑 Password: $password\n";
            echo "🎯 You can now run the API and Acceptance tests.\n";
        } else {
            echo "❌ Failed to create test user!\n";
            echo "Errors:\n";
            foreach ($user->errors as $field => $errors) {
                echo "  $field: " . implode(', ', $errors) . "\n";
            }
        }
    }
}
