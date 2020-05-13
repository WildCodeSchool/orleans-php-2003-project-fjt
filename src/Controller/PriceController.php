<?php

namespace App\Controller;

use App\Model\AdmissionManager;
use App\Model\RoomManager;

use Chumper\Zipper\Zipper;

class PriceController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public const ALLOWED_MIME = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'application/pdf'];
    public const MAX_SIZE = 1000000;
    public const UPLOAD_DIR = '../public/uploads/';

    public function index()
    {
        $roomManager = new RoomManager();
        $admissionManager = new AdmissionManager();
        $rooms = $roomManager -> selectRoom();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $files = $_FILES['file'];
            $errorsFormEmpty = $this -> controlFormEmpty($data);
            $errorsFormFilter = $this -> controlFormFilter($data);
            $errorsFormLengthOne = $this -> controlFormLengthOne($data);
            $errorsFormLengthTwo = $this -> controlFormLengthTwo($data);
            list($filesNameNew, $errorsUpload) = $this -> controlFiles($files);
            $errors = array_merge(
                $errorsFormEmpty,
                $errorsFormFilter,
                $errorsUpload,
                $errorsFormLengthOne,
                $errorsFormLengthTwo
            );
            if (empty($errors)) {
                $zipper = new Zipper;
                $zipName = $data['lastname'] . '_' . uniqid('', true) . ".zip";
                $folderName = $data['lastname'] . '_' . $data['firstname'] ;
                $zipper->make('../public/uploads/' . $zipName)->folder($folderName);
                foreach ($files['name'] as $position => $name) {
                    $fileDestination = self::UPLOAD_DIR . $filesNameNew[$position];

                    if (!move_uploaded_file($files['tmp_name'][$position], $fileDestination)) {
                        $errors['uploads'][$name] = 'Les fichiers n\'a pas pu être téléchargé';
                    }
                    $zipper->add(self::UPLOAD_DIR . $filesNameNew[$position]);
                }
                $zipper->make(self::UPLOAD_DIR . $zipName . "/")->close();
                foreach ($filesNameNew as $fileName) {
                    $filesDel = self::UPLOAD_DIR . $fileName;
                    unlink($filesDel);
                }
                $data['zip_path'] = $zipName;
                $data['folderName'] = $folderName;
                $admissionManager->insert($data);
                header('Location: /price/index');
            }
        }
        return $this -> twig -> render('Price/index.html.twig', [
            'rooms' => $rooms,
            'data' => $data ?? [],
            'errors' => $errors ?? []
        ]);
    }

    private function controlFormEmpty($datas)
    {
        $errorsFormEmpty = [];
        foreach ($datas as $data => $value) {
            if (empty($value)) {
                $errorsFormEmpty[$data] = 'Ce champ ne doit pas être vide';
            }
        }
        return $errorsFormEmpty ?? [];
    }

    private function controlFormFilter($data)
    {
        $errorsFormTwo = [];
        if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
            $errorsFormTwo['mail'] = 'L\' adresse saisie doit être un email.';
        }
        if (!filter_var($data['postalcode'], FILTER_VALIDATE_INT)) {
            $errorsFormTwo['postalcode'] = 'Ce champ doit contenir un code postal';
        }
        return $errorsFormTwo ?? [];
    }
    private function controlFormLengthOne($data)
    {
        $errorsFormLengthOne = [];

        if (strlen($data['city']) > 100) {
            $errorsFormLengthOne['city'] = 'Ce champ est trop long';
        }
        if (strlen($data['phone']) > 10) {
            $errorsFormLengthOne['phone'] = 'Ce champ est trop long';
        }
        if (strlen($data['postalcode']) > 7) {
            $errorsFormLengthOne['postalcode'] = 'Ce champ est trop long';
        }
        if (strlen($data['dateofbirth']) > 10) {
            $errorsFormLengthOne['dateofbirth'] = 'Ce champ est trop long';
        }
        if (strlen($data['mail']) > 255) {
            $errorsFormLengthOne['mail'] = 'Ce champ est trop long';
        }
        return $errorsFormLengthOne ?? [];
    }
    private function controlFormLengthTwo($data)
    {
        $errorsFormLengthTwo = [];
        if (strlen($data['maritalstatus']) > 50) {
            $errorsFormLengthTwo['maritalstatus'] = 'Ce champ est trop long';
        }
        if (strlen($data['firstname']) > 45) {
            $errorsFormLengthTwo['firstname'] = 'Ce champ est trop long';
        }
        if (strlen($data['lastname']) > 45) {
            $errorsFormLengthTwo['lastname'] = 'Ce champ est trop long';
        }
        if (strlen($data['street']) > 255) {
            $errorsFormLengthTwo['street'] = 'Ce champ est trop long';
        }
        return $errorsFormLengthTwo ?? [];
    }
    private function controlFiles($files)
    {
        $errorsUpload = [];
        $filesNameNew = [];

        foreach ($files['name'] as $position => $name) {
            $fileTmp = $files['tmp_name'][$position];
            $fileSize = filesize($fileTmp);
            $mimeType = mime_content_type($fileTmp);
            $fileError = $files['error'][$position];
            $fileExtension = pathinfo($files['name'][$position], PATHINFO_EXTENSION);
            if ($fileError === 0) {
                if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
                    $errorsUpload['file'] = "Le fichier" . $name ." n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_MIME) . '.';
                }
                if ($fileSize > self::MAX_SIZE) {
                    $errorsUpload['file'] = 'Le fichier' . $name . ' doit faire moins de ' . (self::MAX_SIZE / 1000000)
                        . ' Mo';
                }

                if (empty($errorsUpload)) {
                    $filesNameNew[$position] = uniqid('', true) . '.' . $fileExtension;
                }
            }
        }
        return [$filesNameNew, $errorsUpload] ?? [];
    }
}
