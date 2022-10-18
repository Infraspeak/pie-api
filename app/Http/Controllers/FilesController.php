<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class FilesController extends Controller
{
    public function store(FileRequest $request): BaseResponse
    {
        $file = $request->file('file');
        $brokerType = $this->getBrokerType($file->getClientOriginalName());

        if (!$brokerType) {
            return response()->json(['error' => 'Invalid file'], BaseResponse::HTTP_BAD_REQUEST);
        }

        $brokerPayload = [
            'headers' => [
                'uuid' => $request->get('uuid'),
            ],
            'payload' => json_decode(file_get_contents($file->getRealPath()))
        ];

        Redis::publish($brokerType, json_encode($brokerPayload));

        return response()->noContent();
    }

    protected function getBrokerType($fileName)
    {
        $brokerType = null;
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
