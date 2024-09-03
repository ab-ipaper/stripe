<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Define the Item class
class Item
{
    public $ID;
    public $Amount;

}

// Check if the basket element exists in the $_POST array
if (!empty($_POST["basket"])) {
    // Create a new DOMDocument object
    $xmlDoc = new DOMDocument();

    // Load the XML document from a string
    $xmlDoc->loadXml($_POST["basket"]);

    // Use an array to store the items
    $items = [];

    // Use a foreach loop to iterate over the <item> elements in the XML document
    foreach ($xmlDoc->getElementsByTagName("item") as $xn) {
        // Create a new Item object and set its properties
        $item = new Item();
        $item->Amount = intval(
            $xn->getElementsByTagName("amount")->item(0)->nodeValue
        );
        $item->ID = $xn->getElementsByTagName("productid")->item(0)->nodeValue;
        // Add the item to the array
        array_push($items, $item);
    }
} else {
    // If the basket element does not exist, print an error message and exit
$htmlContent = file_get_contents('assets/background-animation.html');
echo $htmlContent;
    exit();
}

// Use the getElementsByTagName method to find all <shop> elements in the XML document
$attributes = $xmlDoc->getElementsByTagName("shop");
$stripe_api = getenv('stripe_api');

require "vendor/autoload.php";
// This is your test secret API key.
\Stripe\Stripe::setApiKey($stripe_api);

header("Content-Type: application/json");

// Set the URL of the Stripe middleware
$YOUR_DOMAIN = "https://stripe.ipaperplayground.repl.co";

// Initialize an array to hold the line items
$line_items_array = [];

// Iterate over the items and add them to the line items array
foreach ($items as $item) {
$line_items_array[] = [
// Set the price to the item ID
"price" => $item->ID,
// Set the quantity to the item amount
"quantity" => $item->Amount,
];
}

// Create a checkout session using the line items array and the success and cancel URLs
$checkout_session = \Stripe\Checkout\Session::create([
"line_items" => [[$line_items_array]],
"mode" => "payment",
"success_url" => $YOUR_DOMAIN . "/success.html",
"cancel_url" => $YOUR_DOMAIN . "/cancel.html",
]);

// Redirect the user to the checkout session URL
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>
