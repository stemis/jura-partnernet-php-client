# php-jura-partnernet
Makes connecting with Jura Partnernet much easier.

Although Jura does not have any form of API to create orders in their system, dealers can use the "Jura Partner Net" to create all orders.
This can be useful when A person orders bean machines via either a webshop or other external system and directly order them with Jura with no delay.

This client utilizes the web portal and copies the exact HTTP calls made to the Cobalt back-end to achieve the goals.

## Installation
`composer require stemis/jura-partnernet-client`

## Example code

```
// Create the connection
$connection = new Connection('YOUR_USERNAME', 'YOUR_PASSWORD');

// Create a new cart
$cart = new Cart($connection);

// Search for the item you want to add to the carrt
$itemRepository = new ItemRepository($connection);
$item = $itemRepository->find(15157);

// Add Item
$cart->addItem($item);

// Create and set address
$address = new Address();
$address->name = 'John Doe';
$address->address = 'Stationsplein';
$address->postal = '1012AB';
$address->city = 'Amsterdam';
$address->attention = 'Afd. XXXX';

$cart->setAddress($address);

// Proceed with order and send confirmation email
$cart->orderCart();

```

## Exceptions
- NoAddressDataException
- NoItemsInCartException

## Issues
There are currently no known issues.

If you do find an issue, please use the issues tab to report it!

## Future Work
- Adding Unit Tests using Mockery
- Adding more detailed information about items
