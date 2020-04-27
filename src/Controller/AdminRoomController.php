<?php


namespace App\Controller;

use App\Model\AdminRoomManager;

class AdminRoomController extends AbstractController
{
    public function index()
    {
        $adminRoomManager = new AdminRoomManager();
        $rooms = $adminRoomManager->selectRoomByAddress();
        $roomByAddresses = [];
        foreach ($rooms as $room) {
            $roomName = $room['name'];
            $roomByAddresses[$roomName][] = $room;
        }
        return $this->twig->render('AdminRoom/index.html.twig', ['roomByAddresses' => $roomByAddresses]);
    }
    public function add()
    {
        $uploadDir = '../public/assets/images/';
        chmod('../public/assets/images/', 0755);
        $adminRoomManager = new AdminRoomManager();
        $addresses = $adminRoomManager->selectAddress();
        $dataTmp = [];
        $errors=[];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $file = $_FILES;
            if (!empty($_POST)) {
                $dataTmp = $this->controlData($data, $dataTmp);
                $dataTmp = $this->controlDataFilter($data, $dataTmp);
                $dataTmp = $this->controlDataFile($file, $dataTmp, $data);
                list($errors, $data) = $dataTmp;
                if (!empty($errors) && !empty($data['picture'])) {
                    $fileName = $uploadDir . $data['picture'];
                    unlink($fileName);
                }
                if (empty($errors)) {
                    $adminRoomManager->insert($data);
                    header('Location:/AdminRoom/index');
                }
            }
        }
        return $this->twig->render('AdminRoom/add.html.twig', ['addresses' => $addresses , 'errors' => $errors]);
    }
    private function controlData($data, $dataTmp)
    {
        if (empty($data['type'])) {
            $dataTmp['type'] = 'Le type de logement ne doit pas être vide';
        }
        if (empty($data['guarantee'])) {
            $dataTmp['guarantee'] = 'La caution du logement ne doit pas être vide';
        }
        if (empty($data['address_id'])) {
            $dataTmp['address_id'] = 'L\'adresse du logement ne doit pas être vide';
        }
        if (strlen($data['type']) > 255) {
            $dataTmp['type'] = 'Le type du logement est trop long';
        }
        if (strlen($data['equipment']) > 100) {
            $dataTmp['equipment'] = 'L\'équipement du logement est trop long';
        }
        if (strlen($data['breakfast']) > 45) {
            $dataTmp['breakfast'] = 'L\'information sur le tarif du petit déjeuner est trop longue';
        }
        return $dataTmp;
    }
    private function controlDataFilter($data, $dataTmp)
    {
        if (!filter_var($data['guarantee'], FILTER_VALIDATE_FLOAT)) {
            $dataTmp['guarantee'] = 'La valeur du dépôt de garantie n\'est pas autorisé';
        }
        if (!filter_var($data['restoration'], FILTER_VALIDATE_FLOAT)) {
            $dataTmp['restoration'] = 'La valeur du crédit restauration n\'est pas autorisé';
        }
        if (!filter_var($data['contribution'], FILTER_VALIDATE_FLOAT)) {
            $dataTmp['contribution'] = 'La valeur de la cotisation n\'est pas autorisée';
        }
        if (!filter_var($data['equipment_contribution'], FILTER_VALIDATE_FLOAT)) {
            $dataTmp['equipment_contribution'] = 'La valeur de la cotisation d\'équipement n\'est pas autorisée';
        }
        if (!filter_var($data['address_id'], FILTER_VALIDATE_INT)) {
            $dataTmp['address_id'] = 'La valeur de l\'adresse n\'est pas autorisée';
        }
        if (!filter_var($data['area'], FILTER_VALIDATE_INT)) {
            $dataTmp['area'] = 'La valeur de la surface n\'est pas autorisée';
        }
        if ($data['breakfast'] !== 'inclus' || !filter_var($data['breakfast'], FILTER_VALIDATE_FLOAT)) {
            $dataTmp['breakfast'] = 'L\'information sur le tarif du petit déjeuner doit être un nombre ou \'inclus\'';
        }
        return $dataTmp;
    }

    private function controlDataFile($file, $dataTmp, $data): array
    {
        $fileNameNew = [];
        if (!empty($file['picture'])) {
            $uploadDir = '../public/assets/images/';
            $allowed = array('png', 'gif', 'jpg');
            $fileTmp = $file['picture']['tmp_name'];
            $fileSize = filesize($fileTmp);
            $fileError = $file['picture']['error'];
            $fileExt = explode('.', $file['picture']['name']);
            $fileExt = strtolower(end($fileExt));
            if (!in_array($fileExt, $allowed, true)) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] l'extension '{$fileExt}' n'est pas autorisée.";
            }
            if ($fileError !== 0) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] errored with code {$fileError}";
            }
            if ($fileSize > 1000000) {
                $dataTmp['picture'] = "{$file['picture']['name']} est trop lourd.";
            }
            $fileNameNew = uniqid('', true) . '.' . $fileExt;
            $fileDestination = $uploadDir . $fileNameNew;
            move_uploaded_file($fileNameNew, $fileDestination);
            if (!move_uploaded_file($fileTmp, $fileDestination)) {
                $dataTmp['picture'] = "[{$file['picture']['name']}] n'a pu être téléchargée.";
            }
        }
        $data['picture'] = $fileNameNew;
        return array ($dataTmp,$data);
    }
}
