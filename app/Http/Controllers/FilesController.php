<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;

class FilesController extends Controller
{
    public function store(FileRequest $request)
    {
        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());

        return response()->json([], 200);
    }
}
