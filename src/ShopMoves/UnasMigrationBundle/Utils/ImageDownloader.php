<?php
/**
 * Created by PhpStorm.
 * User: miskolczicsego
 * Date: 2017.07.10.
 * Time: 10:41
 */

namespace ShopMoves\UnasMigrationBundle\Utils;


class ImageDownloader
{
    public function getFileEncodedContent($url)
    {
        $content = base64_encode(@file_get_contents($url));

        return $content;
    }
}