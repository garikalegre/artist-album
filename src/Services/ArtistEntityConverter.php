<?php

namespace App\Services;

use App\DTO\ArtistDto;

class ArtistEntityConverter implements EntityConverter
{
    public function convert(array $parameters)
    {
        $artistDto = new ArtistDto();
        $artistDto->setLastName($parameters['last_name']);
        $artistDto->setFirstName($parameters['first_name']);

        return $artistDto;
    }
}
