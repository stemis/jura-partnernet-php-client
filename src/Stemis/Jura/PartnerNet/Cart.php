<?php

namespace Stemis\Jura\PartnerNet;

use Stemis\Jura\PartnerNet\Exceptions\NoAddressDataException;
use Stemis\Jura\PartnerNet\Exceptions\NoItemsInCartException;

/**
 * Class Cart
 * @package Stemis\Jura\PartnerNet
 * @author Steff Missot <me@stemis.nl>
 */
class Cart
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Address
     */
    protected $address;

    /**
     * List of items added to the cart
     * @var array[Item]
     */
    protected $items = [];

    /**
     * Cart constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Add an item to the cart and prepare for order execution
     *
     * @param Item $item The product to add to the cart
     * @param int $amount The amount to add to the cart
     */
    public function addItem(Item $item, $amount = 1)
    {
        $this->items[] = $item;

        $this->connection->getClient()->request('POST', Connection::BASE_URL . 'com/Remote.cfc', [
                'form_params' => [
                    'method' => 'editCart',
                    'quantity' => $amount,
                    'itemNo' => $item->itemId,
                    'action' => 'add',
                    'pagekey' => 'items'
                ]
            ]
        );
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;

        // This call is necessary to save the address information to the session
        $this->connection->getClient()->request('POST', Connection::BASE_URL . 'com/Remote.cfc', [
                'form_params' => [
                    'method' => 'editCartExtra',
                    'refval' => '',
                    'comval' => '',
                    'shiptoval' => '_ALT_', // Ship to alternate address(not default address)
                    'afhalenval' => '0', // Explicit string
                    'afhaaldatumval' => (new \DateTime())->format('d-m-Y'),
                    'naamval' => $address->name,
                    'adresval' => $address->address,
                    'adres2val' => '',
                    'postcodeval' => $address->postal,
                    'plaatsval' => $address->city,
                    'contactval' => $address->attention
                ]
            ]
        );
    }

    /**
     * Order all the items in the cart
     */
    public function orderCart()
    {
        if (!$this->address instanceof Address) {
            throw new NoAddressDataException();
        }

        if(!count($this->items)) {
            throw new NoItemsInCartException();
        }

        // Although the address information is already saved to the session, it is also used in the 'Order' call
        $this->connection->getClient()->request('POST', Connection::BASE_URL . 'showCart.cfm', [
                'form_params' => [
                    'name_1' => '1',
                    'shiptocode' => '_ALT_',
                    'afhaaldatum' => (new \DateTime())->format('d-m-Y'), // Current date, obsolete field
                    'naam' => $this->address->name,
                    'adres' => $this->address->address,
                    'adres2' => '',
                    'postcode' => $this->address->postal,
                    'plaats' => $this->address->city,
                    'contact' => $this->address->attention,
                    'reference' => '',
                    'comment' => '',
                    'conditions' => 'on',
                    'gotoPage' => '',
                    'pagingSize' => '25', // Explicit string
                    'currentPage' => '1', // Explicit string
                    'sendOrder' => '1', // Explicit string
                    'action' => ''
                ]
            ]
        );

        $this->sendConfirmationEmail();
    }

    /**
     * After the order has been sent in, you can send a receipt to your email address known by Jura.
     */
    protected function sendConfirmationEmail()
    {
        $this->connection->getClient()->request('POST', Connection::BASE_URL . 'showCart.cfm', [
                'form_params' => [
                    'sendOrder' => '2', // Explicit string
                    'action' => ''
                ]
            ]
        );
    }
}
