<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\TotalResponse;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Playground API",
 *     version="1.0.0",
 *     description="自由な実験的なコードを書くためのコントローラ"
 * )
 */
class PlaygroundController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/playground/sample",
     *     summary="サンプルAPI",
     *     description="これはお試し用のサンプルAPIです。",
     *     tags={"Playground"},
     *     @OA\Response(
     *         response=200,
     *         description="成功時のレスポンス",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This is a sample API response.")
     *         )
     *     )
     * )
     */
    public function sample(): JsonResponse
    {
        return response()->json(['message' => 'This is a sample API response.']);
    }

    /**
     * 作成日：2025/03/27
     * テーマ：continue に数値を指定することで、どのループに戻るかを制御できる
     * パターン：バグのあるコード例
     * 処理：３はスキップして合計値を計算する予定だが、continue に数値を指定していないため、foreach ループを抜けずに処理が継続されてしまう
     */
    public function buggyContinueLevelExample(): TotalResponse
    {
        $total = 0;
        $numberArray = [1, 2, 3, 4];
        foreach ($numberArray as $number) {
            switch ($number) {
                case 3:
                    // ↓を有効にするとPHPの構文エラーになり、「500 Internal Server Error」が返却される
                    // PHP 7.2以前は構文エラーにはならない。 7.4以降は構文エラーになる
                    // continue; // ここでforeachを抜けるはずが、処理が継続された。
                default:
                    break;
            }
            $total += $number; // $numberが"3"のときも加算されてしまう
        }

        return new TotalResponse($total);
    }

    /**
     * 作成日：2025/03/27
     * テーマ：「continue に数値を指定することで、どのループに戻るかを制御できる」
     * パターン：正しいコード例
     * 処理：３はスキップして合計値を計算する予定であるため、continue に数値を指定して外側のループ（foreach）に戻るように修正した
     */
    public function correctContinueLevelExample(): TotalResponse
    {
        $total = 0;
        $numberArray = [1, 2, 3, 4];
        foreach ($numberArray as $number) {
            switch ($number) {
                case 3:
                    continue 2; // 外側のループ（foreach）に戻ります。
                default:
                    break;
            }
            $total += $number;
        }

        return new TotalResponse($total);
    }
}
