<?php

namespace app\controllers;

use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'view' => ['GET'],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['create', 'options'],
        ];

        return $behaviors;
    }

    public function actionCreate(): array
    {
        $model = new SignupForm();
        $model->load(Yii::$app->request->getBodyParams(), '');

        $user = $model->signup();
        if ($user === null) {
            Yii::$app->response->statusCode = 422;
            return [
                'message' => 'Validation failed',
                'errors' => $model->getErrors(),
            ];
        }

        Yii::$app->response->statusCode = 201;
        return $user->toArray();
    }

    public function actionView(int $id): array
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user->toArray();
    }
}
