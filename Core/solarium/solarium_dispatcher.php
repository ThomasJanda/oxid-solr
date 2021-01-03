<?php

namespace rs\solr\Core\solarium;

class solarium_dispatcher implements \Psr\EventDispatcher\EventDispatcherInterface
{
    
    public function dispatch(object $event): object {
        return $event;
    }

}