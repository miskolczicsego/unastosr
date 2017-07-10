<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.07.
 * Time: 14:20
 */

namespace ShopMoves\UnasMigrationBundle\Category\Migration;


use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryDescriptionMigration extends BatchMigration
{

    public function __construct(CategoryDataProvider $dataProvider, ApiCall $apiCall, ContainerInterface $container)
    {
        parent::__construct($dataProvider, $apiCall, $container);
    }

    public function process($product)
    {

        if ($this->isProductDeleted($product)) {
            return;
        }
        $category = $product->Categories->Category;
        $queue = [];
       if(is_array($category)){

           foreach ($category as $cat) {
                if (!array_key_exists($cat->Id, $this->categoryIds)){
                    $this->categoryIds[$cat->Id] = 1;
                    $categoryParts = explode('|', $cat->Name);

                    if (count($categoryParts) > 1) {
                        $counter = 0;
                        foreach ($categoryParts as $categoryPart) {
                            array_push($queue, $categoryPart);
                            $data['id'] = base64_encode($cat->Id . '_' . $categoryPart . '_' . $counter);
                            $data['name'] = implode('|', $queue);
                            $data['category']['id'] = base64_encode($cat->Id . '_'. $categoryPart);
                            $data['language'] = [
                                "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                            ];
                            $this->batchData['requests'][] = [
                                'method' => 'POST',
                                'uri' => 'http://miskolczicsego.api.shoprenter.hu/categoryDescriptions/' . base64_encode($category->Id . '_' . $categoryPart . '_' . $counter),
                                'data' => $data
                            ];
                            ++$counter;
                        }
                        return;
                    }

                    $categoryIds[$cat->Id] = 1;
                    $data['id'] = base64_encode($cat->Id . $cat->Name);
                    $data['name'] = $cat->Name;
                    $data['category']['id'] = base64_encode($cat->Id);
                    $data['language'] = [
                        "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                    ];
                    $this->batchData['requests'][] = [
                        'method' => 'POST',
                        'uri' => 'http://miskolczicsego.api.shoprenter.hu/categoryDescriptions/' . base64_encode($cat->Id . $cat->Name ),
                        'data' => $data
                    ];
                }
            }
       }

        if (!is_array($category)) {
            if(!array_key_exists($category->Id, $this->categoryIds)) {
                $this->categoryIds[$category->Id] = 1;
                $categoryParts = explode('|', $category->Name);
                if(count($categoryParts) > 1) {
                    $counter = 0;
                    foreach ($categoryParts as $categoryPart) {
                        array_push($queue, $categoryPart);
                        $data['id'] = base64_encode($category->Id . '_' . $categoryPart . '_' . $counter);
                        $data['name'] = implode('|', $queue);
                        $data['category']['id'] = base64_encode($category->Id . '_'. $categoryPart);
                        $data['language'] = [
                            "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                        ];
                        $this->batchData['requests'][] = [
                            'method' => 'POST',
                            'uri' => 'http://miskolczicsego.api.shoprenter.hu/categoryDescriptions/' . base64_encode($category->Id . $category->Name),
                            'data' => $data
                        ];
                        ++$counter;
                    }
                    return;
                }
            }
            $data['id'] = base64_encode($category->Id . $category->Name);
            $data['name'] = $category->Name;
            $data['category']['id'] = base64_encode($category->Id);
            $data['language'] = [
                "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
            ];
            $this->batchData['requests'][] = [
                'method' => 'POST',
                'uri' => 'http://miskolczicsego.api.shoprenter.hu/categoryDescriptions/' . base64_encode($category->Id . $category->Name),
                'data' => $data
            ];
        }
    }

    public function getOuterId($data)
    {
        // TODO: Implement getOuterId() method.
    }
}