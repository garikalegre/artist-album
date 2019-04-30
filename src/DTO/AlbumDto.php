<?php

namespace App\DTO;

use App\Entity\Artist;

final class AlbumDto
{
    private $name;
    private $description;
    private $artist;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * @param mixed $artist
     */
    public function setArtist(Artist $artist): void
    {
        $this->artist = $artist;
    }
}
