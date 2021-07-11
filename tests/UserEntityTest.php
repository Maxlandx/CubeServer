<?php

namespace App\Tests;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\Stats\CurlSender;

class UserEntityTest extends WebTestCase
{
    private $curlSender;
    private $token;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->curlSender = new CurlSender();
        $response = $this->curlSender->POST(
            'http://localhost', 
            '/api/login_check', 
            ['username' => 'citizen_1@gmail.com', 'password' => 'root'] 
        );
        $this->token = $response['content']->token;
    }

    /**
    * @covers RelationController::searchUser
    */
    public function testSearchUsers()
    {
        $query1 = "";
        $response1 = $this->curlSender->GET(
            'http://localhost', 
            '/api/user/search?q='.$query1, 
            array("type" => "Authorization: Bearer ", 
            "token" => $this->token)
        );

        $users1 = $response1['content']->users;

        foreach($users1 as $user) {
            $this->assertTrue(str_contains(strtolower($user->text), strtolower($query1)), 
            "User firstname/lastname contains query (no characters)");
        }

        $query2 = "s";
        $response2 = $this->curlSender->GET(
            'http://localhost', 
            '/api/user/search?q='.$query2, 
            array("type" => "Authorization: Bearer ", 
            "token" => $this->token)
        );

        $users2 = $response2['content']->users;

        foreach($users2 as $user) {
            $this->assertTrue(str_contains(strtolower($user->text), strtolower($query2)), 
            "User firstname/lastname contains query (single character)");
        }

        $query3 = "SANDY";

        $response3 = $this->curlSender->GET(
            'http://localhost', 
            '/api/user/search?q='.$query3, 
            array("type" => "Authorization: Bearer ", 
            "token" => $this->token)
        );

        $users3 = $response3['content']->users;

        //dd($users3);

        foreach($users3 as $user) {
            $this->assertTrue(str_contains(strtolower($user->text), strtolower($query3)), 
            "User firstname/lastname contains query (upper case characters)");
        }

        //$this->assertTrue(count($users1) <= 15);
    }
}