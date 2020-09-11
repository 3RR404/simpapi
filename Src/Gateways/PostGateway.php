<?php

namespace Src\Gateways;

class PostGateway
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
                id, title, slug, content, user_id
            FROM
                post;
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
                id, title, slug, content, user_id
            FROM
                post
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
            INSERT INTO post 
                (title, slug, content, user_id)
            VALUES
                (:title, :slug, :content, :user_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'title' => $input['title'],
                'slug'  => $input['slug'],
                'content' => $input['content'],
                'user_id' => $input['user_id']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE post
            SET 
                title = :title,
                slug  = :slug,
                content = :content
            WHERE id = :id AND user_id = :user_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'title' => $input['title'],
                'slug'  => $input['slug'] ?? null,
                'content' => $input['content'],
                'user_id' => $input['user_id']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit( $e->getMessage() );
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM post
            WHERE id = :id AND user_id = :user_id;
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