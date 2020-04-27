<?php


namespace App\Controller;

use App\Model\AdminRoomManager;

class AdminRoomController extends AbstractController
{
    public function index()
    {
        $adminRoomManager = new AdminRoomManager();
        $rooms = $adminRoomManager->selectRoomByAddress();
        $roomByAddresses = [];
        foreach ($rooms as $room) {
            $roomName = $room['name'];
            $roomByAddresses[$roomName][] = $room;
        }
        return $this->twig->render('AdminRoom/index.html.twig', ['roomByAddresses' => $roomByAddresses]);
    }
}
