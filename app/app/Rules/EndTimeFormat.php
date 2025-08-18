<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EndTimeFormat implements ValidationRule
{
    /**
     * バリデーションを実行
     * 終了時刻用：00:01～48:00
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('時刻は文字列で入力してください。');

            return;
        }

        // 時刻形式チェック（HH:MM）
        if (! preg_match('/^([0123]?[0-9]|4[0-8]):[0-5][0-9]$/', $value)) {
            $fail('正しい時刻形式（HH:MM）で入力してください。');

            return;
        }

        // 範囲チェック（00:01～48:00）
        $parts = explode(':', $value);
        $hours = intval($parts[0]);
        $minutes = intval($parts[1]);

        // 00:00は許可しない（最小値は00:01）
        if ($hours === 0 && $minutes === 0) {
            $fail('終了時刻は00:01以降で入力してください。');

            return;
        }

        // 48:00を超える場合は無効
        if ($hours > 48) {
            $fail('終了時刻は48:00までしか設定できません。');

            return;
        }

        // 48:00の場合、分は00のみ許可
        if ($hours === 48 && $minutes > 0) {
            $fail('48:00を超える時刻は設定できません。');

            return;
        }

        if ($minutes < 0 || $minutes > 59) {
            $fail('分は00～59の範囲で入力してください。');

            return;
        }
    }
}
