<?php

namespace app\commands;

use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use app\models\User;
use yii\console\Controller;

class SeedController extends Controller
{
    public function actionUser(): void
    {
        $user = new User();
        $user->username = 'testuser';
        $user->email = 'test@example.com';
        $user->setPassword('123456');
        $user->generateAuthKey();
        $user->created_at = time();
        $user->updated_at = time();
        if ($user->save()) {
            echo "✅ Пользователь создан\n";
        } else {
            print_r($user->errors);
        }
    }

    public function actionBooks(): void
    {
        $authorsData = $this->getAuthorsData();
        $counterBooks = 0;

        foreach ($authorsData as $authorName => $books) {
            $author = Author::findOne(['full_name' => $authorName]);

            if (!$author) {
                $author = new Author([
                    'full_name' => $authorName,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $author->save(false);
                echo "Создан автор: {$authorName}\n";
            }

            foreach ($books as $bookData) {
                [$title, $year, $desc, $isbn] = $bookData;

                $book = new Book([
                    'title' => $title,
                    'year' => $year,
                    'description' => $desc,
                    'isbn' => $isbn,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);

                if ($book->save(false)) {
                    // создаем связь книга-автор
                    $ba = new BookAuthor([
                        'book_id' => $book->id,
                        'author_id' => $author->id,
                    ]);
                    $ba->save(false);

                    $counterBooks++;
                    echo "Добавлена книга: {$book->title}\n";
                }
            }
        }

         echo "\nВсего добавлено книг: {$counterBooks}\n";
    }

    private function getAuthorsData(): array
    {
        return [
                'Алексей Толстой' => [
                    ['Петр Первый', 1930, 'Исторический роман о становлении русского государства.', '978-5-17-089379-9'],
                    ['Гиперболоид инженера Гарина', 1927, 'Фантастический роман о безумном изобретателе.', '978-5-17-118208-3'],
                ],
                'Фёдор Достоевский' => [
                    ['Преступление и наказание', 1866, 'История Раскольникова и его искупления.', '978-5-389-07414-1'],
                    ['Братья Карамазовы', 1880, 'Философский роман о вере, свободе и морали.', '978-5-389-07010-5'],
                    ['Идиот', 1869, 'Князь Мышкин и столкновение добра с пороками общества.', '978-5-389-11607-0'],
                ],
                'Лев Толстой' => [
                    ['Война и мир', 1869, 'Эпопея о судьбах людей во времена Наполеоновских войн.', '978-5-389-09330-2'],
                    ['Анна Каренина', 1877, 'Трагическая история любви и социальных условностей.', '978-5-17-118207-6'],
                ],
                'Александр Пушкин' => [
                    ['Евгений Онегин', 1833, 'Роман в стихах о судьбе лишнего человека.', '978-5-17-118203-8'],
                    ['Капитанская дочка', 1836, 'Историческая повесть о Пугачёвском восстании.', '978-5-389-12217-0'],
                ],
                'Михаил Булгаков' => [
                    ['Мастер и Маргарита', 1940, 'Мистика, сатира и философия в советской Москве.', '978-5-17-080141-1'],
                    ['Собачье сердце', 1925, 'Сатира на попытки создать нового человека.', '978-5-17-087055-4'],
                ],
        ];
    }
}