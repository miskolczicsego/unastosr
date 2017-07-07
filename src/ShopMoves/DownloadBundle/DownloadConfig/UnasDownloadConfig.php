<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 13:10
 */

namespace ShopMoves\DownloadBundle\DownloadConfig;


class UnasDownloadConfig implements DownloadConfig
{
    /**
     * @var string
     */
    private $userName;
    /**
     * @var string
     */
    private $passwordCrypt;
    /**
     * @var string
     */
    private $shopId;
    /**
     * @var string
     */
    private $authCode;
    /**
     * @var string
     */
    private $downloadDir;
    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }
    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }
    /**
     * @return string
     */
    public function getPasswordCrypt()
    {
        return $this->passwordCrypt;
    }
    /**
     * @param string $passwordCrypt
     */
    public function setPasswordCrypt($passwordCrypt)
    {
        $this->passwordCrypt = $passwordCrypt;
    }
    /**
     * @return string
     */
    public function getShopId()
    {
        return $this->shopId;
    }
    /**
     * @param string $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }
    /**
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }
    /**
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }
    /**
     * @return string
     */
    public function getDownloadDir()
    {
        return $this->downloadDir;
    }
    /**
     * @param string $downloadDir
     */
    public function setDownloadDir($downloadDir)
    {
        $this->downloadDir = $downloadDir;
    }
}