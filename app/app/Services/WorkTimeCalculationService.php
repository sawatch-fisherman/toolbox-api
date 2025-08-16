<?php

namespace App\Services;

use Carbon\Carbon;

class WorkTimeCalculationService
{
    /**
     * 勤務時間を計算
     */
    public function calculateWorkTime(string $startTime, string $endTime, string $breakTime, ?int $roundingUnit = null): array
    {
        // 時刻をCarbonオブジェクトに変換
        $startCarbon = $this->timeStringToCarbon($startTime);
        $endCarbon = $this->timeStringToCarbon($endTime);
        $breakCarbon = $this->timeStringToCarbon($breakTime);

        // 勤務時間を計算（分単位）
        $workMinutes = $this->calculateWorkMinutes($startCarbon, $endCarbon, $breakCarbon);

        // 丸め処理
        if ($roundingUnit !== null) {
            $workMinutes = $this->roundMinutes($workMinutes, $roundingUnit);
        }

        // 時間と分に分解
        $hours = intval($workMinutes / 60);
        $minutes = $workMinutes % 60;

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'total_minutes' => $workMinutes
        ];
    }

    /**
     * 時刻文字列をCarbonオブジェクトに変換
     * 終了時刻は最大48:00まで対応
     */
    private function timeStringToCarbon(string $time): Carbon
    {
        $parts = explode(':', $time);
        $hours = intval($parts[0]);
        $minutes = intval($parts[1]);

        // 基準日を設定（今日の日付）
        $baseDate = Carbon::today();

        // 24時間を超える場合（日またぎ）の処理
        if ($hours >= 24) {
            $hours = $hours - 24;
            $baseDate = $baseDate->addDay();
        }

        return $baseDate->setTime($hours, $minutes, 0);
    }

    /**
     * 終了時刻の48:00制限をチェック
     */
    private function validateEndTimeLimit(string $endTime): bool
    {
        $parts = explode(':', $endTime);
        $hours = intval($parts[0]);
        
        // 48:00を超える場合は無効
        return $hours <= 48;
    }

    /**
     * 勤務時間を計算（分単位）
     */
    private function calculateWorkMinutes(Carbon $startTime, Carbon $endTime, Carbon $breakTime): int
    {
        // 終了時刻が開始時刻より前の場合（日またぎ）
        if ($endTime->lt($startTime)) {
            $endTime = $endTime->addDay();
        }

        // 総労働時間を計算（分単位）
        $totalMinutes = $startTime->diffInMinutes($endTime);
        
        // 休憩時間を分に変換
        $breakMinutes = $breakTime->hour * 60 + $breakTime->minute;

        // 勤務時間を計算
        $workMinutes = $totalMinutes - $breakMinutes;

        // 負の値にならないように調整
        return max(0, $workMinutes);
    }

    /**
     * 分を指定単位で丸める
     */
    private function roundMinutes(int $minutes, int $roundingUnit): int
    {
        return round($minutes / $roundingUnit) * $roundingUnit;
    }

    /**
     * 時刻の論理的な整合性をチェック
     */
    public function validateTimeLogic(string $startTime, string $endTime, string $breakTime): array
    {
        $errors = [];

        // 終了時刻の48:00制限チェック
        if (!$this->validateEndTimeLimit($endTime)) {
            $errors[] = '終了時刻は48:00までしか設定できません。';
            return $errors; // 制限を超えている場合は他のチェックをスキップ
        }

        // 時刻をCarbonオブジェクトに変換
        $startCarbon = $this->timeStringToCarbon($startTime);
        $endCarbon = $this->timeStringToCarbon($endTime);
        $breakCarbon = $this->timeStringToCarbon($breakTime);

        // 終了時刻が開始時刻より前の場合（日またぎ）
        if ($endCarbon->lt($startCarbon)) {
            $endCarbon = $endCarbon->addDay();
        }

        // 総労働時間を計算（分単位）
        $totalWorkMinutes = $startCarbon->diffInMinutes($endCarbon);
        
        // 休憩時間を分に変換
        $breakMinutes = $breakCarbon->hour * 60 + $breakCarbon->minute;

        // 休憩時間が総労働時間を超えている場合
        if ($breakMinutes > $totalWorkMinutes) {
            $errors[] = '休憩時間が総労働時間を超えています。';
        }

        // 勤務時間が負になる場合
        $actualWorkMinutes = $totalWorkMinutes - $breakMinutes;
        if ($actualWorkMinutes < 0) {
            $errors[] = '勤務時間が負になります。終了時刻を調整してください。';
        }

        return $errors;
    }
}
