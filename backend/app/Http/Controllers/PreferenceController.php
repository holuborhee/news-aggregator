<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ElasticSearchService;

class PreferenceController extends Controller
{
    protected $elasticSearchService;

    public function __construct(ElasticSearchService $elasticSearchService)
    {
        $this->elasticSearchService = $elasticSearchService;
    }

    // GET /api/v1/user/preferences
    public function show()
    {
        $userId = Auth::id();

        $response = $this->elasticSearchService->getPreference($userId);

        return response()->json([
            'preferences' => $response ?? [
                'categories' => [],
                'sources'    => [],
                'authors'    => [],
            ]
        ]);
    }

    // POST /api/v1/user/preferences
    public function update(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'string|exists:categories,slug',

            'sources' => 'nullable|array',
            'sources.*' => 'string',

            'authors' => 'nullable|array',
            'authors.*' => 'string',
        ]);

        $userId = Auth::id();

        $this->elasticSearchService->savePreference($userId, $validated);

        return response()->json([
            'message' => 'Preferences updated successfully.',
        ]);
    }
}
