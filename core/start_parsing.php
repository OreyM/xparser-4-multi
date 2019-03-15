<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XParser Multi RC1</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php
$start = microtime(true);

require_once '../config/config.php';

require_once '../classes/Parsing.php';
require_once '../classes/GamesData.php';


$countryArray = [
     'usa_en_us' => '/en-us',
      'rus_ru_ru' => '/ru-ru',
      'argentina_es_ar' => '/es-ar',
     'brazil_pt_br' => '/pt-br',
     'canada_en_ca' => '/en-ca',
     'columbia_es_co' => '/es-co',
     'hongkong_en_hk' => '/en-hk',
       'india_en_in' => '/en-in',
       'afric_en_za' => '/en-za',
       'turkish_tr_tr' => '/tr-tr',
      'singapore_en_sg' => '/en-sg',
      'mexico_es_mx' => '/es-mx',
      'newzeland_en_nz' => '/en-nz',

//    'Australia' => '/en-au',
//    'evro_de_de'  => '/de-de',
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
    'titleElement'     => 'h3',
    'priceElement'     => '.c-price span[itemprop="price"]',
    'newPriceElement'  => '.price-info .c-price .srv_price span',
    'imageElement'     => '.srv_appHeaderBoxArt > img',
    'discountElement'  => '.c-price s'
];

$gamePageElements = [
    'fullPage' => '.pi-product-image',
    'realPrice' => '.context-product-placement-data dl dd:eq(1) > .price-info > .c-price > .price-text > span',
    'freeRealPrice' => '.context-product-placement-data dl dd:eq(1) > .price-info > .c-price > .price-text > .price-disclaimer > span'
];

$dataParsing = new Parsing();

#Собираем курсы валют
$dataParsing->currencyParsing('https://ru.fxexchangerate.com/currency-exchange-rates.html');

foreach ($countryArray as $tableName => $countryID) {

    $parsingUrls = Parsing::getGeneralUrls($countryID, $sitePage);

    #TRUE - pars next page, FALSE - not pars next page
    $dataParsing->formationParsingData($parsingUrls, $allGamesPageElements, $tableName, TRUE);
    $dataParsing->parsingSomeGames($gamePageElements, $tableName, 20);
    $dataParsing->getImages($gamePageElements, 20);
    $dataParsing->currencyPrice($tableName);
    $dataParsing->addDataDB($tableName);

//    $dataParsing->varDump();


    $country = $dataParsing->getCountryName($tableName);
    $newImages = count($dataParsing::$newImagesData);

    echo "
    <div class='container'>
    <div class='row'>
    <div class=\"col s12 m12\">
         <ul id=\"task-card\" class=\"collection with-header\">
             <li class=\"collection-header cyan\">
                 <h4 class=\"task-card-title\">Парсинг страны: {$country}</h4>
                 <p class=\"task-card-date\"> Новые игры: <strong>$newImages</strong></p>
             </li>";

    if(!empty($dataParsing::$newImagesData)) {


        foreach ($dataParsing::$newImagesData as $imageData) {
            echo "
            <li class=\"collection-item dismissable\" style=\"touch-action: pan-y; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);\">
                 В базе новая игра! <a href=\"{$imageData['gameUrl']}\" target=\"_blank\">{$imageData['gameName']} </a> Game ID - <strong>{$imageData['gameID']}</strong>
            </li>
        ";
        }

    }

    echo "
         </ul>
     </div>
     </div>
     </div>
     ";

    $dataParsing->clearParcingData();
}
echo '<br><br><br>';
printf('Скрипт выполнялся %.4F сек.', (microtime(true) - $start));

?>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
</body>
</html>