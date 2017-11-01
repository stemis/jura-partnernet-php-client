<?php

namespace Stemis\Jura\PartnerNet\Repositories;

use Stemis\Jura\PartnerNet\Connection;
use Stemis\Jura\PartnerNet\Item;


/**
 * Class ProductRepository
 * @package Stemis\Jura\Partnernet\Repositories
 * @author Steff Missot <me@stemis.nl>
 */
class ItemRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * ProductRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Find an item by the itemId
     *
     * @param $itemId int Number provided by Jura
     * @return Item
     */
    public function find($itemId)
    {
        // Make a request to the Jura site to retrieve the HTML used by the fancy box
        $response = $this->connection->getClient()->request(
            'GET',
            Connection::BASE_URL . 'Jura/Jura_ShowItemDetailsFancybox.cfm', [
                'query' => [
                    'typeScreen' => 'getDetails',
                    'pageKey' => 'items',
                    'key' => $itemId
                ]
            ]
        );

        $html = (string)$response->getBody();

        // Parse the HTML using DOMDocument and XPath
        $doc = new \DOMDocument();
        $doc->loadHTML($html);

        // XPath for ItemHeader class
        $finder = new \DOMXPath($doc);
        $nodes = $finder->query("//td[contains(@class, 'itemHeader')]");
        $description = $nodes[0]->textContent;

        // XPath for ItemData class
        $finder = new \DOMXPath($doc);
        $nodes = $finder->query("//td[contains(@class, 'itemData')]");
        $ean = $nodes[1]->nodeValue;

        // Create Item object and fill
        $item = new Item();
        $item->description = $description;
        $item->itemId = $itemId;
        $item->ean = $ean;

        return $item;
    }
}
