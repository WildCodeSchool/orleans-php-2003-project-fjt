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

    public function show(int $id)
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectOneById($id);

        return $this->twig->render('Admincontact/show.html.twig', ['contact' => $contact]);
    }
}
