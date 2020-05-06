<?php


namespace App\Controller;

use App\Model\RoomManager;

class AdminRoomController extends AbstractAdminController
{
    public const ALLOWED_MIME = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
    public const MAX_SIZE = 1000000;
    public const UPLOAD_DIR = '../public/assets/images/uploads/';

    public function index()
    {
        $roomManager = new RoomManager();
        $rooms = $roomManager->selectRoomByAddress();
        $roomByAddresses = [];
        $addresses = $roomManager->selectAddress();
        foreach ($rooms as $room) {
            $roomName = $room['name'];
            $roomByAddresses[$roomName][] = $room;
        }

        return $this->twig->render('AdminRoom/index.html.twig', ['roomByAddresses' => $roomByAddresses,
            'addresses'=> $addresses]);
    }

    public function addAddress()
    {
        $adminRoomManager = new RoomManager();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $errors = $this->controlAddress($data);
            if (empty($errors)) {
                $adminRoomManager->insertAddress($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this->twig->render('AdminRoom/addAddress.html.twig', [
            'data'=> $data ?? [],
            'errors'=> $errors ?? []]);
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
  
    public function addRoom()
    {
        $roomManager = new RoomManager();
        $addresses = $roomManager->selectAddress();
        $addressesId = [];
        foreach ($addresses as $address) {
            $addressesId[] = $address['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
            $data = array_map('trim', $_POST);
            $file = $_FILES['picture'] ?? [];
            $errorsDataOne = $this->controlDataOne($data, $addressesId);
            $errorsDataTwo = $this->controlDataTwo($data);
            $errorsFilterOne = $this->controlDataFilterOne($data);
            $errorsFilterTwo = $this->controlDataFilterTwo($data);
            $controlFileData = $this -> controlDataFile($file);
            list($fileNameNew, $errorsUpload) = $controlFileData;
            $errors = array_merge($errorsDataOne, $errorsDataTwo, $errorsFilterOne, $errorsFilterTwo, $errorsUpload);

            if (empty($errors)) {
                $data['picture'] = $fileNameNew;
                $fileDestination = self::UPLOAD_DIR . $fileNameNew;
                move_uploaded_file($fileNameNew, $fileDestination);
                $roomManager->insert($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this->twig->render('AdminRoom/addRoom.html.twig', [
            'addresses' => $addresses ,
            'errors' => $errors ?? [] ,
            'room' => $data ?? []
        ]);
    }
    public function editRoom(int $id)
    {
        $roomManager = new RoomManager();
        $room = $roomManager->selectOneById($id);
        $addresses = $roomManager->selectAddress();
        $addressesId = [];
        foreach ($addresses as $address) {
            $addressesId[] = $address['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $data['id'] = $id;
            $file = $_FILES['picture'] ?? [];
            $errorsDataOne = $this->controlDataOne($data, $addressesId);
            $errorsDataTwo = $this->controlDataTwo($data);
            $errorsFilterOne = $this->controlDataFilterOne($data);
            $errorsFilterTwo = $this->controlDataFilterTwo($data);
            $controlFileData = $this -> controlDataFile($file);
            list($fileNameNew, $errorsUpload) = $controlFileData;
            $errors = array_merge($errorsDataOne, $errorsDataTwo, $errorsFilterOne, $errorsFilterTwo, $errorsUpload);
            if (empty($errors)) {
                $data['picture'] = $fileNameNew;
                $fileDestination = self::UPLOAD_DIR . $fileNameNew;
                move_uploaded_file($fileNameNew, $fileDestination);
                $roomManager->update($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this->twig->render('AdminRoom/editRoom.html.twig', [
            'addresses' => $addresses ,
            'errors' => $errors ?? [] ,
            'room' => $room ?? [],
            'data' => $data ?? []
        ]);
    }
}
