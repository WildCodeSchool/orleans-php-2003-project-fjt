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
        $rooms = $roomManager->selectRoom();

        return $this->twig->render('Price/index.html.twig', ['rooms' => $rooms]);
    }
}
