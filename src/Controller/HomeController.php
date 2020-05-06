<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ContactManager;
use App\Model\ItemManager;

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
                $contactManager = new ContactManager();
                $contactManager->insert($data);
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
        if (empty($data['lastname'])) {
            $errors['lastname'] = 'Votre nom est requis';
        } elseif (strlen($data['lastname']) > $maxNameLength) {
            $errors['lastname'] = "La taille de votre nom ne peut pas excéder ".$maxNameLength.' caractères.';
        }

        //prénom
        if (empty($data['firstname'])) {
            $errors['firstname'] = 'Votre prénom est requis';
        } elseif (strlen($data['firstname']) > $maxNameLength) {
            $errors['firstname'] = "La taille de votre prénom ne peut pas dépasser ".$maxNameLength.' lettres.';
        }

        return $errors;
    }

    private function secureInfo($data, $errors)
    {
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Format de mail invalide";
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

    public function show()
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectAllContact();

        return $this->twig->render('Admincontact/reception.html.twig', ['contacts' => $contact]);
    }

    public function message(int $id)
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectOneById($id);

        return $this->twig->render('Admincontact/message.html.twig', ['contacts' => $contact]);
    }

    public function delete(int $id)
    {
        $contactManager = new ContactManager();
        $contactManager->delete($id);
        header('Location:/home/show');
    }
}
