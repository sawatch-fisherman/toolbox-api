<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeFormat implements ValidationRule
{
    /**
     * バリデーションを実行
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('時刻は文字列で入力してください。');
            return;
        }

        // 通常の時刻形式（00:00～23:59）
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return;
        }

        // 日またぎ対応時刻形式（24:00～99:59）
        if (preg_match('/^([2-9][0-9]|1[0-9][0-9]):[0-5][0-9]$/', $value)) {
            return;
        }

        $fail('正しい時刻形式（HH:MM）で入力してください。');
    }
}
