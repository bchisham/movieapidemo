<?php


namespace DB;


class Performers extends ReadWrite
{

    public function getByAPIPredicate(PredicateSet $predicateSet)
    {
        $sql = $this->getSqlPrefix();
        return $this->getByPredicateSet($sql, $predicateSet);
    }

    protected function getSqlPrefix()
    {
        return 'SELECT * FROM Performers WHERE ';
    }
}