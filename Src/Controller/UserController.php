<?php

namespace Src\Controller;

use Src\Gateways\UserGateway;

class UserController 
{
    private $db;
    private $requestMethod;
    private $userId;

    private $userGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->userGateway = new UserGateway( $this->db );
    }

    public function processRequest()
    {
        switch ($this->requestMethod)
        {
            case 'GET':
                if ( $this->userId )
                {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getUsers();
                };
            break;
            case 'PUT':
                $response = $this->updateUser( $this->userId );
            break;
            case 'POST':
                $response = $this->createUser();
            break;
        }
        header( $response['status_code_header'] );
        if ( $response['body'] ) {
            echo $response['body'];
        }
    }

    protected function getUser( int $id = 0 )
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
            $user =  $this->userGateway->find( $id );

            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            $response['body'] = json_encode( $user );

            return $response;
        }
    }

    protected function getUsers()
    {
        $users =  $this->userGateway->findAll();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode( $users );
        return $response;

    }

    protected function createUser()
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if ($e_inp = $this->validateUser($input) !== TRUE ) {
            return $this->unprocessableEntityResponse( $e_inp );
        }

        if( $this->userGateway->insert( $input ) )
        {
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = \json_encode([
                'state' => 'success',
                'description' => 'User has been created'
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
    protected function updateUser( $id )
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if ( is_string( $e_inp = $this->validateUser($input) ) ) {
            return $this->unprocessableEntityResponse( $e_inp );
        }

        if( $this->userGateway->update($id, $input) )
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = \json_encode([
                'state' => 'success',
                'description' => 'User has been updated'
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

    protected function validateUser($input)
    {
        $missing_input = '';

        if (! isset($input['first_name'])) {
            $missing_input = 'first_name';
        }
        if (! isset($input['last_name'])) {
            $missing_input = !empty($missing_input) ? "$missing_input, " : "";
            $missing_input .= 'last_name';
        }
        if (! isset($input['e_mail'])) {
            $missing_input = !empty($missing_input) ? "$missing_input, " : "";
            $missing_input .= 'e_mail';
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