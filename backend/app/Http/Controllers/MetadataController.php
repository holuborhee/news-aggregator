<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ElasticSearchService;

class MetadataController extends Controller
{
    protected $elastic;

    public function __construct(ElasticSearchService $elastic)
    {
        $this->elastic = $elastic;
    }

    public function all()
    {
        // Fetch categories from MySQL
        $categories = Category::select('slug', 'name')->get();

        // Fetch sources & authors from Elasticsearch
        $sources = $this->elastic->getAllSources();
        $authors = $this->elastic->getAllAuthors();

        return response()->json([
            'categories' => $categories,
            'sources'    => $sources,
            'authors'    => $authors,
        ]);
    }
}
