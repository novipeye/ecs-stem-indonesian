<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StemmerService;

class StemController extends Controller
{
    public function index()
    {
        return view('stem.form');
    }

    public function process(Request $request, StemmerService $stemmerService)
    {
        $request->validate([
            'word' => [
                'required',
                'string',
                'min:4',
                'regex:/^[a-zA-Z\-]+$/'
            ],
        ], [
            'min' => 'Minimal word length is four characters.',
            'regex' => 'Only one word with alphabetic characters is allowed.',
        ]);
        $result = $stemmerService->stem($request->input('word'));
        return view('stem.form', [
            'original' => $request->input('word'),
            'root' => $result['root'],
            'status' => $result['status'],
        ]);
    }
}
