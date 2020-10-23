<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;

class FilesController extends Controller
{
    public function store(FileRequest $request)
    {
        $file = $request->file('file');

        return response()->json(['file_name' => $file->getClientOriginalName()], 200);
    }
}
