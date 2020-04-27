<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\AnimationManager;

/**
 * Class ItemController
 *
 */
class AdminAnimationController extends AbstractController
{
    public function index()
    {
        $animationManager = new AnimationManager();
        $animations = $animationManager->selectAll();

        return $this->twig->render('AdminAnimation/index.html.twig', ['animations' => $animations]);
    }
}
