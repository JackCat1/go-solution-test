<?php

use app\models\User;
use tests\fixtures\BookFixture;
use tests\fixtures\UserFixture;

class AuthCest
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

    public function userCanRegister(FunctionalTester $I): void
    {
        $I->sendAjaxRequest('POST', '/users', [
            'username' => 'api_user_' . uniqid(),
            'email' => uniqid() . '@example.com',
            'password' => 'secret123',
        ]);

        $I->seeResponseCodeIs(201);
        $data = $this->decodeResponse($I);
        $I->assertArrayHasKey('id', $data);
        $I->assertArrayHasKey('username', $data);
        $I->assertArrayHasKey('email', $data);
        $I->assertArrayHasKey('created_at', $data);
    }

    public function loginReturnsJwtToken(FunctionalTester $I): void
    {
        $user = User::find()->orderBy('id')->one();

        $I->sendAjaxRequest('POST', '/auth/login', [
            'username' => $user->username,
            'password' => 'secret123',
        ]);

        $I->seeResponseCodeIsSuccessful();
        $data = $this->decodeResponse($I);
        $I->assertArrayHasKey('token', $data);
        $I->assertNotEmpty($data['token']);
        $I->assertEquals('Bearer', $data['token_type']);
        $I->assertArrayHasKey('expires_in', $data);
        $I->assertArrayHasKey('user', $data);
    }

    private function decodeResponse(FunctionalTester $I): array
    {
        $data = json_decode($I->grabPageSource(), true);
        $I->assertIsArray($data);
        return $data;
    }
}
