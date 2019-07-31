<?php


namespace entities;


class MovieList extends EntityList
{
    public function __construct()
    {
        parent::__construct(MovieList::class);
    }
}