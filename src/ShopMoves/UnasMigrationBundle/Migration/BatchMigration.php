<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.06.30.
 * Time: 10:12
 */

namespace ShopMoves\UnasMigrationBundle\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
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

    /**
     * @var IDataProvider $dataProvider
     */
    protected $dataProvider;

    abstract public function process($data);

    abstract public function getOuterId($data);

    public function __construct(IDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        $this->container = $container;
        $this->apiCall = $apiCall;
        $this->dataProvider = $dataProvider;
    }

    public function getMigrationId()
    {
        return $this->id;
    }

    public function migrate()
    {
        $datas = $this->dataProvider->getData();
        $time = 0;
        foreach ($datas as $data){
            $start = microtime(true);
            $this->process($data);
            $time += (microtime(true) - $start);
            file_put_contents('time.log', number_format($time, 2, '.', ' ') . ' Sec' . PHP_EOL);
            file_put_contents('memory.log', number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ') . ' MB' . PHP_EOL);
        }
//        dump($this->batchData);die;
        $batch = [];


        //Laci ajánlása szerint élesen 1000 postot rakjunk egy tömbbe
        //Lokálon a post mérete lehet kicsi ezért itt kisebb kell
        //Élesen a limitek:
        //240s TO, 32M, 10.000 elemű requests tömb

        //customerhez 50 kell hogy átmenjen mindenki lokálon
        //producthoz 200
        $chunk = array_chunk($this->batchData['requests'], 50, 1);
        foreach ($chunk as $batch['requests']) {
            if(!$batch['requests']) {
                return;
            }
            $response = $this->apiCall->execute('POST', '/batch',  $batch);
            dump($response);
        }

    }

    public function isProductDeleted($product)
    {
        if ($product->State === 'deleted') {
            return true;
        }
        return false;
    }

    public function getProductOuterId($product)
    {
        return base64_encode('product_id-Product=' . $product->Sku);
    }

    public function getUrl()
    {
        return "http://demo.api.aurora.miskolczicsego";
    }

    public function addToBatchArray($resourceUri, $id, $data)
    {
        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => $this->getUrl() . $resourceUri . (!empty($id) ? $id : ''),
            'data' => $data
        ];
    }
}