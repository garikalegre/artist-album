<?php

namespace App\Services;

use App\DTO\AlbumDto;

class AlbumEntityConverter implements EntityConverter
{

    public function convert(array $params)
    {
        $albumDto = new AlbumDto();
        $albumDto->setName($params['name']);
        $albumDto->setDescription($params['description']);
        $albumDto->setArtist($params['artist']);

        return $albumDto;
    }
}
