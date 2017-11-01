<?php

namespace Stemis\Jura\PartnerNet;

use GuzzleHttp\Client;

/**
 * Class Connection
 * @package Stemis\Jura\Partnernet
 * @author Steff Missot <me@stemis.nl>
 */
class Connection
{
    const BASE_URL = 'http://shop.jurapartnernet.nl/';

    /**
     * @var Client
     */
    private $client;

    /**
     * Connection constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->client = new Client(['cookies' => true]);
        $this->login($username, $password);
    }

    /**
     * @param $username
     * @param $password
     * @throws \Exception
     */
    protected function login($username, $password)
    {
        // This call is necessary to initialize the session cookies before logging in using the POST call
        $this->client->request('GET', self::BASE_URL . 'Index.cfm');

        $this->client->request('POST', self::BASE_URL . 'Index.cfm', [
            'form_params' => [
                'userlogin' => $username,
                'userpassword' => $password,
            ]
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }
}
