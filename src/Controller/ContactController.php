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
 * Class ItemController
 *
 */
class ContactController extends AbstractController
{
    public function form()
    {
        return $this->twig->render('contact/contact.html.twig');
    }
}
