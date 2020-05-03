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
        $addressesId = [];
        foreach ($addresses as $address) {
            $addressesId[] = $address['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
            $data = array_map('trim', $_POST);
            $file = $_FILES;
            $errorsData = $this->controlData($data);
            $errorsFilter = $this->controlDataFilter($data);
            if (empty($errorsData) && (empty($errorsFilter))) {
                [$fileNameNew,$errorsUpload] = $this -> controlDataFile($file);
                list($fileNameNew,$errorsUpload) = [$fileNameNew,$errorsUpload];
                $errors = array_merge($errorsData, $errorsFilter, $errorsUpload);
                if (!empty($fileNameNew)) {
                    $data['picture'] = $fileNameNew;
                    $adminRoomManager->insert($data);
                    header('Location:/AdminRoom/index');
                }
            } else {
                $errors = array_merge($errorsData, $errorsFilter);
            }
        }
        return $this->twig->render('AdminRoom/addRoom.html.twig', ['addresses' => $addresses ,
            'errors' => $errors ?? []]);
    }
    private function controlData($data)
    {
        $errorsData = [];
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
    private function controlDataFilter($data)
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

    private function controlDataFile($file)
    {

        $errorsUpload = [];
        $uploadDir = '../public/assets/images/';
        $fileNameNew = [];
        if (!empty($file['picture'])) {
            $fileTmp = $file['picture']['tmp_name'];
            $fileSize = filesize($fileTmp);
            $fileError = $file['picture']['error'];
            $ext = mime_content_type($file['picture']['tmp_name']);
            $fileExt = explode('.', $file['picture']['name']);
            $fileExt = strtolower(end($fileExt));
            if (!in_array($ext, self::ALLOWED_EXT, true)) {
                $errorsUpload['picture'] = "[{$file['picture']['name']}] l'extension '{$ext}' n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_EXT) . '.';
            }
            if ($fileError !== 0) {
                $errorsUpload['picture'] = "[{$file['picture']['name']}] erreur avec le code {$fileError}";
            }
            if ($fileSize > self::MAX_SIZE) {
                $errorsUpload['picture'] = "{$file['picture']['name']} doit faire moins de " . (self::MAX_SIZE/1000) .
                    ' Mo';
            }
            if (empty($errorsUpload)) {
                $fileNameNew = uniqid('', true) . '.' . $fileExt;
                $fileDestination = $uploadDir . $fileNameNew;
                if (!move_uploaded_file($fileTmp, $fileDestination)) {
                    $errorsUpload['picture'] = "[{$file['picture']['name']}] n'a pu être téléchargée.";
                }
            }
        }
        return [$fileNameNew,$errorsUpload] ?? [];
    }
}
