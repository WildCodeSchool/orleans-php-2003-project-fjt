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
        $status = ['En attente', 'Validé'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_map('trim', $_POST);
            $errors = [];

            if (empty($data['status'])) {
                $errors['status'] = 'le status ne doit pas être vide.';
            }
            if (!in_array($data['status'], $status)) {
                $errors['status'] = 'le status n\'éxiste pas, il doit être \'En attente\' ou \'Validé\'.';
            }
            if (empty($errors)) {
                $admissionManager->update($data);
                header('Location:/AdminAdmission/index');
            }
        }
        return $this->twig->render('AdminAdmission/show.html.twig', [
            'folder' => $folder,
            'files' => $files,
            'status'=> $status,
            'errors'=> $errors ?? []
        ]);
    }

    public function allWaiting()
    {
        $search = trim($_GET['search'] ?? '');
        $admissionManager = new AdmissionManager();
        $folders = $admissionManager->selectAllFolder($search);
        return $this->twig->render('AdminAdmission/allWaiting.html.twig', ['folders' => $folders]);
    }
    public function allValidate()
    {
        $search = trim($_GET['search'] ?? '');
        $admissionManager = new AdmissionManager();
        $folders = $admissionManager->selectAllFolder($search);

        return $this->twig->render('AdminAdmission/allValidate.html.twig', ['folders' => $folders]);
    }
}
