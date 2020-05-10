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

    public function insertAddress(array $data): void
    {
        $statement = $this -> pdo -> prepare('INSERT INTO ' . self::TABLE . ' (`name`,`address`,`description`) 
        VALUES (:name,:address,:description)');

        $statement -> bindValue(':name', $data['name'], \PDO::PARAM_STR);
        $statement -> bindValue(':address', $data['address'], \PDO::PARAM_STR);
        $statement -> bindValue(':description', $data['description'], \PDO::PARAM_STR);
        $statement -> execute();
    }
    public function deleteAddress($id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
