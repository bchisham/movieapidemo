<?php


namespace API;

class v1 extends API
{

    private $links;

    public function __construct()
    {
        parent::__construct();
        $this->container['links'] = [];
    }

    protected function doGet()
    {
        foreach ($this->getEndpointList() as $name => $epInfo) {
            $epKey = '/api/v1/' . $name;
            $this->container['links'][$epKey] = $epInfo['name'];
        }
        return true;
    }

    public function getEndpointList()
    {
        return [
            'movies' => [
                'name' => 'Movies',
                'class' => Movies::class,
            ],
            'performers' => [
                'name' => 'Performers',
                'class' => Performers::class,
            ]
        ];
    }

    public function getEndpoint($endpoint)
    {
        $listing = $this->getEndpointList();
        if (in_array($endpoint, $listing, true)) {
            $className = $listing[$endpoint];
            $instance = new $className();
        } else {
            $instance = null;
        }
        return $instance;
    }


}