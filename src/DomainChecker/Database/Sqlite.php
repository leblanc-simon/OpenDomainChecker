<?php

namespace DomainChecker\Database;

use DomainChecker\Core\Config;

class Sqlite
    extends ADatabase
    implements IDatabase
{
    /**
     * @var \PDO    the PDO instance
     */
    private $pdo = null;

    public function connect()
    {
        if (null === $this->pdo) {
            $this->pdo = new \PDO('sqlite:'.Config::get('sqlite_db'));
        }

        return $this->pdo;
    }

    public function findOne($type, $name)
    {
        if (in_array($type, $this->available_types) === false) {
            throw new \InvalidArgumentException('type is not valid');
        }

        $this->connect();

        $stmt = $this->pdo->prepare('SELECT value FROM '.$type.' WHERE name = :name');
        $stmt->bindValue(':name', $name, \PDO::PARAM_STR);

        if ($stmt->execute() === false) {
            throw new \PDOException('Impossible to execute the query');
        }

        $value = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (false === $value) {
            return null;
        }

        return json_decode($value['value']);
    }

    public function save()
    {
        $this->connect();

        foreach ($this->available_types as $type) {
            $datas = $this->{$type.'s'};
            foreach ($datas as $key => $value) {
                if ($this->findOne($type, $key) === null) {
                    $sql = 'INSERT INTO '.$type.' (name, value) VALUES (:name, :value)';
                } else {
                    $sql = 'UPDATE '.$type.' SET value = :value WHERE name = :name';
                }

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':name', $key);
                $stmt->bindValue(':value', json_encode($value));
                $stmt->execute();
            }
        }
    }
}