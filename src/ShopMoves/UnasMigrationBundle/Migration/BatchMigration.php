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
        foreach ($datas as $data){
            $this->process($data);
        }
//        dump($this->batchData);die;
        $batch = [];
        //Laci ajánlása szerint élesen 1000 postot rakjunk egy tömbbe
        //Lokálon a post mérete lehet kicsi ezért itt kisebb kell
        //Élesen a limitek:
        //240s TO, 32M, 10.000 elemű requests tömb

        //customerhez 50 kell hogy átmenjen mindenki lokálon
        //producthoz 200
        foreach (array_chunk($this->batchData['requests'], 50, 1) as $batch['requests']) {
            if(!$batch['requests']) {
                return;
            }
            $response = $this->apiCall->execute('POST', '/batch',  $batch);
//            dump($response);
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
        return base64_encode($product->Sku);
    }

    public function getUrl()
    {
        return "http://miskolczicsego.api.shoprenter.hu";
    }

    public function addToBatchArray($resourceUri, $id, $data)
    {
        $this->batchData['requests'][] = [
            'method' => 'POST',
            'uri' => $this->getUrl() . $resourceUri . $id,
            'data' => $data
        ];
    }
}