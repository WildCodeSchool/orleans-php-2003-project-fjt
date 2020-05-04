<?php


namespace App\Controller;

use App\Model\RoomManager;

class AdminRoomController extends AbstractController
{
    const ALLOWED_MIME = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
    const MAX_SIZE = 1000000;
    const UPLOAD_DIR = '../public/assets/images/uploads/';

    public function index()
    {
        $roomManager = new RoomManager();
        $rooms = $roomManager->selectRoomByAddress();
        $roomByAddresses = [];
        foreach ($rooms as $room) {
            $roomName = $room['name'];
            $roomByAddresses[$roomName][] = $room;
        }
        return $this->twig->render('AdminRoom/index.html.twig', ['roomByAddresses' => $roomByAddresses]);
    }

    public function addRoom()
    {
        $roomManager = new RoomManager();
        $addresses = $roomManager->selectAddress();
        $addressesId = [];
        foreach ($addresses as $address) {
            $addressesId[] = $address['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $file = $_FILES['picture'] ?? [];

            $errorsData = $this->controlData($data);
            $errorsFilter = $this->controlDataFilter($data);
            [$fileNameNew,$errorsUpload] = $this->controlDataFile($file);
            $errors = array_merge($errorsData, $errorsFilter, $errorsUpload);

            if (empty($errors)) {
                $data['picture'] = $fileNameNew;
                $roomManager->insert($data);
                header('Location: /AdminRoom/index');
            }
        }

        return $this->twig->render('AdminRoom/addRoom.html.twig', [
            'addresses' => $addresses,
            'errors' => $errors ?? [],
            'room' => $data ?? [],
        ]);
    }

    private function controlData(array $data) :array
    {
        if (empty($data['type'])) {
            $errorsData['type'] = 'Le type de logement ne doit pas être vide';
        }
        if (empty($data['guarantee'])) {
            $errorsData['guarantee'] = 'La caution du logement ne doit pas être vide';
        } elseif ($data['guarantee'] <= 0) {
            $errorsData['guarantee'] = 'La caution du logement doit être supérieur à 0 €';
        }
        if (empty($data['address_id'])) {
            $errorsData['address_id'] = 'L\'adresse du logement ne doit pas être vide';
        }
        if (strlen($data['type']) > 255) {
            $errorsData['type'] = 'Le type du logement est trop long';
        }
        if (strlen($data['equipment']) > 100) {
            $errorsData['equipment'] = 'L\'équipement du logement est trop long';
        }
        if (strlen($data['breakfast']) > 45) {
            $errorsData['breakfast'] = 'L\'information sur le tarif du petit déjeuner est trop longue';
        }

        return $errorsData ?? [];
    }

    private function controlDataFilter(array $data) :array
    {
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

    private function controlDataFile(array $file) :array
    {
        $errorsUpload = [];
        $fileNameNew = '';
        if (!empty($file) && $file['error'] == 0) {
            $fileTmp = $file['tmp_name'];
            $fileSize = filesize($fileTmp);
            $mymeType = mime_content_type($fileTmp);
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (!in_array($mymeType, self::ALLOWED_MIME, true)) {
                $errorsUpload['picture'] = "Le fichier n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_MIME) . '.';
            }
            if ($fileSize > self::MAX_SIZE) {
                $errorsUpload['picture'] = "Le fichier doit faire moins de " . (self::MAX_SIZE/1000000) . ' Mo';
            }

            if (empty($errorsUpload)) {
                $fileNameNew = uniqid() . '.' . $fileExtension;
                $fileDestination = self::UPLOAD_DIR . $fileNameNew;
                if (!move_uploaded_file($fileTmp, $fileDestination)) {
                    $errorsUpload['picture'] = "Le fichier n'a pu être téléchargée.";
                }
            }
        } else {
            $errorsUpload['picture'] = "Problème lors de l'import du fichier";
        }

        return [$fileNameNew,$errorsUpload] ?? [];
    }
}
