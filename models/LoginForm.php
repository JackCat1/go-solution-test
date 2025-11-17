<?php

namespace app\models;

use yii\base\Model;

class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'filter', 'filter' => 'trim'],
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params = []): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
