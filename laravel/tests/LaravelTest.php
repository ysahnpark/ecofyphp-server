<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Laravel 5');
    }

    public function testBox()
    {
        $validationRules = array(
            'email' => 'email',
            'nested.name' => 'required'
        );
        $fields = [
            'email' => 'gmail.com',
            'nested' => [
                'name' => 'hello'
            ]
        ];
        $validator = Validator::make($fields, $validationRules);
        //$validator = \Validator::make($fields, $validationRules);

        $passes = $validator->passes();
        var_dump($passes);
        $expected = [
            'email' => ['The email must be a valid email address.']
        ];
        $actual = json_encode($validator->messages()->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $actual);
    }
}
