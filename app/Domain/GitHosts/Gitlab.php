<?php

namespace App\Domain\GitHosts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Domain\RequestInterface\RequestInterface;

class Gitlab extends RequestInterface
{
    /**
     * Send a search request to the GitLab's API and return structured data.
     *
     * @param Request $request
     * @return \App\Domain\RequestInterface\ResponseStructure
     */
    public function makeRequest(Request $request)
    {
        $searchResponse = $this->httpClient->get('https://gitlab.com/api/v4/projects', [
            'query' => [
                'search' => $request->term,
                'order_by' => $request->sortBy,
                'sort' => $request->order,
                'per_page' => $request->per_page,
                'page' => $request->page
            ]
        ]);

        $repositories = [];
        foreach(json_decode($searchResponse->getBody()) as $repository)
        {
            $repositories[] = $this->structureRepositoryData($repository);
        }

        return $this->buildResponseData($request, $repositories);
    }

    /**
     * Structure the repository data to make it universal.
     *
     * @param $searchRequestData
     * @return array
     */
    protected function structureRepositoryData($searchRequestData)
    {
        return [
            'name' => $searchRequestData->name != null ? $searchRequestData->name : '',
            'full_name' => $searchRequestData->path_with_namespace != null ? $searchRequestData->path_with_namespace : '',
            'description' => $searchRequestData->description != null ? $searchRequestData->description : '',
            'author' => $searchRequestData->path_with_namespace != null ? explode("/", $searchRequestData->path_with_namespace)[0] : '',
            'rating' => $searchRequestData->star_count != null ? $searchRequestData->star_count : 0,
            'url' => $searchRequestData->web_url != null ? $searchRequestData->web_url : '',
            'created_at' => $searchRequestData->created_at != null ? $searchRequestData->created_at : '',
            'updated_at' => $searchRequestData->last_activity_at != null ? $searchRequestData->last_activity_at : '',
        ];
    }

    /**
     * Validate or set GitLab's specific request parameters.
     *
     * @param Request $request
     */
    public function setAndValidateHostSpecificRequestParameters(Request $request)
    {
        if(!$request->has('sortBy'))
        {
            $request['sortBy'] = 'name';
        }
        Validator::make($request->all(), [
            'sortBy' => 'string|in:id,name,path,created_at,updated_at,last_activity_at'
        ])->validate();
    }
}