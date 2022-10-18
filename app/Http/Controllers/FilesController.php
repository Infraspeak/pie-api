<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class FilesController extends Controller
{
    public function store(FileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $brokerType = $this->getBrokerType($file->getClientOriginalName());

        if (!$brokerType) {
            return response()->json(['error' => 'Invalid file'], Response::HTTP_BAD_REQUEST);
        }

        $brokerPayload = [
            'headers' => [
                'uuid' => $request->get('uuid'),
            ],
            'payload' => json_decode(file_get_contents($file->getRealPath()))
        ];

        Redis::publish($brokerType, json_encode($brokerPayload));

        return response()->json([], 200);
    }

    protected function getBrokerType($fileName)
    {
        switch ($fileName) {
            case 'composer.json':
                $brokerType = 'COMPOSER_FILE';
                break;
            case 'package.json':
                $brokerType = 'NPM_FILE';
                break;
            default:
                $brokerType = null;
        }

        return $brokerType;
    }
}
