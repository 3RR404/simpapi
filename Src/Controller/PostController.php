<?php

namespace Src\Controller;

use Src\Gateways\PostGateway;
use Src\Helpers\TextHelper;

class PostController 
{
    private $db;
    private $requestMethod;
    private $postId;

    private $postGateway;

    public function __construct($db, $requestMethod, $postId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->postId = $postId;

        $this->postGateway = new PostGateway( $this->db );
    }

    public function processRequest()
    {
        switch ($this->requestMethod)
        {
            case 'GET':
                if ( $this->postId )
                {
                    $response = $this->getPost( $this->postId );
                } else {
                    $response = $this->getPosts();
                };
            break;
            case 'PUT':
                $response = $this->updatePost( $this->postId );
            break;
            case 'POST':
                $response = $this->createPost();
            break;
        }
        header( $response['status_code_header'] );
        if ( $response['body'] ) {
            echo $response['body'];
        }
    }

    protected function getPost( int $id = 0 )
    {
        if ( !$id )
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';

            $response['body'] = \json_encode([
                "state" => "error",
                "error_code" => 102,
                "error_description" => "ID not found"
            ]);

            return $response;
        }
        else
        {
            $post =  $this->postGateway->find( $id );

            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            $response['body'] = json_encode( $post );

            return $response;
        }
    }

    protected function getPosts()
    {
        $posts =  $this->postGateway->findAll();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode( $posts );
        return $response;

    }

    protected function createPost()
    {
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if ($e_inp = $this->validatePostInputs($input) !== TRUE ) {
            return $this->unprocessableEntityResponse( $e_inp );
        }

        if ( empty( $input['slug'] ) ) $input['slug'] = TextHelper::slugify( $input['title'] );

        if( $this->postGateway->insert( $input ) )
        {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = \json_encode([
                'state' => 'success',
                'description' => 'Post has been created'
            ]);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = \json_encode([
                'state' => 'error',
                'error_code' => 103,
                'error_description' => 'Something wrong'
            ]);
        }

        return $response;
    }
    protected function updatePost( $id )
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if ( is_string( $e_inp = $this->validatePostInputs($input) ) )
            return $this->unprocessableEntityResponse( $e_inp );

        if ( empty( $input['slug'] ) ) $input['slug'] = TextHelper::slugify( $input['title'] );

        if( $this->postGateway->update($id, $input) )
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = \json_encode([
                'state' => 'success',
                'description' => 'Post has been updated'
            ]);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $response['body'] = \json_encode([
                'state' => 'error',
                'error_code' => 103,
                'error_description' => 'Something wrong'
            ]);
        }

        return $response;
    }

    protected function validatePostInputs( array $input )
    {
        $missing_input = '';

        if (! isset($input['title'])) {
            $missing_input = 'title';
        }
        if (! isset($input['user_id'])) {
            $missing_input = !empty( $missing_input ) ? "$missing_input, " : "" ;
            $missing_input .= 'user_id';
        }
        if (! isset($input['content'])) {
            $missing_input = !empty( $missing_input ) ? "$missing_input, " : "" ;
            $missing_input .= 'content';
        }
        return !empty($missing_input) ? $missing_input : true;
    }

    private function unprocessableEntityResponse( $error )
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'state' => 'error',
            'error_code' => '102',
            'error_description' => 'Invalid input',
            'error_details' => [
                'description' => 'Is mandantory',
                'input' => $error
            ]
        ]);
        return $response;
    }
}