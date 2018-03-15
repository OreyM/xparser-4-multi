<?php
require_once 'phpQuery/phpQuery.php';
require_once 'Curl.php';

class SomePage {

    public function getSomePageData ($url) {

        $dataUrl = array();

        $curlData = (new Curl($url))->getCurlData();
        $elementParsing = phpQuery::newDocument($curlData);

        foreach ($elementParsing->find('#ContentBlockList_1 .gameDivsWrapper .m-product-placement-item') as $parsingData) {
//            '#ContentBlockList_1 .gameDivsWrapper .m-product-placement-item' - MS XBOX
//            'table tr td' - XBOX

            $parsingData = pq($parsingData);

            $productLink = $parsingData->find('a')->attr('href');

            if(substr($productLink, -6) == 'search')
                $productLink = str_replace('?cid=msft_web_search', '', $productLink);

            $productID = substr($productLink, -12);

            $dataUrl[] = $productID;
        }
        return $dataUrl;
    }
}