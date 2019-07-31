<?php


namespace entities;


class PerformerList extends EntityList
{
    public function __construct()
    {
        parent::__construct(Performer::class);
    }
}