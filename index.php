<?php
$start = microtime(true);
require_once 'config/config.php';
require_once 'classes/Parsing.php';


$countryArray = [
    'usa_en_us' => '/en-us',
//    'rus_ru_ru' => '/ru-ru',
//    'EVRO'  => '/de-de',
//    'Argentina' => '/es-ar',
//    'Brazil' => '/pt-br',
//    'Canada' => '/en-ca',
//    'Columbia' => '/es-co',
//    'Hong-Kong' => '/en-hk',
//    'India' => '/en-in',
//    'South Africa' => '/en-za',
//    'Turkish' => '/tr-tr',
//    'Singapore' => '/en-sg',
//    'Norvay' => '/nb-no',
//    'Mexico' => '/es-mx',
//    'Hungary' => '/hu-hu',
//    'Israel' => '/en-il',
//    'Japan' => '/ja-jp',
//    'South Korea' => '/ko-kr',
//    'Taiwann' => '/zh-tw',

//    'Australia' => '/en-au',
//    'England' => '/en-gb',
//    'Dania' => '/da-dk',
//    'New Zealand' => '/en-nz',
//    'Poland' => '/pl-pl',
//    'Switzerland' => '/de-ch',
//    'Chili' => '/es-cl',
//    'Czech' => '/cs-cz',
];

$sitePage = [
        '/store/top-paid/games/xbox',
//        '/store/best-rated/games/xbox',
//        '/store/new/games/xbox',
//        '/store/top-free/games/xbox'
];

$allGamesPageElements = [
    'fullPage'         => '.context-list-page .m-product-placement-item',
    'titleElement'     => '.c-heading',
    'priceElement'     => '.c-price span[itemprop="price"]',
    'newPriceElement'  => '.price-info .c-price .srv_price span',
    'imageElement'     => '.srv_appHeaderBoxArt > img',
    'discountElement'  => '.c-price s'
];

$gamePageElements = [
    'fullPage' => '.m-product-detail-hero .m-product-detail-hero-product-placement',
    'realPrice' => '.context-product-placement-data dl dd:eq(1) > .price-info > .c-price > .price-text > span',
    'freeRealPrice' => '.context-product-placement-data dl dd:eq(1) > .price-info > .c-price > .price-text > .price-disclaimer > span'
];

foreach ($countryArray as $tableName => $countryID) {

    $parsingUrls = Parsing::getGeneralUrls($countryID, $sitePage);

    $firstDataParsing = new Parsing();
    #TRUE - pars next page, FALSE - not pars next page
    $firstDataParsing->formationParsingData($parsingUrls, $allGamesPageElements, FALSE);
    $firstDataParsing->parsingSomeGames($gamePageElements, 25);
    $firstDataParsing->transformPrice($tableName);
//    $firstDataParsing->getImages($gamePageElements, 10);
//    $firstDataParsing->addDataDB($tableName);

    $firstDataParsing->varDump();
    $firstDataParsing->clearParcingData();
}

//$someGame = new Parsing();
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/voodoo-vince-remastered/9nblggh42xc3', $gamePageElements);
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/fifa-18/bwpkgqv97n7n', $gamePageElements);
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/minecraft-for-windows-10-mobile/9wzdncrdzsbb', $gamePageElements);

echo '<br>';
printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $start));