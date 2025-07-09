<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedQueryBuilder;
use App\Services\ElasticSearchService;

class FeedController extends Controller
{
    protected $elasticSearchService;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticSearchService = $elasticSearchService;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $pref = $this->elasticSearchService->getPreference($userId) ?? [];

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);


        $query = (new FeedQueryBuilder($pref ?? [], $page, $perPage))->build();

        $result = $this->elasticSearchService->search('articles', $query);

        return response()->json([
            'pref' => $pref,
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
