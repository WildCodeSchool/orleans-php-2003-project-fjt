<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class AnimationManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'animation';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectByFour()
    {
        return $this->pdo->query('SELECT * FROM animation ORDER BY id DESC LIMIT 4')->fetchAll();
    }

    /**
     * @param array $animation
     * @return int
     */
    public function insert(array $animation): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`name`, `description`, `image`)
        VALUES (:name, :description, :image)");
        $statement->bindValue('name', $animation['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $animation['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $animation['image'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function update(array $animation): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name, `description` = :description,
        `image` = :image WHERE id=:id");
        $statement->bindValue('id', $animation['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $animation['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $animation['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $animation['image'], \PDO::PARAM_STR);
        return $statement->execute();
    }

    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
