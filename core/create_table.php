<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/GamesData.php';

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


$generateGames = new GamesData();
$generateGames->gamesID($countryArray);
//require_once 'show_table.php';
