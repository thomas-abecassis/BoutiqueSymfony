<?php

namespace App\Classe;

use DateTimeImmutable;

class Suivi
{

    private $api_key = "lqkp8RNcQIfL2jBuYBqfM7iK+OG89s7UopLJRYG4e/r7oPnMbMGcz0KQOwa7ODqE";
    private $response;


    public function __construct($numeroSuivi)
    {
        if ($numeroSuivi == null) {
            $this->response = null;
            return;
        }
        $ch = curl_init();
        $headers    = [];
        $headers[]  = 'Content-Type: application/json';
        $headers[]  = 'X-Okapi-Key: ' . $this->api_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://api.laposte.fr/suivi/v2/idships/" . $numeroSuivi . "?lang=fr_FR");
        $this->response = json_decode(curl_exec($ch), true);
    }

    /*
    * 0 -> colis non envoyé, 1 -> colis en route, 2 -> colis livré
    */
    public function getEtatCode()
    {
        if (!$this->response || count($this->response["shipment"]["event"]) == 0)
            return 0;

        $responseCode = $this->response["shipment"]["event"][0]["code"];

        if ($responseCode == "DI1" || $responseCode == "DI2" || $responseCode == "AG1")
            return 2;

        return 1;
    }

    public function getEventTimeline()
    {
        if ($this->getEtatCode() == 0)
            return [];

        return $this->response["shipment"]["event"];
    }

    public function getLastEvent()
    {
        if ($this->getEtatCode() == 0) {
            $date = new DateTimeImmutable();
            $date = $date->format('Y-m-d');
            return [
                "date" => $date,
                "label" => "Votre colis n'est pas encore envoyé",
                "code" => "DR1"
            ];
        }

        return $this->response["shipment"]["event"][0];
    }
}
