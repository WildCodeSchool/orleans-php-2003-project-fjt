<?php

namespace App\Model;

/**
 *
 */
class PriceManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'price';

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
        return $this->pdo->query('SELECT * FROM room JOIN address ON room.address_id=address.id')->fetchAll();
    }
}
