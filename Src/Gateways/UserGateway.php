<?php

namespace Src\Gateways;

class UserGateway
{
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, first_name, last_name, e_mail
            FROM
                user;
        ";

        try {
            $statement = $this->db->query( $statement );
            $result = $statement->fetchAll( \PDO::FETCH_ASSOC );
            return $result;
        } catch (\PDOException $e) {
            exit( $e->getMessage() );
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, first_name, last_name, e_mail
            FROM
                user
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO user 
                (first_name, last_name, e_mail)
            VALUES
                (:first_name, :last_name, :e_mail);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'],
                'e_mail' => $input['e_mail']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE user
            SET 
                first_name = :first_name,
                last_name  = :last_name,
                e_mail = :e_mail
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'] ?? null,
                'e_mail' => $input['e_mail']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit( $e->getMessage() );
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM user
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}