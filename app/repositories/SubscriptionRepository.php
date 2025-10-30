<?php

namespace app\repositories;

use app\models\Subscription;
use Yii;

class SubscriptionRepository
{
    public function getSubscribedAuthorIds(int $userId, ?string $contact): array
    {
        $query = Subscription::find()->select('author_id');
        if ($userId) {
            $query->where(['user_id' => $userId]);
            return $query->column();
        }

        $query->where(['contact' => $contact]);

        return $query->column();
    }

    public function saveSubscriptions(array $selectedAuthors, string $contact, int $userId = 0): bool
    {
        if (!$contact) {
            throw new \InvalidArgumentException('Контакт обязателен');
        }

        // Очистка старых подписок
        $userId
            ? Subscription::deleteAll(['user_id' => $userId])
            : Subscription::deleteAll(['contact' => $contact]);

        foreach ($selectedAuthors as $authorId) {
            $sub = new Subscription([
                'user_id' => $userId,
                'author_id' => $authorId,
                'contact' => $contact,
                'created_at' => time(),
            ]);
            $sub->save();
        }

        return true;
    }

    public function getUserContact(int $userId): ?string
    {
        return Subscription::find()->select('contact')->where(['user_id' => $userId])->scalar();
    }

    public function validateContact(string $contact): bool
    {
        return preg_match('/^(\+7|8)\d{10}$/', $contact);
    }
}