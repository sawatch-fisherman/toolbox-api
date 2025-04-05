<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class TotalResponse extends JsonResponse
{
    /**
     * TotalResponse コンストラクタ
     *
     * @param  int  $total  合計値
     * @param  int  $status  HTTPステータスコード
     * @param  array  $headers  HTTPヘッダー
     * @param  int  $options  JSONエンコードオプション
     */
    public function __construct(int $total, int $status = 200, array $headers = [], int $options = 0)
    {
        $data = ['total' => $total];
        parent::__construct($data, $status, $headers, $options);
    }
}
