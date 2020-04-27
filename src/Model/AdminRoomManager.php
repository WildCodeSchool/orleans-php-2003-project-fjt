<?php


namespace App\Model;

use PDO;

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
        return $this->pdo->query('SELECT *, r.id FROM room r JOIN address ON r.address_id = address.id ORDER BY 
        address.id ASC, r.area ASC')->fetchAll();
    }
    public function selectAddress(): array
    {
        return $this->pdo->query('SELECT * FROM address ORDER BY 
        address.id ASC')->fetchAll();
    }
    public function insert(array $infosToAdd): void
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' (`type`,`guarantee`,`equipment`,
        `restoration`,`contribution`,`breakfast`,`equipment_contribution`,`picture`,`area`,`address_id`) VALUES
         (:type,:guarantee,:equipment,:restoration,:contribution,:breakfast,:equipment_contribution,:picture,
         :area,:address_id)');
        self ::bound($infosToAdd, $statement);

        $statement->execute();
    }
    public static function bound($adminPrice, $statement)
    {
        foreach ($adminPrice as $key => $value) {
            if (is_int($value) || is_float($value)) {
                $statement->bindValue(':' . $key, $value, PDO::PARAM_INT);
            } else {
                $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
            }
        }
    }
}
