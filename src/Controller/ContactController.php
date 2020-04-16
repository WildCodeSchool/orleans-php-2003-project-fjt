<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\ContactManager;

/**
 * Class ContactController
 *
 */
class ContactController extends AbstractController
{
    public function index()
    {
        return $this->twig->render('contact/index.html.twig');
    }
}
