<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FilesController extends Controller
{
    public function store(FileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $fileType = $this->getFileType($file->getClientOriginalName());

        if (!$fileType) {
            return response()->json(['error' => 'Invalid file'], Response::HTTP_BAD_REQUEST);
        }

        $brokerPayload = [
            'file' => $fileType,
            'headers' => [
                'uuid' => $request->get('uuid'),
            ],
            'payload' => file_get_contents($file->getRealPath())
        ];

        return response()->json([], 200);
    }

    protected function getFileType($file): string
    {
        switch ($file) {
            case 'composer.json':
                $fileType = 'COMPOSER_FILE';
                break;
            case 'package.json':
                $fileType = 'NPM_FILE';
                break;
        }

        return $fileType;
    }
}
