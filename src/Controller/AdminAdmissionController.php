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
    const UPLOAD_DIR = '../public';
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
        $files = (new Zipper)->zip(self::UPLOAD_DIR . $folder['zip_path'])->folder($folder['folderName'])->
        listFiles('/^(?!.*\.log).*$/i');
        return $this->twig->render('AdminAdmission/show.html.twig', [
            'folder' => $folder,
            'files' => $files
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
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admissionManager = new AdmissionManager();
            $admission = $admissionManager->selectOneById($id);
            if ($admission) {
                if (file_exists(self::UPLOAD_DIR . $admission['zip_path'])) {
                    unlink(self::UPLOAD_DIR . $admission['zip_path']);
                }
                $admissionManager->delete($id);
            }
            header('Location:/AdminAdmission/index');
        }
    }
}
