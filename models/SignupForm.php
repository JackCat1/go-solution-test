<?php

namespace app\models;

use yii\base\Model;

class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            ['email', 'email'],
            ['email', 'string', 'max' => 128],
            [
                'username',
                'unique',
                'targetClass' => User::class,
                'message' => 'Username is already taken.',
            ],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'message' => 'Email is already registered.',
            ],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
