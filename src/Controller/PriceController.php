<?php

namespace App\Controller;

use App\Model\RoomManager;

use Alchemy\Zippy\Zippy;

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
    public const UPLOAD_DIR = '../public/assets/images/uploads/';

    public function index()
    {
        $roomManager = new RoomManager();
        $rooms = $roomManager -> selectRoom();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $files = $_FILES;
            $errorsFormEmpty = $this -> controlFormEmpty($data);
            $errorsFormFilter = $this -> controlFormFilter($data);
            list($filesNameNew, $errorsUpload) = $this -> controlFiles($files);
            $errors = array_merge($errorsFormEmpty, $errorsFormFilter, $errorsUpload);
            if (empty($errors)) {
                $zippy = Zippy::load();
                $zipName = $data['firstname'] . '/' . $data['lastname'] . ".zip";
                $archive = $zippy->create($zipName);
                $archive->addMembers(
                    array(
                        '/path/to/file',
                        '/path/to/file2',
                        '/path/to/dir'
                    )
                );
                foreach ($filesNameNew as $fileNameNew) {
                    $fileDestination = self::UPLOAD_DIR . $fileNameNew;
                    move_uploaded_file($fileNameNew, $fileDestination);
                }

                header('Location:/Price/index');
            }
        }
        return $this -> twig -> render('Price/index.html.twig', [
            'rooms' => $rooms,
            'info' => $data ?? [],
            'errors' => $errors ?? []
        ]);
    }

    private function controlFormEmpty($datas)
    {
        $errorsFormOne = [];
        foreach ($datas as $data) {
            if (empty($data)) {
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
        if (!empty($files) && $files['error'] === 0) {
            foreach ($files as $file) {
                $fileTmp = $file['tmp_name'];
                $fileSize = filesize($fileTmp);
                $mimeType = mime_content_type($fileTmp);
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
                    $errorsUpload['picture'] = "Le fichier n'est pas autorisée,
                 les types de fichiers autorisés sont " . implode(', ', self::ALLOWED_MIME) . '.';
                }
                if ($fileSize > self::MAX_SIZE) {
                    $errorsUpload['picture'] = 'Le fichier doit faire moins de ' . (self::MAX_SIZE / 1000000) .
                        ' Mo';
                }
                if (empty($errorsUpload)) {
                    $filesNameNew[] = uniqid('', true) . '.' . $fileExtension;
                } else {
                    $errorsUpload['picture'] = "Problème lors de l'import des fichiers";
                }
            }
            return [$filesNameNew, $errorsUpload] ?? [];
        }
    }
}
