<?php

namespace App\Controller;

use App\Model\ContactManager;

class AdminContactController extends AbstractController
{
    public function index()
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectAllContact();

        return $this->twig->render('Admincontact/reception.html.twig', ['contacts' => $contact]);
    }


    public function details(int $id)
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectOneById($id);

        return $this->twig->render('Admincontact/message.html.twig', ['contacts' => $contact]);
    }

    public function delete(int $id)
    {
        $contactManager = new ContactManager();
        $contactManager->delete($id);
        header('Location:/AdminContact/index');
    }
}
