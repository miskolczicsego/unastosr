<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.29.
 * Time: 13:48
 */

namespace ShopMoves\UnasMigrationBundle\Curl;


class Curl
{
    protected $ch;
    protected $info = array();
    protected $error = false;
    protected $options = array();

    public function setOption($key, $val)
    {
        $this->options[$key] = $val;
    }

    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function addHeader($header)
    {
        $this->setOption(CURLOPT_HEADER, true);
    }

    /**
     * @param $url String
     */
    public function setUrl($url)
    {
        $this->setOption(CURLOPT_URL, $url);
    }

    /**
     * @param $fields Array
     */
    public function setPostFields($fields)
    {
        $this->setOption(CURLOPT_POST, 1);
        $this->setOption(CURLOPT_POSTFIELDS, http_build_query($fields));
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getError()
    {
        return $this->error;
    }

    public function send()
    {
        $this->ch = curl_init();
        curl_setopt_array($this->ch, $this->options);
        $result = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        $this->error = curl_error($this->ch);
        curl_close($this->ch);
        if (strlen($this->error)) {
            return false;
        }
        return $result;
    }

    /**
     * Újraindíthatjuk a curl_init-et, ha még nem lett elindítva, vagy le lett zárva
     * Akkor érdemes, ha egymás után szeretnénk több mint 1 URLt curlozni
     */
    public function reset()
    {
        if (!is_resource($this->ch)) {
            $this->ch = curl_init();
        }
    }

    /**
     * Példány "kipucolása". Az error, info, options, illetve a channel adattagok ürítése.
     *
     * @return void
     */
    public function clean()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
        $this->ch = null;
        $this->error = false;
        $this->info = array();
        $this->options = array();
    }

    /**
     * Lekérjük és visszaadjuk egy távoli URL tartalmát
     * a file_get_contents() kiváltására készült, mert nem mindenhol engedélyezett az allow_url_fopen()
     *
     * @param $url
     * @return bool|string
     */
    public function getContents($url)
    {
        $this->ch = curl_init();
        $this->setUrl($url);
        curl_setopt_array($this->ch, $this->options);
        ob_start();
        curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        $this->error = curl_error($this->ch);
        $return = ob_get_contents();
        ob_end_clean();
        curl_close($this->ch);

        if (strlen($this->error) || $this->info['http_code'] != 200) {
            return false;
        }
        return $return;
    }
}