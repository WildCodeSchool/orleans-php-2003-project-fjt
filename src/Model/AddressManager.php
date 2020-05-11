<?php


namespace App\Model;

/**
 *
 */
class AddressManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'address';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent ::__construct(self::TABLE);
    }

    public function update($data)
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name,
         `address` = :address, `description` = :description WHERE id=:id");
        $statement -> bindValue(':name', $data['name'], \PDO::PARAM_STR);
        $statement -> bindValue(':address', $data['address'], \PDO::PARAM_STR);
        $statement -> bindValue(':description', $data['description'], \PDO::PARAM_STR);
        $statement -> bindValue(':id', $data['id'], \PDO::PARAM_INT);
        return $statement->execute();
    }
}
