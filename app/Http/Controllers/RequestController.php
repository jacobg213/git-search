<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Send a request to the specified Git host.
     * Return universally structured search results.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $this->checkBaseRequestParameters($request);
        $this->validate($request, ['provider' => 'required|in:Gitlab,Github,Bitbucket']);

        $hostClass = 'App\Domain\GitHosts\\' . $request->provider;
        $host = new $hostClass;
        $host->setAndValidateHostSpecificRequestParameters($request);

        $this->validate($request, [
            'term' => 'required|string',
            'sortBy' => 'required',
            'order' => 'required|in:asc,desc',
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer'
        ]);

        $data = $host->makeRequest($request);

        return response()->json($data);
    }

    /**
     * Check if the pagination and order parameters are within the request.
     * Set the parameters to the defaults if not present.
     *
     * @param $request
     */
    private function checkBaseRequestParameters($request)
    {
        if(!$request->has('per_page'))
        {
            $request['per_page'] = 25;
        }
        if(!$request->has('page'))
        {
            $request['page'] = 1;
        }
        if(!$request->has('order'))
        {
            $request['order'] = 'desc';
        }
    }
}
