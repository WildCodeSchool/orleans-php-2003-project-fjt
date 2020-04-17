<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\ServiceManager;

/**
 * Class ItemController
 *
 */
class ServiceController extends AbstractController
{


    /**
     * Display item listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $serviceManager = new ServiceManager();
        $services = $serviceManager->selectAll();

        return $this->twig->render('Service/index.html.twig', ['services' => $services]);
    }
}
