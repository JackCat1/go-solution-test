<?php

namespace app\controllers;

use app\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'login' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionLogin(): array
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if (!$model->validate()) {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Validation failed',
                'errors' => $model->getErrors(),
            ];
        }

        $user = $model->getUser();
        $token = Yii::$app->jwt->generateToken($user);

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Yii::$app->jwt->getTtl(),
            'user' => $user->toArray(),
        ];
    }
}
