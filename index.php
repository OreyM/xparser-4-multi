<?php
$start = microtime(true);
require_once 'config/config.php';
require_once 'classes/Parsing.php';
require_once 'classes/GamesData.php';

require_once 'classes/InsertData.php';
require_once 'classes/GenerateData.php';
require_once 'classes/MissGames.php';
require_once 'classes/CreateTable.php';


$countryArray = [
    'usa_en_us' => '/en-us',
    'rus_ru_ru' => '/ru-ru',
    'evro_de_de'  => '/de-de',
    'argentina_es_ar' => '/es-ar',
    'brazil_pt_br' => '/pt-br',
    'canada_en_ca' => '/en-ca',
    'columbia_es_co' => '/es-co',
    'hongkong_en_hk' => '/en-hk',
    'india_en_in' => '/en-in',
    'africa_en_za' => '/en-za',
    'turkish_tr_tr' => '/tr-tr',
    'singapore_en_sg' => '/en-sg',
    'mexico_es_mx' => '/es-mx',
    'newzeland_en_nz' => '/en-nz',

//    'Australia' => '/en-au',
//    'japan_ja_jp' => '/ja-jp',
//    'korea_ko_kr' => '/ko-kr',
//    'taiwann_zh_tw' => '/zh-tw',
//    'hungary_hu_hu' => '/hu-hu',
//    'israel_en_il' => '/en-il',
//    'norvay_nb_no' => '/nb-no',
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
    '/store/best-rated/games/xbox',
    '/store/new/games/xbox',
    '/store/top-free/games/xbox'
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

//$dataParsing = new Parsing();

#Собираем курсы валют
//$dataParsing->currencyParsing('http://ru.fxexchangerate.com/currency-exchange-rates.html');
////$dataParsing->varDump();


//foreach ($countryArray as $tableName => $countryID) {
//
//    $parsingUrls = Parsing::getGeneralUrls($countryID, $sitePage);
//
//    #TRUE - pars next page, FALSE - not pars next page
//    $dataParsing->formationParsingData($parsingUrls, $allGamesPageElements, $tableName, TRUE);
//    $dataParsing->parsingSomeGames($gamePageElements, $tableName, 30);
////    $dataParsing->getImages($gamePageElements, 30);
//    $dataParsing->currencyPrice($tableName);
//    $dataParsing->addDataDB($tableName);
//
////    $dataParsing->varDump();
//    $dataParsing->clearParcingData();
//}

$generateGames = new GamesData();
$generateGames->gamesID($countryArray);

####################
####################
#####  #############

//$searchGames = new GenerateData();
//$searchGames->checkMissGames_General($countryArray);
//$searchGames->parsingMissGame($gamePageElements, 'usa_us_us', 20);
//$searchGames->addDataDB('usa_en_us');
//$searchGames->varDump();
//$searchGames->clearParcingData();











###################################################
//$checkGames = new MissGames();
//$checkGames->checkMissGames();

####################
####################
#####  #############

//$searchAnotherGames = new GenerateData();
//$searchAnotherGames->checkMissGames_Another($countryArray);
//$searchGames->parsingMissGame($gamePageElements, 'rus_ru_ru', 20);
//$searchGames->varDump();
//$searchGames->clearParcingData();

//$someGame = new Parsing();
#Игра есть, ценник без скидок и бесплатных игр
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/project-cars-digital-edition/bwd6mg147s5j', $gamePageElements);
#Игры не существует
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/hasbro-family-fun-pack/c2css1s7lwbf', $gamePageElements);
#Ссылка на игру действительна, но ценника нет - Игра только в бандле
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/dragon-age-%d0%98%d0%bd%d0%ba%d0%b2%d0%b8%d0%b7%d0%b8%d1%86%d0%b8%d1%8f/c47gzzbmr5wg', $gamePageElements);
#Ссылка на игру дейстивительна, но игра больше не предоставляется
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/nba-2k16-preorder-edition/brj918k9k7s6', $gamePageElements);
#Ссылка на игру дейстивительна, но игра не для ХБОКСА
//$someGame->oneGame('https://www.microsoft.com/en-us/store/p/motogp-15/bsh5fpmr3gd8?activetab=pivot%3aoverviewtab', $gamePageElements);

echo '<br>';
printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $start));