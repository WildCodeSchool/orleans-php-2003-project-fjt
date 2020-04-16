<?php

namespace App\Model;

/**
 *
 */
class RoomManager extends AbstractManager
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
        parent ::__construct(self::TABLE);
    }

    /**
     * Get all row from database.
     *
     * @return array
     */
    public function selectPrice(): array
    {
        return $this->pdo->query('SELECT * FROM room JOIN address ON room.address_id=address.id ORDER BY 
        address.id ASC, room.area ASC')->fetchAll();
    }
}
