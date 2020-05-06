<?php

namespace App\Controller;

class AbstractAdminController extends AbstractController
{
    public const ALLOWED_MIME = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
    public const MAX_SIZE = 1000000;
    public const UPLOAD_DIR = '../public/assets/images/uploads/';

    protected function controlDataOne($data, $addressesId)
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

    protected function controlDataTwo($data)
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

    protected function controlDataFilterOne(array $data): array
    {
        $errorsFilterOne = [];
        if (!filter_var($data['guarantee'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilterOne['guarantee'] = 'La valeur du dépôt de garantie n\'est pas autorisé';
        }
        if (!filter_var($data['catering'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilterOne['catering'] = 'La valeur du crédit restauration n\'est pas autorisé';
        }
        if (!filter_var($data['contribution'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilterOne['contribution'] = 'La valeur de la cotisation n\'est pas autorisée';
        }
        if (!filter_var($data['equipment_contribution'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilterOne['equipment_contribution']
                = 'La valeur de la cotisation d\'équipement n\'est pas autorisée';
        }
        if (!filter_var($data['address_id'], FILTER_VALIDATE_INT)) {
            $errorsFilterOne['address_id'] = 'La valeur de l\'adresse n\'est pas autorisée';
        }
        return $errorsFilterOne ?? [];
    }

    protected function controlDataFilterTwo(array $data): array
    {
        $errorsFilterTwo = [];

        if ($data['area'] > 0 && !filter_var($data['area'], FILTER_VALIDATE_INT)) {
            $errorsFilterTwo['area'] = 'La valeur de la surface n\'est pas autorisée';
        }
        if ($data['breakfast'] !== 'inclus' && !filter_var($data['breakfast'], FILTER_VALIDATE_FLOAT)) {
            $errorsFilterTwo['breakfast'] = 'L\'information sur le tarif du petit déjeuner doit être 
            un nombre ou \'inclus\'';
        }
        return $errorsFilterTwo ?? [];
    }

    protected function controlDataFile(array $file): array
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
                $errorsUpload['picture'] = 'Le fichier doit faire moins de ' . (self::MAX_SIZE / 1000000) .
                    ' Mo';
            }
            if (empty($errorsUpload)) {
                $fileNameNew = uniqid('', true) . '.' . $fileExtension;
            }
        } else {
            $errorsUpload['picture'] = "Problème lors de l'import du fichier";
        }
        return [$fileNameNew, $errorsUpload] ?? [];
    }
}
