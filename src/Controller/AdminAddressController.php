<?php


namespace App\Controller;

use App\Model\AddressManager;

class AdminAddressController extends AbstractController
{
    public function deleteAddress()
    {
        $addressManager = new AddressManager();
        $id = trim($_POST['id']);
        $addressManager->deleteAddress($id);
        header('Location: /AdminRoom/index');
    }
}
