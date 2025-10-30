<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\repositories\{BookRepository, AuthorRepository, ReportRepository, SubscriptionRepository};

class SiteController extends Controller
{
    private BookRepository $bookRepo;
    private AuthorRepository $authorRepo;
    private SubscriptionRepository $subRepo;

    private ReportRepository $reportRepository;

    public function __construct(
        $id,
        $module,
        BookRepository $bookRepo,
        AuthorRepository $authorRepo,
        SubscriptionRepository $subRepo,
        ReportRepository $reportRepository,
        $config = []
    ) {
        $this->bookRepo = $bookRepo;
        $this->authorRepo = $authorRepo;
        $this->subRepo = $subRepo;
        $this->reportRepository = $reportRepository;
        parent::__construct($id, $module, $config);
    }
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
        return $this->render('index', [
            'dataProvider' => $this->bookRepo->getDataProvider(),
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
        $contact = $this->subRepo->getUserContact($userId);
        $dataProvider = $this->authorRepo->getDataProvider();
        $subscribedIds = $this->subRepo->getSubscribedAuthorIds($userId, $contact);

        return $this->render('subscription', compact('dataProvider', 'subscribedIds', 'contact'));
    }

    public function actionSaveSubscriptions()
    {
        $selectedAuthors = Yii::$app->request->post('subscriptions', []);
        $contact = trim(Yii::$app->request->post('contact', ''));
        $userId = Yii::$app->user->id ?? 0;

        try {
            if (!$contact) {
                throw new \DomainException('Укажите контакты для рассылки.');
            }
            if (!$this->subRepo->validateContact($contact)) {
                throw new \DomainException('Некорректный формат телефона.');
            }
            if (empty($selectedAuthors)) {
                throw new \DomainException('Выберите хотя бы одного автора.');
            }

            $this->subRepo->saveSubscriptions($selectedAuthors, $contact, $userId);
            Yii::$app->session->setFlash('success', 'Подписки успешно сохранены.');

        } catch (\DomainException|\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['site/subscription']);
    }


    public function actionReports()
    {
        $this->view->title = 'Отчет — ТОП 10 авторов';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $year = Yii::$app->request->get('year', date('Y'));

        $dataProvider = $this->reportRepository->getTopAuthorsByYear((int)$year);

        return $this->render('reports', [
            'dataProvider' => $dataProvider,
            'year' => $year,
        ]);
    }
}
