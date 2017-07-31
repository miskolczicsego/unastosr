<?php

namespace ShopMoves\UnasMigrationBundle\Helper;
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.04.
 * Time: 10:04
 */
class NameHelper
{
    /**
     * Mapping logika, ha a név 1 szóból áll
    Akkor a vezetéknévhez kell tölteni a nevet. (ha kötelező a keresztnév feltöltése akkor egy kötőjel karakter legyen)
    Mapping logika, ha a név 2 szóból áll
    Az első a vezetéknév, a második pedig a keresztnév.
    Mapping logika, ha a név több mint 2 szóból áll
    Ha az első vagy a második szó "né"-vel végződik, akkor a vezetéknév az első 2 szó legyen.
    Ha az első és a második szó között egy "-" van, akkor a vezetéknév az első 2 szó legyen.
    Ha az első vagy második szó "dr" vagy "dr.", vagy az első 2 szó valamelyikének karakter hossza kevesebb mint 2, akkor a vezetéknév legyen az első 2 szó.
    Ha a névben szerepel a "kft", a "zrt", az "alapítvány", az "egyesület", az "önk.", az "önkormányzat", az "iroda", a "bt.", az "iskola", az "isk." vagy az "óvod" szó, akkor a teljes név legyen vezetéknévként feltöltve.
    Ha a fentiek egyike sem teljesül, akkor az első szó a vezetéknév, a többi pedig a keresztnév.
     */

    public function separate($name)
    {
        //TODO: ezeket majd ki lehetne váltani regexxel
        $data = explode(' ', $name);
        $nameParts = [];
        //Csegő
        if (count($data) === 1) {
            $nameParts['firstname'] = $data[0];
            $nameParts['lastname'] = '-';
        }
        //Miskolczi Csegő || Miskolczi-Kis Csegő
        if (count($data) === 2) {
            $nameParts['firstname'] = $data[0];
            $nameParts['lastname'] = $data[1];
        }
        //Miskolczi Csegő Dániel || Miskolcziné Barna Dorina || M Csegő Dániel || dr. Miskolczi Csegő
        if(count($data) > 2) {
            if(substr($data[0] ,-2) == 'né' ||
                substr($data[1] ,-2) == 'né' ||
                $data[0] === 'dr.' ||
                $data[1] === 'dr' ||
                $data[0] === 'dr' ||
                $data[1] === 'dr.' ||
                count($data[0]) < 2 ||
                count($data[1]) < 2) {
                $nameParts['firstname'] = $data[0] . ' ' . $data[1];
                $nameParts['lastname'] = $data[2];
            }

        }
        return $nameParts;
    }
}