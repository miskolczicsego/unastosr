<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 10:12
 */

namespace ShopMoves\UnasMigrationBundle\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Api\Response;
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

    /**
     * @var IDataProvider $dataProvider
     */
    protected $dataProvider;

    abstract public function process($data);

    public function __construct(
        IDataProvider $dataProvider,
        ApiCall $apiCall,
        ContainerInterface $container
    ) {
        $this->timeStamp = $container->get('timestamp_provider')->getTimestamp();
        $this->container = $container;
        $this->apiCall = $apiCall;
        $this->dataProvider = $dataProvider;
        $this->languageHelper = $container->get('language_helper');
        $this->hungarianLanguageId = $this->languageHelper->getLanguageByKey('HU');

    }

    public function getMigrationId()
    {
        return $this->id;
    }

    public function migrate()
    {
        $chunkSize = 5;
        $datas = $this->dataProvider->getData();
        $time = 0;
//dump($datas);die;
        file_put_contents('status.log', 'Start of process ' . (get_class($this). PHP_EOL), FILE_APPEND);
        foreach ($datas as $data){
            $start = microtime(true);
            $this->process($data);
            $time += (microtime(true) - $start);
        }
        file_put_contents('status.log', 'End of process ' . (get_class($this) .' | TIME: ' . number_format($time, 2, '.', ' ') . ' Sec' . PHP_EOL) , FILE_APPEND);

//        die;
        file_put_contents('api_send_status.log', 'Start of send to api ' . (get_class($this). PHP_EOL), FILE_APPEND);
        $batch = [];
        $chunk = array_chunk($this->batchData['requests'],$chunkSize, true);
        $time = 0;
        foreach ($chunk as $batch['requests']) {
            $start = microtime(true);
            $this->sendBatchData($batch);
            $time += (microtime(true) - $start);
        }
        file_put_contents('api_send_status.log', 'End of send to api' . (get_class($this) .' | TIME: ' . number_format($time, 2, '.', ' ') . ' Sec' . PHP_EOL), FILE_APPEND);
        sleep(1);

    }

    public function isProductDeleted($product)
    {
        if ($product->State === 'deleted') {
            return true;
        }
        return false;
    }

    /**
     * @param $product
     * @return string
     */
    public function getProductOuterId($product)
    {
        return base64_encode('product_productId' . $product->Sku. $this->timeStamp);
    }

    public function getUrl()
    {

//        return "http://kiscip.api.shoprenter.hu";
//        return "http://miskolczicsego.api.shoprenter.hu";

        return "http://demo.api.aurora.miskolczicsego";
    }

    public function addToBatchArray($resourceUri, $id='', $data)
    {
        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => $this->getUrl() . '/' .$resourceUri  . (!empty($id) ? '/' . $id : ''),
            'data' => $data
        ];
    }
    //Laci ajánlása szerint élesen 1000 postot rakjunk egy tömbbe
    //Lokálon a post mérete lehet kicsi ezért itt kisebb kell
    //Élesen a limitek:
    //240s TO, 32M, 10.000 elemű requests tömb

    //customerhez 50 kell hogy átmenjen mindenki lokálon
    //producthoz 200


    public function sendBatchData($data)
    {
        /** @var Response $response */
        $response = $this->apiCall->execute('POST', '/batch',  $data);

        $data = $response->getData();

        dump($data);
    }
}