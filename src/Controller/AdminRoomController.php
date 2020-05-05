<?php


namespace App\Controller;

use App\Model\RoomManager;

class AdminRoomController extends AbstractController
{
    public function index()
    {
        $roomManager = new RoomManager();
        $rooms = $roomManager->selectRoomByAddress();
        $roomByAddresses = [];
        $addresses = $roomManager->selectAddress();
        foreach ($rooms as $room) {
            $roomName = $room['name'];
            $roomByAddresses[$roomName][] = $room;
        }
        return $this->twig->render('AdminRoom/index.html.twig', ['roomByAddresses' => $roomByAddresses,
            'addresses'=> $addresses]);
    }
}
