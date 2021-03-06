<?php

namespace App\Controller;

use App\Model\ContactManager;

class AdminContactController extends AbstractController
{
    public function index()
    {
        $contactManager = new ContactManager();
        $contacts = $contactManager->selectAllContacts();

        return $this->twig->render('AdminContact/index.html.twig', ['contacts' => $contacts]);
    }

    public function show(int $id)
    {
        $contactManager = new ContactManager();
        $contact = $contactManager->selectOneById($id);

        return $this->twig->render('AdminContact/show.html.twig', ['contact' => $contact]);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contactManager = new ContactManager();
            $contactManager->delete($id);
        }
        header('Location:/AdminContact/index');
    }
}
