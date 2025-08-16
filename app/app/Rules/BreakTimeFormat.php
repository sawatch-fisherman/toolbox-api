<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BreakTimeFormat implements ValidationRule
{
    /**
     * バリデーションを実行
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('休憩時間は文字列で入力してください。');
            return;
        }

        // 休憩時間は通常の時刻形式のみ（00:00～23:59）
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            $fail('正しい時刻形式（HH:MM）で入力してください。');
            return;
        }

        // 休憩時間が24時間を超える場合は無効
        $parts = explode(':', $value);
        $hours = intval($parts[0]);
        
        if ($hours >= 24) {
            $fail('休憩時間は24時間未満で入力してください。');
        }
    }
}
