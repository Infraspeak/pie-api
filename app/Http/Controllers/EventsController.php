<?php

namespace App\Http\Controllers;

use App\Events\ParsedFile;

class EventsController extends Controller
{
    public function index(): string
    {
        $payload = [
            'repository' => [
                'name' => 'name',
                'url' => 'repo url'
            ],
            'issues' => [
                [
                    'url' => '',
                    'title' => '',
                    'description' => '',
                    'author' => '',
                    'status' => 'open/closed',
                    'tags' => [
                        'hacktoberfest',
                        'cheaters'
                    ],
                    'id' => '',
                    'date_opened' => ''
                ]
            ]
        ];

        broadcast(new ParsedFile($payload))->toOthers();

        return 'sent';
    }
}
