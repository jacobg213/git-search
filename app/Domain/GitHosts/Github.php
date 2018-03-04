<?php

namespace App\Domain\GitHosts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Domain\RequestInterface\RequestInterface;

class Github extends RequestInterface
{
    /**
     * Send a search request to the GitHub's API and return structured data.
     *
     * @param Request $request
     * @return \App\Domain\RequestInterface\ResponseStructure
     */
    public function makeRequest(Request $request)
    {
        $searchResponse = $this->httpClient->get('https://api.github.com/search/repositories', [
            'query' => [
                'q' => $request->term,
                'sort' => $request->sortBy,
                'order' => $request->order,
                'per_page' => $request->perPage,
                'page' => $request->page
            ]
        ]);

        $this->total = json_decode($searchResponse->getBody())->total_count;

        $repositories = [];
        foreach(json_decode($searchResponse->getBody())->items as $repository)
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
            'full_name' => $searchRequestData->full_name != null ? $searchRequestData->full_name : '',
            'description' => $searchRequestData->description != null ? $searchRequestData->description : '',
            'author' => $searchRequestData->owner->login != null ? $searchRequestData->owner->login : '',
            'rating' => $searchRequestData->stargazers_count != null ? $searchRequestData->stargazers_count : 0,
            'url' => $searchRequestData->url != null ? $searchRequestData->url : '',
            'created_at' => $searchRequestData->created_at != null ? $searchRequestData->created_at : '',
            'updated_at' => $searchRequestData->updated_at != null ? $searchRequestData->updated_at : '',
        ];
    }

    /**
     * Validate or set GitHub's specific request parameters.
     *
     * @param Request $request
     */
    public function setAndValidateHostSpecificRequestParameters(Request $request)
    {
        if(!$request->has('sortBy'))
        {
            $request['sortBy'] = 'stars';
        }
        Validator::make($request->all(), [
            'sortBy' => 'string|in:stars,forks,updated'
        ])->validate();
    }
}