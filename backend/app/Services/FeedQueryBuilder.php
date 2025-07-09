<?php

namespace App\Services;

class FeedQueryBuilder
{
    protected array $preferences;
    protected int $size;
    protected int $from;

    public function __construct(array $preferences, int $page = 1, int $size = 10)
    {
        $this->preferences = $preferences;
        $this->size = $size;
        $this->from = ($page - 1) * $size;
    }

    public function build(): array
    {
        $should = [];

        if (!empty($this->preferences['sources'])) {
            $should[] = [
                "terms" => [
                    "source.slug" => $this->preferences['sources'],
                    "boost" => 3
                ]
            ];
        }

        if (!empty($this->preferences['categories'])) {
            $should[] = [
                "nested" => [
                    "path" => "categories",
                    "query" => [
                        "terms" => [
                            "categories.slug" => $this->preferences['categories']
                        ]    
                    ],
                    "boost" => 2
                ]
            ];
        }

        if (!empty($this->preferences['authors'])) {
            $should[] = [
                "terms" => [
                    "author.slug" => $this->preferences['authors'],
                    "boost" => 1
                ]
            ];
        }

        return [
            "query" => [
                "bool" => [
                    "should" => $should,
                    "minimum_should_match" => 1
                ]
            ],
            "sort" => [[ "published_at" => "desc" ]],
            "from" => $this->from,
            "size" => $this->size
        ];
    }
}
