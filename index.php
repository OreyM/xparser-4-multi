<?php
$start = microtime(true);
require_once 'config/config.php';
require_once 'classes/Parsing.php';


$countryArray = [
    'USA' => '/en-us'
];

$sitePage = [
        '/store/top-paid/games/xbox',
        '/store/best-rated/games/xbox',
        '/store/new/games/xbox',
        '/store/top-free/games/xbox'
];

$gamePageElements = [
    'fullPage'         => '.context-list-page .m-product-placement-item',
    'titleElement'     => '.c-heading',
    'priceElement'     => '.c-price span[itemprop="price"]',
    'newPriceElement'  => '.price-info .c-price .srv_price span',
    'imageElement'     => '.srv_appHeaderBoxArt > img',
    'goldElement'      => '.c-price span img',
    'eaElement'        => '.c-price > span:last',
    'gamePassElement'  => '.c-price span img',
    'discountElement'  => '.c-price > s'
];

foreach ($countryArray as $someCountry){

    $sitePagesReady = Parsing::getGeneralUrls($someCountry, $sitePage);

    $firstDataParsing = new Parsing();
    $allGamesData = $firstDataParsing->formationParsingData($sitePagesReady, $gamePageElements);

    echo '<pre>';
    var_dump($allGamesData);
    echo '</pre>';

    $firstDataParsing->clearParcingData();

}



//$firstDataParsing->addParsingDataDB();



//$result = (new MultiCurl($urls))->getData();
//echo $result[0];


printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $start));