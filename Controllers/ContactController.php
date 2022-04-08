<?php
namespace Controllers;

require_once(ROOT_PATH.'Views/ViewBase.php');
require_once(ROOT_PATH.'Controllers/Validators/ContactValidator.php');
require_once(ROOT_PATH.'Models/Database.php');

use Views\ViewBase;
use Controllers\Validators\ContactValidator;
use Models\Database;

class ContactController
{
    public function index()
    {
        ViewBase::render('/Contact/index.php');
    }

    public function confirm()
    {
        // POSTのみ受け付ける
        if (!$_POST) {
            header('Location: /contact/index');
            exit;
        }

        $contactValidator = (new ContactValidator($_POST));
        if ($contactValidator->isValid()) {
            ViewBase::render(
                '/Contact/confirm.php',
                [
                    'name'  => $_POST['name'],
                    'kana'  => $_POST['kana'],
                    'tel'   => $_POST['tel'],
                    'email' => $_POST['email'],
                    'body'  => $_POST['body'],
                ]
            );
            exit;
        }

        ViewBase::render(
            '/Contact/index.php',
            [
                'name'   => $_POST['name'],
                'kana'   => $_POST['kana'],
                'tel'    => $_POST['tel'],
                'email'  => $_POST['email'],
                'body'   => $_POST['body'],
                'errors' => $contactValidator->getErrors(),
            ]
        );
        exit;
    }

    public function complete()
    {
        if (!$_POST) {
            header('Location: /contact/index');
            exit;
        }
        
        if (!$_POST['name']
            && !$_POST['kana']
            && !$_POST['tel']
            && !$_POST['email']
            && !$_POST['body'])
        {
            header('Location: /contact/index');
            exit;
        }
        
        $contactValidator = (new ContactValidator($_POST));
        if ($contactValidator->isValid()) {
            try {
                // DB接続
                $pdo = (new Database())->getPdo();
            
                // sqlを用意
                $sql  = 'INSERT INTO contacts (name, kana, tel, email, body) VALUE (:name, :kana, :tel, :email, :body)';
                
                // sqlをセット
                $stmt = $pdo->prepare($sql);
            
                // 値置き換え
                $stmt->bindValue(':name', $_POST['name'], \PDO::PARAM_STR);
                $stmt->bindValue(':kana', $_POST['kana'], \PDO::PARAM_STR);
                $stmt->bindValue(':tel', $_POST['tel'], \PDO::PARAM_STR);
                $stmt->bindValue(':email', $_POST['email'], \PDO::PARAM_STR);
                $stmt->bindValue(':body', $_POST['body'], \PDO::PARAM_STR);
            
                // sql実行
                // 1箇所にしか書き込みを行わないので、トランザクションは不要
                $stmt->execute();

                // 表示
                ViewBase::render('/Contact/complete.php');
            } catch (\PDOException $e) {
                error_log($e->getMessage());
                var_dump($e->getMessage()); //エラー画面へ遷移させるとかする
            }
        }

        // indexへ戻す
        ViewBase::render(
            '/Contact/index.php',
            [
                'name'  => $_POST['name'],
                'kana'  => $_POST['kana'],
                'tel'   => $_POST['tel'],
                'email' => $_POST['email'],
                'body'  => $_POST['body'],
                'errors' => $contactValidator->getErrors(),
            ]
        );
    }
}

