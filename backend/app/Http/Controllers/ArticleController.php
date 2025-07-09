<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ArticleSearchQueryBuilder;
use App\Services\ElasticSearchService;

class ArticleController extends Controller
{
    protected $elasticSearchService;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticSearchService = $elasticSearchService;
    }

    public function search(Request $request)
    {
        $filters = $request->only(['q', 'source', 'category', 'date']);
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);


        $query = (new ArticleSearchQueryBuilder($filters, $page, $perPage))->build();

        $result = $this->elasticSearchService->search('articles', $query);

        return response()->json([
            'data' => $result['articles'],
            'meta' => [
                'total' => $result['total'],
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($result['total'] / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => ($page - 1) * $perPage + count($result['articles']),
            ]
        ]);
    }       
}
