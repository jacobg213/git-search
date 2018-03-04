<?php

namespace App\Domain\RequestInterface;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

abstract class RequestInterface
{
    protected $httpClient;
    protected $total;
    private $repositories = [];

    /**
     * Instantiate new Guzzle client on construct.
     *
     * RequestInterface constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Git host request method
     *
     * This method should use the $httpClient to make
     * a request to your Git provider and then
     * call the structureRepositoryData function
     * for each repository found and return
     * the buildResponseData function.
     *
     * @access public
     * @param Illuminate\Http\Request $request
     */
    abstract public function makeRequest(Request $request);

    /**
     * Structure Git host response data
     *
     * This method should structure your Git host response data to match the requirements
     * of the App\Domain\RequestInterface\Repository class.
     *
     * @access protected
     * @param $searchRequestData
     * @return void
     */
    abstract protected function structureRepositoryData($searchRequestData);

    /**
     * Set any required request parameters specific for your Git host.
     *
     * @access public
     * @param Request $request
     * @return void
     */
    abstract public function setHostSpecificRequestParameters(Request $request);

    /**
     * Structure the data to make it ready for the final response.
     *
     * @param $request
     * @param $repositoriesFound
     * @return ResponseStructure
     */
    public function buildResponseData($request, $repositoriesFound)
    {
        foreach($repositoriesFound as $found)
        {
            $this->repositories[] = new Repository(
                $found['name'],
                $found['full_name'],
                $found['description'],
                $found['author'],
                $found['rating'],
                $found['url'],
                $found['created_at'],
                $found['updated_at']
            );
        }

        return new ResponseStructure(
            $request->per_page,
            $request->page,
            $request->sortBy,
            $request->order,
            $request->term,
            $this->repositories,
            $this->total
        );
    }
}