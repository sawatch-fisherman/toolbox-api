<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * 自由な実験的なコードを書くためのコントローラ
 */
class PlaygroundController extends Controller
{
    /**
     * サンプルAPI
     */
    public function sample(): JsonResponse
    {
        return response()->json(['message' => 'This is a sample API response.']);
    }
}
