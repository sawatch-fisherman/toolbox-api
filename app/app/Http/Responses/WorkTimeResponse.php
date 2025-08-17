<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class WorkTimeResponse extends JsonResponse
{
    /**
     * WorkTimeResponse コンストラクタ
     *
     * @param  int  $hours  勤務時間（時間単位）
     * @param  int  $minutes  勤務時間（分単位）
     * @param  int  $totalMinutes  勤務時間の合計（分）
     * @param  int  $status  HTTPステータスコード
     * @param  array  $headers  HTTPヘッダー
     * @param  int  $options  JSONエンコードオプション
     */
    public function __construct(int $hours, int $minutes, int $totalMinutes, int $status = 200, array $headers = [], int $options = 0)
    {
        $data = [
            'hours' => $hours,
            'minutes' => $minutes,
            'total_minutes' => $totalMinutes
        ];
        parent::__construct($data, $status, $headers, $options);
    }
}

