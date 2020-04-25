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
    public function selectAddress(): array
    {
        return $this->pdo->query('SELECT * FROM address ORDER BY 
        address.id ASC')->fetchAll();
    }
    public function selectRoom(): array
    {
        return $this->pdo->query('SELECT *, r.id FROM room r JOIN address ON r.address_id = address.id ORDER BY 
        address.id ASC, r.area ASC')->fetchAll();
    }
}
