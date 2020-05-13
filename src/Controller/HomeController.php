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
     * Display home page without loading
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function indexBis()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);

            $errors = $this->secureName($data, $errors);
            $errors = $this->secureInfo($data, $errors);
            $errors = $this->secureMessage($data, $errors);


            if (empty($errors)) {
                $contactManager = new ContactManager();
                $contactManager->insert($data);
                header('Location:/home/index/' .
                    '?success=Votre message a bien été envoyé, nous vous recontacterons dans les plus brefs délais.');
            }
        }

        return $this->twig->render(
            'Home/indexbis.html.twig',
            [
                'post'=> $_POST,
                'errors'=> $errors,
                'success' => $_GET
            ]
        );
    }

    /**
     * Display home page with loading
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        return $this->twig->render('Home/index.html.twig');
    }

    private function secureName($data, $errors)
    {
        //nom
        $maxNameLength = 55;
        if (empty($data['lastname'])) {
            $errors['lastname'] = 'Votre nom est requis';
        } elseif (strlen($data['lastname']) > $maxNameLength) {
            $errors['lastname'] = "La taille de votre nom ne peut pas excéder ".$maxNameLength.' caractères.';
        }

        //prénom
        if (empty($data['firstname'])) {
            $errors['firstname'] = 'Votre prénom est requis';
        } elseif (strlen($data['firstname']) > $maxNameLength) {
            $errors['firstname'] = "La taille de votre prénom ne peut pas excéder ".$maxNameLength.' caractères.';
        }

        return $errors;
    }

    private function secureInfo($data, $errors)
    {
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Votre adresse doit correspondre au format adressse@valide.com";
            }
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

    public function showLegals()
    {
        return $this->twig->render('Home/details_legals.html.twig');
    }
}
