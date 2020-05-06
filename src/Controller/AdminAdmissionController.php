<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\AdminAdmissionManager;

use Chumper\Zipper\Zipper;

/**
 * Class AdminAnimationController
 *
 */
class AdminAdmissionController extends AbstractController
{
    public function index()
    {
        $admissionManager = new AdminAdmissionManager();
        $folders = $admissionManager->selectAllByTen();

        $countFolders = count($folders);
        for ($i = 0; $i < $countFolders; $i++) {
            $exploded = $this->multiexplode([",",".","/","(",")"], $folders[$i]['zip_path']);
            $folders[$i]['fold'] = $exploded[8];
        }
        return $this->twig->render('AdminAdmission/index.html.twig', ['folders' => $folders]);
    }
    public function show(int $id)
    {
        $admissionManager = new AdminAdmissionManager();
        $folder = $admissionManager->selectOneById($id);
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
        $search = trim($_GET['search'] ?? '');
        $admissionManager = new AdminAdmissionManager();
        $folders = $admissionManager->selectAllFolder($search);

        return $this->twig->render('AdminAdmission/allWaiting.html.twig', ['folders' => $folders]);
    }
    public function allValidate()
    {
        $search = trim($_GET['search'] ?? '');
        $admissionManager = new AdminAdmissionManager();
        $folders = $admissionManager->selectAllFolder($search);

        return $this->twig->render('AdminAdmission/allValidate.html.twig', ['folders' => $folders]);
    }
    private function multiexplode($delimiters, $string)
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        return explode($delimiters[0], $ready);
    }
}
