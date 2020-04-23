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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];


            $data = array_map('trim', $_POST);
            $data = array_map('stripcslashes', $_POST);
            $data = array_map('htmlentities', $_POST);

            $errors = $this->secureData($data);
        }
    }



    private function secureData($data)
    {
        //%req pour champ requis

        //nom

        if (empty($data["lastname"])) {
            $errors['lastreq'] = "Votre nom est requis";
        } elseif (strlen($data['lastname']) > 55) {
            $errors['lenghtlastname'] = "Ce nom est trop long";
        }

        //prénom
        if (empty($data["firstname"])) {
            $errors['firstreq'] = "Votre prénom est requis";
        } elseif (strlen($data['firstname']) > 55) {
            $errors['lenghtfirstname'] = "Ce prénom est trop long";
        }

        //email
        if (empty($data['email'])) {
            $errors['mailreq'] = 'Votre adresse mail est requise';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['mailinvalid'] = "Format de mail invalide";
        }

        //téléphone, non requis mais si input doit correspondre au format
        if (!empty($data['phone'])) {
            if (!preg_match('/^0\d(?:[ .-]?\d{2}){4}$/', $data['phone'])) {
                $errors['phoneformat'] = 'Format de numéro invalide';
            }
        }

        // message
        if (empty($data['message'])) {
            $errors['messagereq'] = 'Un message est requis';
        }
        header('Location: index.php');
        return $this->twig->render('Contact/index.html.twig');
    }
}
