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
    public function selectRoom(): array
    {
        return $this->pdo->query('SELECT * FROM room JOIN address ON room.address_id=address.id ORDER BY 
        address.id ASC, room.area ASC')->fetchAll();
    }
    public function selectRoomByAddress(): array
    {
        return $this->pdo->query('SELECT *, r.id FROM room r JOIN address ON r.address_id = address.id ORDER BY 
        address.id ASC, r.area ASC')->fetchAll();
    }
    public function insertAddress(array $data): void
    {
        $statement = $this->pdo->prepare('INSERT INTO address (`name`,`address`,`description`) VALUES
         (:name,:address,:description)');

        $statement->bindValue(':name', $data['name'], \PDO::PARAM_STR);
        $statement->bindValue(':address', $data['address'], \PDO::PARAM_STR);
        $statement->bindValue(':description', $data['description'], \PDO::PARAM_STR);
        $statement->execute();
    }
    public function selectAddress(): array
    {
        return $this->pdo->query('SELECT * FROM address ORDER BY 
        address.id ASC')->fetchAll();
    }

    public function insert(array $infosToAdd): void
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' (`type`,`guarantee`,`equipment`,
        `catering`,`contribution`,`breakfast`,`equipment_contribution`,`picture`,`area`,`address_id`) VALUES
         (:type,:guarantee,:equipment,:catering,:contribution,:breakfast,:equipment_contribution,:picture,
         :area,:address_id)');
        self ::bound($infosToAdd, $statement);

        $statement->execute();
    }
    private static function bound($adminPrice, $statement)
    {
        foreach ($adminPrice as $key => $value) {
            if (is_int($value) || is_float($value)) {
                $statement->bindValue(':' . $key, $value, \PDO::PARAM_INT);
            } elseif ($value === null) {
                $statement->bindValue(':' . $key, $value, \PDO::PARAM_NULL);
            } else {
                $statement->bindValue(':' . $key, $value, \PDO::PARAM_STR);
            }
        }
    }
}
