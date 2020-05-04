<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ContactManager;

class HomeController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);

            $errors = $this->secureName($data, $errors);
            $errors = $this->secureInfo($data, $errors);
            $errors = $this->secureMessage($data, $errors);


            if (empty($errors)) {
                $data = [
                    'lastname' => $_POST['lastname'],
                    'firstname' => $_POST['firstname'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'message' => $_POST['message'],
                ];
                header('Location:/home/index');
            }
        }
        return $this->twig->render(
            'Home/index.html.twig',
            [
            'post'=> $_POST,
            'errors'=> $errors,
            ]
        );
    }


    private function secureName($data, $errors)
    {
        //nom
        $maxNameLength = 55;
        if (strlen($data['lastname']) > $maxNameLength) {
            $errors['lastname'] = "La taille de votre nom ne peut pas excéder ".$maxNameLength.' caractères.';
        }

        //prénom
        if (strlen($data['firstname']) > $maxNameLength) {
            $errors['firstname'] = "La taille de votre prénom ne peut pas dépasser ".$maxNameLength.' lettres.';
        }

        return $errors;
    }

    private function secureInfo($data, $errors)
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format de mail invalide";
        }

        return $errors;
    }


    private function secureMessage($data, $errors)
    {
        // message
        if (empty($data['message'])) {
            $errors['message'] = 'Un message est requis';
        }
        return $errors;
    }
}
