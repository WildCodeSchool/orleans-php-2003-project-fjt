<?php

namespace App\Model;

/**
 *
 */
class AdmissionManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'admission_file';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent ::__construct(self::TABLE);
    }
    public function insert(array $infosToAdd): void
    {
        $statement = $this->pdo->prepare('INSERT INTO ' . self::TABLE . ' (`marital_status`,`firstname`
        ,`lastname`, `street`,`city`,`phone`,`postal_code`,`dateofbirth`,`zip_path`,`mail`,`folderName`) VALUES
         ( :marital_status,:firstname,:lastname,:street,:city,:phone,:postal_code,:dateofbirth,
         :zip_path, :mail, :folderName)');
        $statement->bindValue(':marital_status', $infosToAdd['maritalstatus'], \PDO::PARAM_STR);
        $statement->bindValue(':firstname', $infosToAdd['firstname'], \PDO::PARAM_STR);
        $statement->bindValue(':lastname', $infosToAdd['lastname'], \PDO::PARAM_STR);
        $statement->bindValue(':street', $infosToAdd['street'], \PDO::PARAM_STR);
        $statement->bindValue(':city', $infosToAdd['city'], \PDO::PARAM_STR);
        $statement->bindValue(':phone', $infosToAdd['phone'], \PDO::PARAM_INT);
        $statement->bindValue(':postal_code', $infosToAdd['postalcode'], \PDO::PARAM_INT);
        $statement->bindValue(':dateofbirth', $infosToAdd['dateofbirth'], \PDO::PARAM_STR);
        $statement->bindValue(':zip_path', $infosToAdd['zip_path'], \PDO::PARAM_STR);
        $statement->bindValue(':mail', $infosToAdd['mail'], \PDO::PARAM_STR);
        $statement->bindValue(':folderName', $infosToAdd['folderName'], \PDO::PARAM_STR);
        $statement->execute();
    }
  
    public function selectAllByTen(): array
    {
        return $this->pdo->query('SELECT * FROM ' . self::TABLE . ' ORDER BY lastname, firstname 
        LIMIT 10')->fetchAll();
    }

    public function selectAllFolder(string $search = ''): array
    {
        $query = 'SELECT * FROM ' . self::TABLE ;
        if ($search) {
            $query .= ' WHERE lastname LIKE :search' ;
        }
        $query .=' ORDER BY lastname, firstname';
        $statement = $this->pdo->prepare($query);
        if ($search) {
            $statement->bindValue('search', $search . '%');
        }
        $statement->execute();

        return $statement->fetchAll();
    }
    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
