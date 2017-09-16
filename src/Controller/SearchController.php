<?php

namespace DailyDouble\Controller;

use DailyDouble\Action\SearchAction;
use Interop\Container\ContainerInterface;

class SearchController extends BaseController
{
    public function search($request, $response, $args)
    {
        if (empty($request->getAttribute('term')) === true) {
            $response->write(json_encode(false));

            return $response;
        }

        $search = new SearchAction($this->container->db);

        $results = $search->getSongs($request->getAttribute('term'));

        $response->write(json_encode($results));

        return $response;
    }
}