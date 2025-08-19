<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateWorkTimeRequest;
use App\Http\Responses\WorkTimeResponse;
use App\Services\WorkTimeCalculationService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Work Time Calculation API",
 *     version="1.0.0",
 *     description="勤務時間計算API"
 * )
 */
class WorkTimeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/calculate-work-time",
     *     summary="勤務時間計算",
     *     description="開始時刻、終了時刻、休憩時間から勤務時間を計算します",
     *     tags={"Work Time"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"start_time","end_time","break_time"},
     *
     *             @OA\Property(property="start_time", type="string", example="09:00", description="開始時刻（HH:MM形式）"),
     *             @OA\Property(property="end_time", type="string", example="28:00", description="終了時刻（HH:MM形式、日またぎ可）"),
     *             @OA\Property(property="break_time", type="string", example="01:00", description="休憩時間（HH:MM形式）"),
     *             @OA\Property(property="rounding_unit", type="integer", nullable=true, example=null, description="丸め単位（分）")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="成功時のレスポンス",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="hours", type="integer", example=19, description="勤務時間（時間単位）"),
     *             @OA\Property(property="minutes", type="integer", example=0, description="勤務時間（分単位）"),
     *             @OA\Property(property="total_minutes", type="integer", example=1140, description="勤務時間の合計（分）")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="バリデーションエラー",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function calculateWorkTime(CalculateWorkTimeRequest $request): JsonResponse
    {
        try {
            // バリデーション済みデータを取得
            $validated = $request->validated();

            // サービスクラスを使用して計算
            $service = new WorkTimeCalculationService;
            $result = $service->calculateWorkTime(
                $validated['start_time'],
                $validated['end_time'],
                $validated['break_time'],
                $validated['rounding_unit'] ?? null
            );

            return new WorkTimeResponse($result['hours'], $result['minutes'], $result['total_minutes']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '計算処理中にエラーが発生しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
