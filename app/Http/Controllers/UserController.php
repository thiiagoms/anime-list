<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Interfaces\UserInterface;
use Core\Infra\EntityManagerFactory;
use Exception;

class UserController extends Controller implements UserInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $_entityManager;

    public function __construct()
    {
        $this->_entityManager = (new EntityManagerFactory())->getEntityManager();
    }

    public function auth(): void
    {
        $userRepo = $this->_entityManager->getRepository(User::class);

        try {

            $email = filter_input(
                INPUT_POST,
                'userEmail',
                FILTER_VALIDATE_EMAIL
            );

            if (is_null($email) || $email === false) {
                $_SESSION['alertClass'] = 'danger';
                $_SESSION['message'] = "E-mail is invalid";
                header('Location: /user/login');
                exit();
            }

            $password = filter_input(
                INPUT_POST,
                'userPassword',
                FILTER_SANITIZE_STRING
            );

            /** @var User $user */
            $user =  $userRepo->findOneBy(['_email' => $email]);

            if (is_null($user) || !$user->checkPassword($password)) {
                $_SESSION['alertClass'] = 'danger';
                $_SESSION['message'] = "E-mail or password are incorrect!";
                header('Location: /user/login');
                exit();
            }

            $_SESSION['logged'] = true;

            header('Location: /animes/list-animes');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function login(): void
    {
        echo $this->render('user/form', [
            'title' => 'Login',
        ]);
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /user/login');
    }
}
