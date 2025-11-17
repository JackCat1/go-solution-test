<?php

namespace tests\fixtures;

use app\models\Book;
use yii\test\ActiveFixture;

class BookFixture extends ActiveFixture
{
    public $modelClass = Book::class;
    public $dataFile = __DIR__ . '/data/book.php';
    public $depends = [
        UserFixture::class,
    ];
}
