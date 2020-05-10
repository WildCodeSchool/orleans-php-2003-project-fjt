<?php


namespace App\Controller;

use App\Model\AddressManager;

class AdminAddressController extends AbstractController
{
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
    private function controlAddress($data)
    {
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Le nom du logement ne doit pas être vide';
        } elseif (strlen($data['name']) > 255) {
            $errors['name'] = 'Le nom du logement est trop long';
        }
        if (empty($data['address'])) {
            $errors['address'] = 'L\'adresse du logement ne doit pas être vide';
        } elseif (strlen($data['address']) > 255) {
            $errors['address'] = 'L\'adresse du logement est trop longue';
        }

        return $errors ?? [];
    }
}
