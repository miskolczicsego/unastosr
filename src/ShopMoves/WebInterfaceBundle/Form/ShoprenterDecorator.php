<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 13:00
 */

namespace ShopMoves\WebInterfaceBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;

class ShoprenterDecorator
{
    public function addSrFields(FormBuilder $form)
    {
        $form
            ->add("sr-username", TextType::class, [
                "data" => "dsadasda"
            ])
            ->add("sr-password", TextType::class, [
                "data" => "dsadasdasdsdasd"
            ]);
        return $form;
    }
}