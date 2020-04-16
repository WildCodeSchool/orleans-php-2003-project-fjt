<?php

namespace App\Controller;

use App\Model\RoomManager;

class PriceController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $roomManager = new RoomManager();
        $prices = $roomManager->selectPrice();

        return $this->twig->render('Price/index.html.twig', ['prices' => $prices]);
    }
}
