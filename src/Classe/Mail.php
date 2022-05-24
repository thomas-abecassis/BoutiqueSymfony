<?php

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{

    //Utilisation de l'api Mailjet, création de mail sur leur plateforme et on utilise leur api pour l'envoyer

    private $api_key = "6c3ceea72935fc863adada3e91a623d2";
    private $api_secrete_key = "2a48897e03cf7e645e42f084902990b3";

    public function send($destination, $objet, $contenu)
    {
        $mj = new Client($this->api_key, $this->api_secrete_key, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "testtoasttust@yopmail.com",
                        'Name' => "Boutique"
                    ],
                    'To' => [
                        [
                            'Email' => $destination,
                            'Name' => "Client"
                        ]
                    ],
                    'TemplateID' => 3958588,
                    'TemplateLanguage' => true,
                    'Subject' => "Toast",
                    'Variables' => json_decode('{
          "content": "' . $contenu . '"
        }', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
    }
}
