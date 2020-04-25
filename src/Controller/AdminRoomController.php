<?php


namespace App\Controller;

use App\Model\AdminRoomManager;

class AdminRoomController extends AbstractController
{
    public function index()
    {
        $adminRoomManager = new AdminRoomManager();
        $addresses = $adminRoomManager->selectAddress();
        $rooms = $adminRoomManager->selectRoom();
        return $this->twig->render('AdminRoom/index.html.twig', ['addresses' => $addresses, 'rooms' => $rooms]);
    }
}
