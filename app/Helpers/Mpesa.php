<?php

namespace App\Helpers;
use anlutro\LaravelSettings\Facade as Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;



class  Mpesa{


    //function to generate mpesa token
    public static function generateToken(){
        $client = new Client();
        $baseUrl = env('MPESA_BASE_URL');

        $credentials = base64_encode(env('CONSUMER_KEY').':' .env('CONSUMER_SECRET'));

        try{
            $response = $client->get($baseUrl.'oauth/v1/generate?grant_type=client_credentials',[
                'headers' => [
                    'Authorization' => 'Basic '.$credentials,
                    'Content-Type' => 'application/json',
                ]

            ]);

            return json_decode((string) $response->getBody(), true);


        } catch(BadResponseException $exception) {

            return json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

        }
    }

    //post to end point for requests

    public static function post($endurl,$requestBody){
        $client = new Client();
        $baseUrl = env('MPESA_BASE_URL');
        $token = Setting::get('api-token.token');

        try{
            $response = $client->post($baseUrl.$endurl,[
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestBody
            ]);

            return json_decode((string) $response->getBody(), true);

        }catch (BadResponseException $exception){

            return json_decode((string) $exception->getResponse()->getBody()->getContents(), true);
        }

    }
}