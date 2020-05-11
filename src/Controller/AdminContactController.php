<?php

namespace App\Controller;

use App\Model\ContactManager;

class AdminContactController extends AbstractController
{
    public function index()
    {
        $contactManager = new ContactManager();
        $contacts = $contactManager->selectAllContacts();

        return $this->twig->render('Admincontact/index.html.twig', ['contacts' => $contacts]);
    }
}
