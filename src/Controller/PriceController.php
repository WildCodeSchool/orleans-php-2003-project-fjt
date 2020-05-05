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
    public const UPLOAD_DIR = '../public/assets/images/';

    public function index()
    {
        $roomManager = new RoomManager();
        $admissionManager = new AdmissionManager();
        $rooms = $roomManager -> selectRoom();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
            $data = array_map('trim', $_POST);
            $files = $_FILES['file'];
            $errorsFormEmpty = $this -> controlFormEmpty($data);
            $errorsFormFilter = $this -> controlFormFilter($data);
            list($filesNameNew, $errorsUpload) = $this -> controlFiles($files);
            $errors = array_merge($errorsFormEmpty, $errorsFormFilter, $errorsUpload);
            if (empty($errors)) {
                $zipper = new Zipper;
                $zipName = $data['firstname'] . $data['lastname'] . '(' . uniqid('', true) . ')'. ".zip";
                $zipper->make('../public/assets/images/uploads/' . $zipName)->folder($zipName);
                foreach ($files['name'] as $position => $value) {
                    $fileDestination = self::UPLOAD_DIR . $filesNameNew[$position];
                    move_uploaded_file($files['tmp_name'][$position], $fileDestination);
                    $zipper->add('../public/assets/images/' . $filesNameNew[$position]);
                }
                $zipper->make('../public/assets/uploads/' . $zipName . "/")->close();
                foreach ($filesNameNew as $fileName) {
                    $filesDel = '../public/assets/images/' . $fileName;
                    unlink($filesDel);
                }
                $data['zip_path'] = '../public/assets/uploads/' . $zipName;
                $admissionManager->insert($data);
                header('Location:/Home/index');
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
        $errorsFormOne = [];
        foreach ($datas as $data => $value) {
            if (empty($value)) {
                $errorsFormOne[$data] = 'Ce champ ne doit pas être vide';
            }
        }
        return $errorsFormOne ?? [];
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
    private function controlFiles($files)
    {
        $errorsUpload = [];
        $filesNameNew = [];
        foreach ($files['name'] as $position => $value) {
            $fileTmp = $files['tmp_name'][$position];
            $fileSize = filesize($fileTmp);
            $mimeType = mime_content_type($fileTmp);
            $fileError = $files['error'][$position];
            $fileExtension = pathinfo($files['tmp_name'][$position], PATHINFO_EXTENSION);
            if ($fileError === 0) {
                if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
                    $errorsUpload['file'] = "Le fichier n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_MIME) . '.';
                }
                if ($fileSize > self::MAX_SIZE) {
                    $errorsUpload['file'] = 'Le fichier doit faire moins de ' . (self::MAX_SIZE / 1000000) .
                        ' Mo';
                }

                if (empty($errorsUpload)) {
                    $filesNameNew[$position] = uniqid('', true) . '.' . $fileExtension;
                }
            }
        }
        return [$filesNameNew, $errorsUpload] ?? [];
    }
}
