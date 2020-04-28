<?php


namespace App\Controller;

use App\Model\RoomManager;

class AdminRoomController extends AbstractController
{
    private const ALLOWED_EXT = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
    private const MAX_SIZE = 1000000;
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
    public function add()
    {
        $adminRoomManager = new RoomManager();
        $addresses = $adminRoomManager->selectAddress();
        $dataTmp = [];
        $errors=[];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $file = $_FILES;
            $dataTmp = $this->controlData($data, $dataTmp);
            $dataTmp = $this->controlDataFilter($data, $dataTmp);
            $dataTmp = $this->controlDataFile($file, $dataTmp, $data);
            [$errors, $data] = $dataTmp;
            if (empty($errors)) {
                $adminRoomManager->insert($data);
                header('Location:/AdminRoom/index');
            }
        }
        return $this->twig->render('AdminRoom/add.html.twig', ['addresses' => $addresses , 'errors' => $errors]);
    }
    private function controlData($data, $errors)
    {
        if (empty($data['type'])) {
            $errors['type'] = 'Le type de logement ne doit pas être vide';
        }
        if (empty($data['guarantee'])) {
            $errors['guarantee'] = 'La caution du logement ne doit pas être vide';
        } elseif ($data['guarantee'] <= 0) {
            $errors['guarantee'] = 'La caution du logement doit être supérieur à 0 €';
        }
        if (empty($data['address_id'])) {
            $errors['address_id'] = 'L\'adresse du logement ne doit pas être vide';
        }
        if (strlen($data['type']) > 255) {
            $errors['type'] = 'Le type du logement est trop long';
        }
        if (strlen($data['equipment']) > 100) {
            $errors['equipment'] = 'L\'équipement du logement est trop long';
        }
        if (strlen($data['breakfast']) > 45) {
            $errors['breakfast'] = 'L\'information sur le tarif du petit déjeuner est trop longue';
        }
        return $errors;
    }
    private function controlDataFilter($data, $errors)
    {
        if (!filter_var($data['guarantee'], FILTER_VALIDATE_FLOAT)) {
            $errors['guarantee'] = 'La valeur du dépôt de garantie n\'est pas autorisé';
        }
        if (!filter_var($data['catering'], FILTER_VALIDATE_FLOAT)) {
            $errors['catering'] = 'La valeur du crédit restauration n\'est pas autorisé';
        }
        if (!filter_var($data['contribution'], FILTER_VALIDATE_FLOAT)) {
            $errors['contribution'] = 'La valeur de la cotisation n\'est pas autorisée';
        }
        if (!filter_var($data['equipment_contribution'], FILTER_VALIDATE_FLOAT)) {
            $errors['equipment_contribution'] = 'La valeur de la cotisation d\'équipement n\'est pas autorisée';
        }
        if (!filter_var($data['address_id'], FILTER_VALIDATE_INT)) {
            $errors['address_id'] = 'La valeur de l\'adresse n\'est pas autorisée';
        }
        if (!filter_var($data['area'], FILTER_VALIDATE_INT)) {
            $errors['area'] = 'La valeur de la surface n\'est pas autorisée';
        }
        if ($data['breakfast'] !== 'inclus' && !filter_var($data['breakfast'], FILTER_VALIDATE_FLOAT)) {
            $errors['breakfast'] = 'L\'information sur le tarif du petit déjeuner doit être un nombre ou \'inclus\'';
        }
        return $errors;
    }

    private function controlDataFile($file, $dataTmp, $data): array
    {
        $uploadDir = '../public/assets/uploads/images/';
        $fileNameNew = [];
        if (!empty($file['picture'])) {
            $fileTmp = $file['picture']['tmp_name'];
            $fileSize = filesize($fileTmp);
            $fileError = $file['picture']['error'];
            $fileExt = explode('.', $file['picture']['name']);
            $fileExt = strtolower(end($fileExt));
            if (!in_array($file['picture']['type'], self::ALLOWED_EXT, true)) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] l'extension '{$fileExt}' n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_EXT) . '.';
            }
            if ($fileError !== 0) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] errored with code {$fileError}";
            }
            if ($fileSize > self::MAX_SIZE) {
                $dataTmp['picture'] = "{$file['picture']['name']} doit faire moins de " . (self::MAX_SIZE/1000) .
                    ' Mo';
            }
            $fileNameNew = uniqid('', true) . '.' . $fileExt;
            $fileDestination = $uploadDir . $fileNameNew;
            if (!move_uploaded_file($fileTmp, $fileDestination)) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] n'a pu être téléchargée.";
            }
        }
        $data['picture'] = $fileNameNew;
        if (!empty($dataTmp) && !empty($data['picture'])) {
            $fileName = $uploadDir . $data['picture'];
            unlink($fileName);
        }
        return array ($dataTmp,$data);
    }
}
