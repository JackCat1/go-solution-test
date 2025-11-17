<?php

use app\models\User;
use tests\fixtures\BookFixture;
use tests\fixtures\UserFixture;

class BookCest
{
    public function _before(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'users' => [
                'class' => UserFixture::class,
            ],
            'books' => [
                'class' => BookFixture::class,
            ],
        ]);
    }

    public function listBooksWithPagination(FunctionalTester $I): void
    {
        $I->sendAjaxRequest('GET', '/books', ['page' => 1, 'per-page' => 5]);

        $I->seeResponseCodeIsSuccessful();
        $data = $this->decodeResponse($I);
        $I->assertArrayHasKey('items', $data);
        $I->assertArrayHasKey('meta', $data);
        $I->assertCount(5, $data['items']);
    }

    public function createUpdateDeleteFlow(FunctionalTester $I): void
    {
        $token = $this->obtainToken($I);

        // create
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendAjaxRequest('POST', '/books', [
            'title' => 'Test Driven Development',
            'author' => 'Kent Beck',
            'year' => 2002,
            'description' => 'Sample book created in test',
        ]);
        $I->seeResponseCodeIs(201);
        $book = $this->decodeResponse($I);
        $bookId = $book['id'];

        // update
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendAjaxRequest('PUT', "/books/{$bookId}", [
            'title' => 'TDD by Example',
            'author' => 'Kent Beck',
            'year' => 2003,
        ]);
        $I->seeResponseCodeIsSuccessful();
        $updated = $this->decodeResponse($I);
        $I->assertEquals('TDD by Example', $updated['title']);
        $I->assertEquals(2003, $updated['year']);

        // delete
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);
        $I->sendAjaxRequest('DELETE', "/books/{$bookId}");
        $I->seeResponseCodeIs(204);
    }

    public function cannotCreateBookWithoutAuth(FunctionalTester $I): void
    {
        $I->sendAjaxRequest('POST', '/books', [
            'title' => 'Unauthorized',
            'author' => 'Guest',
        ]);
        $I->seeResponseCodeIs(401);
    }

    private function obtainToken(FunctionalTester $I): string
    {
        $user = User::find()->orderBy('id')->one();
        $I->sendAjaxRequest('POST', '/auth/login', [
            'username' => $user->username,
            'password' => 'secret123',
        ]);
        $I->seeResponseCodeIsSuccessful();
        $data = $this->decodeResponse($I);
        return $data['token'];
    }

    private function decodeResponse(FunctionalTester $I): array
    {
        $data = json_decode($I->grabPageSource(), true);
        $I->assertIsArray($data);
        return $data;
    }
}
