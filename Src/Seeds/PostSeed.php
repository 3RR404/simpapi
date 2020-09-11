<?php 

namespace Src\Seeds;

use Src\System\TableCreator;
use Src\System\TableSeeder;

class PostSeed 
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
        $tableUser->string('title', 100);
        $tableUser->string('slug', 100);
        $tableUser->longtext('content', 100);
        $tableUser->integer('user_id', 11);
        $tableUser->up();

        $this->seed();
    }

    protected function seed()
    {
        $values = [
            [
                'id' => 1,
                'title' => 'Post test number one',
                'slug' => 'post-test-number-one',
                'content' => 'Consequat laboris dolor dolor aliquip veniam. Reprehenderit exercitation reprehenderit proident ullamco.Velit sint est in incididunt est aliqua voluptate est deserunt reprehenderit. Irure esse magna excepteur eiusmod. Laboris elit commodo est non pariatur consectetur. Laboris ex et consequat eiusmod id quis in aliquip eu do aliqua labore incididunt nulla. Minim elit consectetur elit ea tempor sit culpa Lorem Lorem. Nulla tempor eu deserunt adipisicing anim ad reprehenderit. Occaecat ipsum aliquip deserunt proident non et magna quis magna sint. Dolore anim qui reprehenderit veniam dolore elit culpa ex consectetur duis ad. Voluptate nulla esse magna culpa reprehenderit ut fugiat est sint elit enim nisi.',
                'user_id' => 1
            ],
            [
                'id' => 2,
                'title' => 'Post test number two',
                'slug' => 'post-test-number-two',
                'content' => 'Ipsum minim cillum ut et sit ad sit pariatur Lorem. Duis fugiat nulla id consectetur sit quis esse id nulla mollit eiusmod adipisicing tempor duis.',
                'user_id' => 2
            ]
        ];
        
        $seed = new TableSeeder( $this->table );
        $seed->seed( $values );
    }
    
}