<?php

namespace app\controllers;

use app\models\Book;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BookController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'optional' => ['index', 'view'],
        ];

        return $behaviors;
    }

    public function actionIndex(): array
    {
        $query = Book::find()->orderBy(['created_at' => SORT_DESC]);

        $page = max((int) Yii::$app->request->get('page', 1), 1);
        $perPage = (int) Yii::$app->request->get('per-page', 10);
        $perPage = max(1, min($perPage, 50));

        $total = (clone $query)->count();
        $items = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->all();

        return [
            'items' => $items,
            'meta' => [
                'total' => (int) $total,
                'page' => $page,
                'perPage' => $perPage,
                'pageCount' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function actionView(int $id): array
    {
        return $this->findBook($id)->toArray([], ['creator']);
    }

    public function actionCreate(): array
    {
        $book = new Book();
        $book->load(Yii::$app->request->getBodyParams(), '');
        $book->created_by = Yii::$app->user->id;

        if ($book->save()) {
            Yii::$app->response->statusCode = 201;
            return $book->toArray([], ['creator']);
        }

        Yii::$app->response->statusCode = 422;
        return [
            'message' => 'Validation failed',
            'errors' => $book->getErrors(),
        ];
    }

    public function actionUpdate(int $id): array
    {
        $book = $this->findBook($id);
        $this->ensureOwnership($book);

        $book->load(Yii::$app->request->getBodyParams(), '');
        if ($book->save()) {
            return $book->toArray([], ['creator']);
        }

        Yii::$app->response->statusCode = 422;
        return [
            'message' => 'Validation failed',
            'errors' => $book->getErrors(),
        ];
    }

    public function actionDelete(int $id): void
    {
        $book = $this->findBook($id);
        $this->ensureOwnership($book);
        $book->delete();

        Yii::$app->response->statusCode = 204;
    }

    private function findBook(int $id): Book
    {
        $book = Book::findOne($id);
        if (!$book) {
            throw new NotFoundHttpException('Book not found.');
        }

        return $book;
    }

    private function ensureOwnership(Book $book): void
    {
        if ((int) $book->created_by !== (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException('You can modify only your own books.');
        }
    }
}
