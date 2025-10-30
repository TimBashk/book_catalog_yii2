<?php

namespace app\controllers;

use app\repositories\BookRepository;
use yii\web\Controller;

use Yii;
use app\models\Book;
use yii\filters\AccessControl;

class BookController extends Controller
{
    private BookRepository $repo;

    public function __construct($id, $module, BookRepository $repo, $config = [])
    {
        $this->repo = $repo;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => $this->repo->getDataProvider(),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repo->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) && $this->repo->create($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->repo->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $this->repo->update($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->repo->delete($id);
        return $this->redirect(['index']);
    }
}