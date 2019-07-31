<?php


namespace entities;


use DB\Movies;

class Movie extends Entity
{
    const FIELD_ID = 'movie_id';
    const FIELD_TITLE = 'title';
    const FIELD_RELEASE_DATE = 'release_date';

    public function getDBHandle()
    {
        static $db = null;
        if (!isset($db)) {
            $db = new Movies();
        }
        return $db;
    }
}