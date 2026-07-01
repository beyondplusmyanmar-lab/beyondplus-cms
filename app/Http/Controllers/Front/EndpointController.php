<?php
/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class EndpointController extends Controller
{
    public function clientApi()
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get('http://localhost/oauth/token', [
                'form_params' => [
                    'client_id' => 2,
                    // The secret generated when you ran: php artisan passport:install
                    'client_secret' => 'REDACTED',
                    'grant_type' => 'password',
                    'username' => 'johndoe@scotch.io',
                    'password' => 'secret',
                    'scope' => '*'
                ]
            ]);

            return $response->getBody();

            // You'd typically save this payload in the session
             $auth = json_decode( (string) $response->getBody() );

            // $response = $client->get('http://127.0.0.1:8000/api/todos', [
            //     'headers' => [
            //         'Authorization' => 'Bearer '.$auth->access_token,
            //     ]
            // ]);

            // $todos = json_decode( (string) $response->getBody() );

            // $todoList = "";
            // foreach ($todos as $todo) {
            //     $todoList .= "<li>{$todo->task}".($todo->done ? '✅' : '')."</li>";
            // }

            // echo "<ul>{$todoList}</ul>";

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            echo "Unable to retrieve access token.";
        }

        return $auth;
    }


}
?>