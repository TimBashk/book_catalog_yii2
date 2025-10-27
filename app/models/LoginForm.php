<?php

namespace app\models;


use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
        ];
    }

    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user && $user->validatePassword($this->password)) {
                return Yii::$app->user->login($user);
            }
            $this->addError('password', 'Неверное имя пользователя или пароль.');
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}