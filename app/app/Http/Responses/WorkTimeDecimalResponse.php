<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class WorkTimeDecimalResponse extends JsonResponse
{
    /**
     * WorkTimeDecimalResponse コンストラクタ
     *
     * @param  float  $hoursDecimal  勤務時間（小数点以下2桁 例: 8.75）
     * @param  int  $totalMinutes  勤務時間の合計（分）
     * @param  int  $status  HTTPステータスコード
     * @param  array  $headers  HTTPヘッダー
     * @param  int  $options  JSONエンコードオプション
     */
	public function __construct(float $hoursDecimal, int $totalMinutes, int $status = 200, array $headers = [], int $options = 0)
	{
		parent::__construct([
			'hours_decimal' => $hoursDecimal,
			'total_minutes' => $totalMinutes,
		], $status, $headers, $options);
	}
}
