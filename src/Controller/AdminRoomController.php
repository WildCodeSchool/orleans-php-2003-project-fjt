<?php


namespace App\Controller;

use App\Model\RoomManager;

class AdminRoomController extends AbstractController
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
            $errorsFilter = $this->controlDataFilter($data);
            $controlFileData = $this -> controlDataFile($file);
            list($fileNameNew, $errorsUpload) = $controlFileData;
            $errors = array_merge($errorsDataOne, $errorsDataTwo, $errorsFilter, $errorsUpload);

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

    private function controlDataOne($data, $addressesId)
    {
        $errorsDataOne = [];
        if (empty($data['type'])) {
            $errorsDataOne['type'] = 'Le type de logement ne doit pas être vide';
        }
        if (empty($data['guarantee'])) {
            $errorsDataOne['guarantee'] = 'La caution du logement ne doit pas être vide';
        } elseif ($data['guarantee'] <= 0) {
            $errorsDataOne['guarantee'] = 'La caution du logement doit être supérieur à 0 €';
        }
        if (empty($data['address_id'])) {
            $errorsDataOne['address_id'] = 'L\'adresse du logement ne doit pas être vide';
        } elseif (!in_array($data['address_id'], $addressesId, true)) {
             $errorsDataOne['address_id'] = 'L\'adresse du logement n\'est pas une adresse éxistante';
        }
        return $errorsDataOne ?? [];
    }

    private function controlDataTwo($data)
    {
        $errorsDataTwo = [];
        if (!empty($data['type']) && strlen($data['type']) > 255) {
            $errorsDataTwo['type'] = 'Le type du logement est trop long';
        }
        if (!empty($data['equipment']) && strlen($data['equipment']) > 100) {
            $errorsDataTwo['equipment'] = 'L\'équipement du logement est trop long';
        }
        if (!empty($data['breakfast']) && strlen($data['breakfast']) > 45) {
            $errorsDataTwo['breakfast'] = 'L\'information sur le tarif du petit déjeuner est trop longue';
        }
        return $errorsDataTwo ?? [];
    }

    private function controlDataFilter(array $data) :array
    {
        $errorsFilter = [];
        if (!filter_var($data['guarantee'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilter['guarantee'] = 'La valeur du dépôt de garantie n\'est pas autorisé';
        }
        if (!filter_var($data['catering'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilter['catering'] = 'La valeur du crédit restauration n\'est pas autorisé';
        }
        if (!filter_var($data['contribution'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilter['contribution'] = 'La valeur de la cotisation n\'est pas autorisée';
        }
        if (!filter_var($data['equipment_contribution'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilter['equipment_contribution'] = 'La valeur de la cotisation d\'équipement n\'est pas autorisée';
        }
        if (!filter_var($data['address_id'], FILTER_VALIDATE_INT)) {
            $errorsFilter['address_id'] = 'La valeur de l\'adresse n\'est pas autorisée';
        }
        if (!filter_var($data['area'], FILTER_VALIDATE_INT)) {
            $errorsFilter['area'] = 'La valeur de la surface n\'est pas autorisée';
        }
        if ($data['breakfast'] !== 'inclus' && !filter_var($data['breakfast'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilter['breakfast'] = 'L\'information sur le tarif du petit déjeuner doit être 
            un nombre ou \'inclus\'';
        }
        return $errorsFilter ?? [];
    }

    private function controlDataFile(array $file):array
    {
        $errorsUpload = [];
        $fileNameNew = '';
        if (!empty($file) && $file['error'] === 0) {
            $fileTmp = $file['tmp_name'];
            $fileSize = filesize($fileTmp);
            $mimeType = mime_content_type($fileTmp);
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
                $errorsUpload['picture'] = "Le fichier n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_MIME) . '.';
            }
            if ($fileSize > self::MAX_SIZE) {
                $errorsUpload['picture'] = 'Le fichier doit faire moins de ' . (self::MAX_SIZE/1000000) .
                    ' Mo';
            }
            if (empty($errorsUpload)) {
                $fileNameNew = uniqid('', true) . '.' . $fileExtension;
            }
        } else {
            $errorsUpload['picture'] = "Problème lors de l'import du fichier";
        }
        return [$fileNameNew,$errorsUpload] ?? [];
    }
}
