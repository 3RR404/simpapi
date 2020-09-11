<?php 

namespace Src\Seeds;

use Src\System\TableCreator;
use Src\System\TableSeeder;

class UserSeed 
{
    private $table = null;

    public function __construct( $table )
    {
        $this->table = $table;

        $this->create();
    }

    protected function create()
    {
        $tableUser = new TableCreator( $this->table );
        $tableUser->integer( 'id', 11, '', true );
        $tableUser->string('first_name', 100);
        $tableUser->string('last_name', 100);
        $tableUser->string('e_mail', 100);
        $tableUser->up();

        $this->seed();
    }

    protected function seed()
    {
        $values = [
            [
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'e_mail' => 'john.doe@example.com'
            ],
            [
                'id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'e_mail' => 'jane.doe@example.com'
            ]
        ];
        
        $seed = new TableSeeder( $this->table );
        $seed->seed( $values );
    }
    
}