<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 10:12
 */

namespace ShopMoves\UnasMigrationBundle\Migration;


use Monolog\Logger;
use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Api\Response;
use ShopMoves\UnasMigrationBundle\Helper\LanguageHelper;
use ShopMoves\UnasMigrationBundle\Product\Migration\ProductImageMigration;
use ShopMoves\UnasMigrationBundle\Provider\IDataProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BatchMigration
{
    /**
     * @var array $categoryIds
     */
    protected $categoryIds = [];

    /**
     * @var array $mainImageToProduct
     */
    protected $mainImageToProduct;

    /**
     * @var LanguageHelper $languageHelper
     */
    protected $languageHelper;


    protected $hungarianLanguageId;

    protected $id;

    protected $batchData;

    /**
     * @var ApiCall $apicall
     */
    protected $apicall;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    protected $timeStamp;

    protected $chunkSize;

    /**
     * @var IDataProvider $dataProvider
     */
    protected $dataProvider;

    /** @var Logger $logger */
    protected $logger;

    abstract public function process($data);

    public function __construct(
        IDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    )
    {
        $this->timeStamp = $container->get('timestamp_provider')->getTimestamp();
        $this->container = $container;
        $this->chunkSize = $this->container->getParameter('chunksize');
        $this->logger = $container->get('monolog.logger.unasmigration');
        $this->apiCall = $apiCall;
        $this->dataProvider = $dataProvider;
        $this->languageHelper = $container->get('language_helper');
        $this->hungarianLanguageId = $this->languageHelper->getLanguageByKey('HU');

    }

    public function migrate()
    {

        $datas = $this->dataProvider->getData();

        $this->processUnasDatas($datas);

        if (!empty($this->batchData)) {
            $this->logSendedData();
            $this->processBatchData();
        }
    }

    public function getUrl()
    {

//        return "http://kiscip.api.shoprenter.hu";
//       return 'http://pipereporta.api.shoprenter.hu';

//        return "http://miskolczicsego.api.shoprenter.hu";

        return "http://demo.api.aurora.miskolczicsego";
    }

    public function addToBatchArray($resourceUri, $id = '', $data)
    {
        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => $this->getUrl() . '/' . $resourceUri . (!empty($id) ? '/' . $id : ''),
            'data' => $data
        ];
    }
    //Laci ajánlása szerint élesen 1000 postot rakjunk egy tömbbe
    //Lokálon a post mérete lehet kicsi ezért itt kisebb kell
    //Élesen a limitek:
    //240s TO, 32M, 10.000 elemű requests tömb

    //customerhez 50 kell hogy átmenjen mindenki lokálon
    //producthoz 200


    public function sendBatchChunkData($data)
    {
        /** @var Response $response */
        $response = $this->apiCall->execute('POST', '/batch', $data);
        $responseData = $response->getData();

        $this->logger->info('Response from SR Api: ', ['data' => serialize($responseData)]);

        unset($responseData);
    }

    public function processUnasDatas($datas)
    {
        $this->logger->debug(
            'Start of process ', [
                'class' => get_class($this)
            ]
        );

        $time = 0;
        foreach ($datas as $data){
            $start = microtime(true);
            $this->process($data);
            $time += (microtime(true) - $start);
        }

        $this->logger->debug(
            'End of process ', [
                'class' => get_class($this),
                'time' => number_format($time, 2, '.', ' ') . ' Sec'
            ]
        );
    }

    public function processBatchData()
    {
        $batch = [];
        $time = 0;
        $chunk = array_chunk($this->batchData['requests'], $this->chunkSize, true);

        $this->logger->debug(
            'Start of send to api ', [
                'class' => get_class($this)
            ]
        );
        foreach ($chunk as $batch['requests']) {
            $start = microtime(true);
            $this->sendBatchChunkData($batch);
            $time += (microtime(true) - $start);
        }
        $this->logger->debug(
            'End of send to api ', [
                'class' => get_class($this),
                'time' => number_format($time, 2, '.', ' ') . ' Sec'
            ]
        );

    }

    public function logSendedData()
    {
        $name = explode('\\', get_class($this));
        $this->logger->info('Sended data: ', ['class' => $name[count($name) - 1], 'data' => serialize($this->batchData)]);
    }
}