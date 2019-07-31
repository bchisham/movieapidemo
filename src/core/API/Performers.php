<?php


namespace API;


use DB\PerformerID;
use DB\Predicate;
use DB\PredicateSet;
use DB\Target;
use entities\Performer;
use entities\PerformerList;

class Performers extends v1
{

    protected function getAPIMeta()
    {
        return [
            'performer_id' => ['type' => self::FIELD_TYPE_ID, 'label' => 'Performer ID'],
            'name' => ['type' => self::FIELD_TYPE_STRING, 'label' => 'Name'],
            'birth_date' => ['type' => self::FIELD_TYPE_DATE, 'label' => 'Birth Date']
        ];
    }

    protected function doGet()
    {
        $fields = $this->getRequestAsFields();
        $meta = $this->getAPIMeta();
        if (is_array($fields)) {
            if (!$this->validateRequestFields($fields, $meta)) {
                return false;
            }
            if (isset($fields['performer_id'])) {
                $performerID = new PerformerID($fields['performer_id']);
                $performer = new Performer();
                $performer->loadByID($performerID);
                $this->container['data']['performers'] = [iterator_to_array($performer)];

            } else {
                $predicates = [];
                foreach ($fields as $field => $value) {
                    $target = new Target($this->translateApiToDBType($meta[$field]['type'], $field));
                    $predicates[] = new Predicate($target, Predicate::OP_EQ, $value);
                }
                $predicateSet = new PredicateSet($predicates);
                $performers = new PerformerList();
                $performers->loadByPredicateSet($predicateSet);
            }
        }

        return true;
    }
}