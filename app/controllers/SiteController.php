<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use app\models\Subscription;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Book::find()->with('authors'),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => ['year' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSubscription()
    {
        $this->view->title = 'Подписки';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $userId = Yii::$app->user->id ?? 0;
        $contact = Subscription::find()->select('contact')->where(['user_id' => $userId])->scalar();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Author::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => ['full_name' => SORT_ASC],
            ],
        ]);

        // Уже подписанные авторы
        if (!$userId) {
            $subscribedIds = Subscription::find()
                ->select('author_id')
                ->where(['contact' => $contact])
                ->column();
        } else {
            $subscribedIds = Subscription::find()
                ->select('author_id')
                ->where(['user_id' => $userId])
                ->column();
        }

        return $this->render('subscription', [
            'dataProvider' => $dataProvider,
            'subscribedIds' => $subscribedIds,
            'contact' => $contact
        ]);
    }

    public function actionSaveSubscriptions()
    {
        $selectedAuthors = Yii::$app->request->post('subscriptions', []);
        $contact = trim(Yii::$app->request->post('contact', ''));
        $userId = Yii::$app->user->id ?? 0; // 0 - это гость

        // Проверка наличия контакта
        if (empty($contact)) {
            Yii::$app->session->setFlash('error', 'Укажите контакты для рассылки.');
            return $this->redirect(['site/subscription']);
        }

        // Проверка формата телефона
        if (!preg_match('/^(\+7|8)\d{10}$/', $contact)) {
            Yii::$app->session->setFlash('error', 'Некорректный формат телефона. Используйте +7XXXXXXXXXX или 8XXXXXXXXXX.');
            return $this->redirect(['site/subscription']);
        }

        // Проверка — выбраны ли авторы
        if (empty($selectedAuthors)) {
            Yii::$app->session->setFlash('error', 'Выберите хотя бы одного автора для подписки.');
            return $this->redirect(['site/subscription']);
        }

        // Сохраняем
        if (!$userId) {
            Subscription::deleteAll(['contact' => $contact]); // если гость, удалим по контактам
        } else {
            Subscription::deleteAll(['user_id' => $userId]); // иначе по пользователю
        }

        foreach ($selectedAuthors as $authorId) {
            $sub = new Subscription([
                'user_id' => $userId,
                'author_id' => $authorId,
                'contact' => $contact,
                'created_at' => time()
            ]);

            $sub->save();
        }

        Yii::$app->session->setFlash('success', 'Подписки успешно сохранены.');
        return $this->redirect(['site/subscription']);
    }



    public function actionReports()
    {
        $this->view->title = 'Отчет';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('reports');
    }
}
