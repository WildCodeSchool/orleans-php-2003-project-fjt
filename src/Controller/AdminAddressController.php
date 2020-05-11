<?php


namespace App\Controller;

use App\Model\AddressManager;

class AdminAddressController extends AbstractController
{
    const MAX_LENGTH = 255;
    public function editAddress(int $id)
    {
        $addressManager = new AddressManager();
        $address = $addressManager -> selectOneById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $errors = $this->controlAddress($data);
            if (empty($errors)) {
                $addressManager -> update($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this -> twig -> render('AdminAddress/editAddress.html.twig', [
            'address' => $address,
            'errors' => $errors ?? [],
            'data' => $data ?? []
        ]);
    }

    public function addAddress()
    {
        $addressManager = new AddressManager();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $errors = $this->controlAddress($data);
            if (empty($errors)) {
                $addressManager->insertAddress($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this->twig->render('AdminAddress/addAddress.html.twig', [
            'data'=> $data ?? [],
            'errors'=> $errors ?? []]);
    }
      
    private function controlAddress($data)
    {

        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom du logement ne doit pas être vide';
        } elseif (strlen($data['name']) > self::MAX_LENGTH) {
            $errors['name'] = 'Le nom du logement doit fait moins de ' . self::MAX_LENGTH . ' caractères.';
        }
        if (empty($data['address'])) {
            $errors['address'] = 'L\'adresse du logement ne doit pas être vide';
        } elseif (strlen($data['address']) > self::MAX_LENGTH) {
            $errors['address'] = 'L\'adresse du logement doit fait moins de ' . self::MAX_LENGTH . ' caractères.';

        }

        return $errors ?? [];
    }
}
