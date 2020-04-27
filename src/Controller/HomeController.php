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
        $nameErrors = [];
        $infoErrors = [];
        $messageErrors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);

            $nameErrors = $this->secureName($data);
            $infoErrors = $this->secureInfo($data);
            $messageErrors = $this->secureMessage($data);


            if (empty($nameErrors) || (empty($infoErrors)) || (empty($messageErrors))) {
                header('Location:/home/index');
            }
        }
        return $this->twig->render(
            'Home/index.html.twig',
            [
            'post'=> $_POST,
            'nameErrors' => $nameErrors,
            'infoErrors' => $infoErrors,
            'messageErrors' => $messageErrors
            ]
        );
    }

  
    private function secureName($data) : array
    {
        $nameErrors = [];
        //nom
        if (empty($data["lastname"])) {
            $nameErrors['lastname'] = "Votre nom est requis";
        } elseif (strlen($data['lastname']) > 55) {
            $nameErrors['lastname'] = "Ce nom est trop long";
        } elseif (!preg_match('#[^0-9]#', $data['lastname'])) {
            $nameErrors['lastname'] = "Votre nom ne peut contenir que des lettres";
        }

        //prénom
        if (empty($data["firstname"])) {
            $nameErrors['firstname'] = "Votre prénom est requis";
        } elseif (strlen($data['firstname']) > 55) {
            $nameErrors['firstname'] = "Ce prénom est trop long";
        } elseif (!preg_match('#[^0-9]#', $data['firstname'])) {
            $nameErrors['firstname'] = "Votre prénom ne peut contenir que des lettres";
        }
        return $nameErrors;
    }

    private function secureInfo($data)
    {
        $infoErrors = [];
        //email
        if (empty($data['email'])) {
            $infoErrors['email'] = 'Votre adresse mail est requise';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $infoErrors['email'] = "Format de mail invalide";
        }

        //téléphone, non requis mais si il est rentré doit correspondre au regex pour les numéros de téléphone français
        if (!empty($data['phone'])) {
            if (!preg_match('/^0\d(?:[ .-]?\d{2}){4}$/', $data['phone'])) {
                $infoErrors['phone'] = 'Format de numéro invalide';
            }
        }
        return $infoErrors;
    }


    private function secureMessage($data)
    {
        $messageErrors = [];
        // message
        if (empty($data['message'])) {
            $messageErrors['message'] = 'Un message est requis';
        } elseif (strlen($data['message']) > 300) {
            $messageErrors['message'] = "Ce message est trop long";
        }
        return $messageErrors;
    }
}
