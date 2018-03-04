<?php

namespace App\Domain\GitHosts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Domain\RequestInterface\RequestInterface;

class Bitbucket extends RequestInterface
{
    /**
     * Send a search request to the Bitbucket's API and return structured data.
     *
     * @param Request $request
     * @return \App\Domain\RequestInterface\ResponseStructure
     */
    public function makeRequest(Request $request)
    {
        if($request->order === 'desc')
        {
            $request['sortBy'] = '-'.$request->sortBy;
        }

        $searchResponse = $this->httpClient->get(
            "https://api.bitbucket.org/2.0/repositories/{$request->username}?page=$request->page&pagelen={$request->perPage}&q=(name~\"{$request->term}\" OR description~\"{$request->term}\")&sort={$request->sortBy}"
        );

        $this->total = json_decode($searchResponse->getBody())->size;

        $repositories = [];
        foreach(json_decode($searchResponse->getBody())->values as $repository)
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
            'author' => $searchRequestData->owner->username != null ? $searchRequestData->owner->username : '',
            'rating' => 0, // Not supported with Bitbucket
            'url' => $searchRequestData->links->self->href != null ? $searchRequestData->links->self->href : '',
            'created_at' => $searchRequestData->created_on != null ? $searchRequestData->created_on : '',
            'updated_at' => $searchRequestData->updated_on != null ? $searchRequestData->updated_on : '',
        ];
    }

    /**
     * Validate or set Bitbucket's specific request parameters.
     *
     * @param Request $request
     */
    public function setAndValidateHostSpecificRequestParameters(Request $request)
    {
        if($request->provider === 'Bitbucket')
        {
            if(!$request->has('sortBy'))
            {
                $request['sortBy'] = 'name';
            }
            Validator::make($request->all(), [
                'username' => 'required|string',
                'sortBy' => 'nullable|string|in:id,name,path,created_at,updated_at,last_activity_at'
            ])->validate();
        }
    }
}