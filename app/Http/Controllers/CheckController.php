<?php

namespace App\Http\Controllers;

use App\Services\ParseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request): JsonResponse
    {
        $parser = new ParseService();

        return $parser->store($request);
    }


    public function show()
    {
        $data = file_get_contents('har/check.har');
        return view('show', compact('data'));
    }
}
