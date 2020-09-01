<?php

require __DIR__ . '/vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class MSGraphUser
{
    private $accessToken;
    private $user;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getUser()
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        return $graph->createRequest("GET", "/me")
                      ->setReturnType(Model\User::class)
                      ->execute();
    }

    public function getGroups()
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        return $graph->createRequest("GET", "/me/memberOf")
                      ->setReturnType(Model\Group::class)
                      ->execute();
    }

    public function getRoles()
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $groups = $graph->createRequest("GET", "/me/memberOf")
                      ->setReturnType(Model\Group::class)
                      ->execute();

        foreach ($groups as $group) {
            $returnArray[] = $group->getDisplayName();
        }
        return $returnArray;
    }
}
