<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeFormat implements ValidationRule
{
    /**
     * バリデーションを実行
     * 開始時刻用：00:00～23:59
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('時刻は文字列で入力してください。');

            return;
        }

        // 時刻形式チェック（HH:MM）
        if (! preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            $fail('正しい時刻形式（HH:MM）で入力してください。');

            return;
        }

        // 範囲チェック（00:00～23:59）
        $parts = explode(':', $value);
        $hours = intval($parts[0]);
        $minutes = intval($parts[1]);

        if ($hours < 0 || $hours > 23) {
            $fail('時刻は00:00～23:59の範囲で入力してください。');

            return;
        }

        if ($minutes < 0 || $minutes > 59) {
            $fail('分は00～59の範囲で入力してください。');

            return;
        }
    }
}
