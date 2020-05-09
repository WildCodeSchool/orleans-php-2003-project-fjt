<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\AdmissionManager;

use Chumper\Zipper\Zipper;

/**
 * Class AdminAdmissionController
 *
 */
class AdminAdmissionController extends AbstractController
{
    public function index()
    {
        $admissionManager = new AdmissionManager();
        $folders = $admissionManager->selectAllByTen();
        // le code ci-dessous sert à générer en fonction de l'iniquid du dossier zip un numéro de dossier
        // pour l'affichage
        $countFolders = count($folders);
        for ($i = 0; $i < $countFolders; $i++) {
            $exploded = $this->multiexplode([",",".","/","(",")"], $folders[$i]['zip_path']);
            $folders[$i]['fold'] = $exploded[8];
        }
        return $this->twig->render('AdminAdmission/index.html.twig', ['folders' => $folders]);
    }
    public function show(int $id)
    {
        $admissionManager = new AdmissionManager();
        $folder = $admissionManager->selectOneById($id);
        // le code ci-dessous sert à générer en fonction de l'iniquid du dossier zip un numéro de dossier
        // pour l'affichage
        $exploded = $this->multiexplode([",",".","/","(",")"], $folder['zip_path']);
        $folder['fold'] = $exploded[8];

        $files = (new Zipper)->make($folder['zip_path'])->listFiles('/^(?!.*\.log).*$/i');
        return $this->twig->render('AdminAdmission/show.html.twig', [
            'folder' => $folder,
            'files' => $files
        ]);
    }
    public function allWaiting()
    {
        $admissionManager = new AdmissionManager();
        $folders = $admissionManager->selectAllFolder();

        return $this->twig->render('AdminAdmission/allWaiting.html.twig', ['folders' => $folders]);
    }
    public function allValidate()
    {
        $admissionManager = new AdmissionManager();
        $folders = $admissionManager->selectAllFolder();

        return $this->twig->render('AdminAdmission/allValidate.html.twig', ['folders' => $folders]);
    }
    private function multiexplode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $ready);
    }
}
