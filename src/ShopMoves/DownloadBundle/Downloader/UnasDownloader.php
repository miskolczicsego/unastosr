<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 13:09
 */

namespace ShopMoves\DownloadBundle\Downloader;


use ShopMoves\DownloadBundle\DownloadConfig\UnasDownloadConfig;
use Symfony\Component\Process\PhpProcess;

class UnasDownloader
{

    /**
     * @var UnasDownloadConfig
     */
    protected $config;
    /**
     * @var string
     */
    protected $migrationId;

    public function setConfig($userName, $passwordCrypt, $shopId, $authCode, $dir)
    {
        $this->config = new UnasDownloadConfig();
        $this->config->setUserName($userName);
        $this->config->setPasswordCrypt($passwordCrypt);
        $this->config->setShopId($shopId);
        $this->config->setAuthCode($authCode);
        $this->config->setDownloadDir($dir);
    }
    public function getConfig()
    {
        return $this->config;
    }
    public function download()
    {
        $command = "node";
        $path = __DIR__ . "/";
        $process = new PhpProcess(
            "$command $path " .
            "-u {$this->config->getUserName()} " .
            "-p {$this->config->getPasswordCrypt()} " .
            "-s {$this->config->getShopId()} " .
            "-a {$this->config->getAuthCode()} " .
            "-d {$this->config->getDownloadDir()} " .
            "-i {$this->getMigrationId()}"
        );
      $process->start();
      $process->wait();
    }
    /**
     * @return string
     */
    public function getMigrationId()
    {
        return $this->migrationId;
    }
    /**
     * @param string $migrationId
     */
    public function setMigrationId($migrationId)
    {
        $this->migrationId = $migrationId;
    }
}