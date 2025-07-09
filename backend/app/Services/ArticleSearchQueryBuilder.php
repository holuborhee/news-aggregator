<?php

namespace App\Services;

class ArticleSearchQueryBuilder
{
    protected array $filters;
    protected int $size;
    protected int $from;

    public function __construct(array $filters = [], int $page = 1, int $size = 10)
    {
        $this->filters = $filters;
        $this->size = $size;
        $this->from = ($page - 1) * $size;
    }

    public function build(): array
    {
        $must = [];

        if (!empty($this->filters['q'])) {
            $must[] = [
                'multi_match' => [
                    'query' => $this->filters['q'],
                    'fields' => ['title', 'description', 'content'],
                ],
            ];
        }

        if (!empty($this->filters['source'])) {
            $must[] = ['term' => ['source.slug' => $this->filters['source']]];
        }

        if (!empty($this->filters['category'])) {
            $must[] = [
                'nested' => [
                    'path' => 'categories',
                    'query' => [
                        'term' => ['categories.slug' => $this->filters['category']],
                    ]
                ]
            ];
        }

        if (!empty($this->filters['date'])) {
            $must[] = [
                'range' => [
                    'published_at' => [
                        'gte' => $this->filters['date'] . 'T00:00:00',
                        'lte' => $this->filters['date'] . 'T23:59:59',
                    ]
                ]
            ];
        }

        $query = [
            'from' => $this->from,
            'size' => $this->size,
            'sort' => [['published_at' => ['order' => 'desc']]],
            'query' => [
                'bool' => ['must' => $must],
            ],
        ];

        return $query;
    }
}
