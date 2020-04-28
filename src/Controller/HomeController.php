<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

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
        $maxNameLenght = 55;
        if (empty($data["lastname"])) {
            $errors['lastname'] = "Votre nom est requis";
        } elseif (strlen($data['lastname']) > $maxNameLenght) {
            $errors['lastname'] = "La taille de votre nom ne peut pas dépasser ".$maxNameLenght.' lettres.';
        } elseif (!preg_match('#[^0-9]#', $data['lastname'])) {
            $errors['lastname'] = "Votre nom ne peut contenir que des lettres";
        }

        //prénom
        if (empty($data["firstname"])) {
            $errors['firstname'] = "Votre prénom est requis";
        } elseif (strlen($data['firstname']) > $maxNameLenght) {
            $errors['firstname'] = "La taille de votre prénom ne peut pas dépasser ".$maxNameLenght.' lettres.';
        } elseif (!preg_match('#[^0-9]#', $data['firstname'])) {
            $errors['firstname'] = "Votre prénom ne peut contenir que des lettres";
        }
        return $errors;
    }

    private function secureInfo($data, $errors)
    {
        //email
        if (empty($data['email'])) {
            $errors['email'] = 'Votre adresse mail est requise';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format de mail invalide";
        }

        //téléphone, non requis mais si il est rentré doit correspondre au regex pour les numéros de téléphone français
        if (!empty($data['phone'])) {
            if (!preg_match('/^0\d(?:[ .-]?\d{2}){4}$/', $data['phone'])) {
                $errors['phone'] = 'Format de numéro invalide';
            }
        }
        return $errors;
    }


    private function secureMessage($data, $errors)
    {
        $maxMessageLenght = 300;
        // message
        if (empty($data['message'])) {
            $errors['message'] = 'Un message est requis';
        } elseif (strlen($data['message']) > $maxMessageLenght) {
            $errors['message'] = 'La taille de votre message ne peut pas dépasser '.$maxMessageLenght.' lettres.';
        }
        return $errors;
    }
}
