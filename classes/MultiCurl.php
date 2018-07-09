<?php
class MultiCurl{

    private
        $multiUrls,
        $multi,
        $channels = array(),
        $resultContent = array();

    public function __construct(array $multiUrlArray){

        $this->multiUrls = $multiUrlArray;
        $this->multi = curl_multi_init();

    }

    public function getData(){

        foreach ($this->multiUrls as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0');

            curl_multi_add_handle($this->multi, $ch);

            $this->channels[$url] = $ch;
        }

        $active = null;

        do {
            $mrc = curl_multi_exec($this->multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            do {
                $mrc = curl_multi_exec($this->multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        foreach ($this->channels as $url => $channel) {
            $this->resultContent[$url] = curl_multi_getcontent($channel);
            curl_multi_remove_handle($this->multi, $channel);
        }

        curl_multi_close($this->multi);

        return $this->resultContent;
    }

}