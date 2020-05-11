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

        return $this->twig->render('AdminAdmission/index.html.twig', ['folders' => $folders]);
    }
    public function show(int $id)
    {
        $admissionManager = new AdmissionManager();
        $folder = $admissionManager->selectOneById($id);
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
        $folders = $admissionManager -> selectAllFolder();

        return $this -> twig -> render('AdminAdmission/allValidate.html.twig', ['folders' => $folders]);
    }
}
