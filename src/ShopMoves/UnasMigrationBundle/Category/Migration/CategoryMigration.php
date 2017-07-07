<?php

namespace ShopMoves\UnasMigrationBundle\Category\Migration;

use ShopMoves\UnasMigrationBundle\Api\ApiCall;
use ShopMoves\UnasMigrationBundle\Category\Provider\CategoryDataProvider;
use ShopMoves\UnasMigrationBundle\Migration\BatchMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.05.
 * Time: 13:47
 */
class CategoryMigration extends BatchMigration
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

        //több kategória is van a kategóriákon belül => egy termék több kategóriába is tartozik
        if(is_array($category)) {
            //menjünk végig, tároljuk el ami még nem volt, és rakjuk be a batch tömbbe az id-kat
            foreach ($category as $cat) {
                if(!array_key_exists($cat->Id, $this->categoryIds)) {
                    $this->categoryIds[$cat->Id] = 1;
                    $categoryParts = explode('|', $cat->Name);
                    //el van választva | jellel? ha igen akkor szülő gyerek-ként kell felépíteni
                    if(count($categoryParts) > 1) {
                        $counter = 0;
                        foreach ($categoryParts as $categoryPart) {

                            array_push($queue, $categoryPart);

                            $data['id'] = base64_encode($cat->Id . '_'. implode('|', $queue));

//                            if ($counter > 0) {
//                                $data['parentCategory']['id'] = base64_encode($cat->Id . '_'. $categoryParts[0]);
//                            }

                            $descriptionData['id'] = base64_encode($cat->Id . $categoryPart);
                            $descriptionData['name'] = $categoryPart;
                            $descriptionData['category']['id'] = base64_encode($cat->Id . '_'. $categoryPart);
                            $descriptionData['language'] = [
                                "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                            ];


                            $this->batchData['requests'][] = [
                                'method' => 'POST',
                                'uri' => 'http://demo.api.aurora.miskolczicsego/categories/' .  base64_encode($cat->Id . '_'. $categoryPart),
                                'data' => $data
                            ];

                            $this->batchData['requests'][] = [
                                'method' => 'POST',
                                'uri' => 'http://demo.api.aurora.miskolczicsego/categoryDescription/' .  base64_encode($cat->Id . $categoryPart),
                                'data' => $descriptionData
                            ];
                            ++$counter;
                        }
                    } else {
                        $outerId = $this->getOuterId($cat);
                        $categoryIds[$cat->Id] = 1;

                        $data['id'] = $outerId;

                        $descriptionData['id'] = base64_encode($category->Id . $category->Name);
                        $descriptionData['name'] = $category->Name;
                        $descriptionData['category']['id'] = base64_encode($category->Id);
                        $descriptionData['language'] = [
                            "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                        ];

                        $this->batchData['requests'][] = [
                            'method' => 'POST',
                            'uri' => 'http://demo.api.aurora.miskolczicsego/categories/' . $outerId,
                            'data' => $data
                        ];


                        $this->batchData['requests'][] = [
                            'method' => 'POST',
                            'uri' => 'http://demo.api.aurora.miskolczicsego/categoryDescriptions/' . base64_encode($category->Id . $category->Name),
                            'data' => $descriptionData
                        ];
                    }
                }
            }
        }
        //ha csak egy kategória van a felsorolásban
        if(!is_array($category)) {
            //ha még nem találkoztunk ilyennel akkor mentsük el
            if(!array_key_exists($category->Id, $this->categoryIds)) {
                $this->categoryIds[$category->Id] = 1;
                $categoryParts = explode('|', $category->Name);
                if(count($categoryParts) > 1) {
                    $counter = 0;
                    foreach ($categoryParts as $categoryPart) {
                        array_push($queue, $categoryPart);
                        $data['id'] = base64_encode($category->Id . '_'. $categoryPart);

                        $descriptionData['id'] = base64_encode($category->Id . $categoryPart);
                        $descriptionData['name'] = $categoryPart;
                        $descriptionData['category']['id'] = base64_encode($category->Id . '_'. $categoryPart);
                        $descriptionData['language'] = [
                            "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                        ];

//                        if ($counter > 0) {
//                            $data['parentCategory']['id'] = base64_encode($category->Id . '_'. $categoryParts[0]);
//                        }


                        $this->batchData['requests'][] = [
                            'method' => 'POST',
                            'uri' => 'http://demo.api.aurora.miskolczicsego/categories/' . base64_encode($category->Id . '_'. $categoryPart),
                            'data' => $data
                        ];

                        $this->batchData['requests'][] = [
                            'method' => 'POST',
                            'uri' => 'http://demo.api.aurora.miskolczicsego/categoryDescriptions/' . base64_encode($category->Id . $category->Name),
                            'data' => $descriptionData
                        ];
                        ++$counter;
                    };
                } else {
                    $outerId = $this->getOuterId($category);
                    $this->categoryIds[$category->Id] = 1;
                    $data['id'] = $outerId;

                    $descriptionData['id'] = base64_encode($category->Id . $category->Name);
                    $descriptionData['name'] = $category->Name;
                    $descriptionData['category']['id'] = base64_encode($category->Id);
                    $descriptionData['language'] = [
                        "id" => 'bGFuZ3VhZ2UtbGFuZ3VhZ2VfaWQ9MQ=='
                    ];

                    $this->batchData['requests'][] = [
                        'method' => 'POST',
                        'uri' => 'http://demo.api.aurora.miskolczicsego/categories/' . $outerId,
                        'data' => $data
                    ];


                    $this->batchData['requests'][] = [
                        'method' => 'POST',
                        'uri' => 'http://demo.api.aurora.miskolczicsego/categoryDescriptions/' . base64_encode($category->Id . $category->Name),
                        'data' => $descriptionData
                    ];
                }
            }
        }
    }

    public function getOuterId($category)
    {
        return base64_encode($category->Id);
    }

    public function getDeeperOuterId($queue)
    {
        $id = implode('|', $queue);
        return base64_encode($id);
    }
}