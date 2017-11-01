<?php

namespace Stemis\Jura\PartnerNet;

/**
 * Class Address
 * @package Stemis\Jura\PartnerNet
 * @author Steff Missot <me@stemis.nl>
 */
class Address
{
    /**
     * @var string Name of the company
     */
    public $name;

    /**
     * @var string Address of the company including house number
     */
    public $address;

    /**
     * @var string postalcode or zipcode of the address
     */
    public $postal;

    /**
     * @var string City
     */
    public $city;

    /**
     * @var string Attention to, ie a person within the company
     */
    public $attention;
}
