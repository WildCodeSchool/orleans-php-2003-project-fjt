<?php


namespace App\Model;

class AdminRoomManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'room';


    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectRoomByAddress(): array
    {
        return $this->pdo->query('SELECT * FROM room r JOIN address ON r.address_id = address.id ORDER BY 
        address.id ASC, r.area ASC')->fetchAll();
    }
}
