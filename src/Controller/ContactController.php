<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\ContactManager;
use App\Model\ItemManager;

/**
 * Class ContactController
 *
 */
class ContactController extends AbstractController
{
    public function index()
    {
        return $this->twig->render('Contact/index.html.twig');
    }


    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendContact()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $data = array_map('stripcslashes', $_POST);
            $data = array_map('htmlentities', $_POST);

            $errors = $this->secureData($data, $errors);
        }
        return $this->twig->render('Contact/index.html.twig', ['errors'=> $errors]);
    }

    private function secureData($data, $errors)
    {
        //%req pour champ requis
        //nom

        if (empty($data["lastname"])) {
            $errors['lastname'] = "Votre nom est requis";
        } elseif (strlen($data['lastname']) > 55) {
            $errors['lastname'] = "Ce nom est trop long";
        }

        //prénom
        if (empty($data["firstname"])) {
            $errors['firstname'] = "Votre prénom est requis";
        } elseif (strlen($data['firstname']) > 55) {
            $errors['firstname'] = "Ce prénom est trop long";
        }

        //email
        if (empty($data['email'])) {
            $errors['email'] = 'Votre adresse mail est requise';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format de mail invalide";
        }

        //téléphone non requis mais si input doit correspondre au format
        if (!preg_match('/^0\d(?:[ .-]?\d{2}){4}$/', $data['phone'])) {
                $errors['phoneformat'] = 'Format de numéro invalide';
        }
        
        // message
        if (empty($data['message'])) {
            $errors['message'] = 'Un message est requis';
        }
        header('Location: Contact/index.php');
        return $errors;
    }
}
