<?php


namespace API;


use DB\MovieID;
use DB\Predicate;
use DB\PredicateSet;
use DB\Target;
use entities\Movie;
use entities\MovieList;

class Movies extends v1
{

    protected function getAPIMeta()
    {
        return [
            'movie_id' => ['type' => self::FIELD_TYPE_ID, 'label' => 'Movie ID'],
            'title' => ['type' => self::FIELD_TYPE_STRING, 'label' => 'Movie Title'],
            'release_date' => ['type' => self::FIELD_TYPE_DATE, 'label' => 'Release Date']
        ];
    }

    protected function doGet()
    {
        $fields = $this->getRequestAsFields();
        error_log(var_export($fields, true));
        $meta = $this->getAPIMeta();
        if (is_array($fields)) {
            if (!$this->validateRequestFields($fields, $meta)) {
                return false;
            }
            if (isset($fields['movie_id'])) {
                $movieID = new MovieID($fields['movie_id']);
                $movie = new Movie();
                $movie->loadByID($movieID);
                error_log(var_export($movie, true));
                $this->container['data']['movies'] = $movie->getContainer();

            } else {
                $predicates = [];
                foreach ($fields as $field => $value) {
                    $target = new Target($this->translateApiToDBType($meta[$field]['type'], $field));
                    $predicates[] = new Predicate($target, Predicate::OP_EQ, $value);
                }
                $predicateSet = new PredicateSet($predicates);
                $movies = new MovieList();
                $movies->loadByPredicateSet($predicateSet);
            }
        }
        return true;
    }

}