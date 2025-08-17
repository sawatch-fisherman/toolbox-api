<?php

namespace App\Http\Requests;

use App\Rules\TimeFormat;
use App\Rules\EndTimeFormat;
use App\Rules\BreakTimeFormat;
use App\Services\WorkTimeCalculationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CalculateWorkTimeRequest extends FormRequest
{
    /**
     * リクエストの認可を決定
     */
    public function authorize(): bool
    {
        return true; // 認証不要
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', new TimeFormat()],      // 00:00～23:59
            'end_time' => ['required', new EndTimeFormat()],     // 00:01～48:00
            'break_time' => ['required', new BreakTimeFormat()], // 00:00～23:59
            'rounding_unit' => ['nullable', 'integer', 'min:1', 'max:60'],
        ];
    }

    /**
     * カスタムバリデーションメッセージ
     */
    public function messages(): array
    {
        return [
            'start_time.required' => '開始時刻は必須です。',
            'end_time.required' => '終了時刻は必須です。',
            'break_time.required' => '休憩時間は必須です。',
            'rounding_unit.integer' => '丸め単位は整数で入力してください。',
            'rounding_unit.min' => '丸め単位は1分以上で入力してください。',
            'rounding_unit.max' => '丸め単位は60分以下で入力してください。',
        ];
    }

    /**
     * カスタムバリデーション
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $this->validateTimeLogic($validator);
        });
    }

    /**
     * 時刻の論理的な整合性をチェック
     */
    private function validateTimeLogic(Validator $validator): void
    {
        $startTime = $this->input('start_time');
        $endTime = $this->input('end_time');
        $breakTime = $this->input('break_time');

        if (!$startTime || !$endTime || !$breakTime) {
            return; // 基本バリデーションでエラーになるため、ここではスキップ
        }

        $service = new WorkTimeCalculationService();
        $errors = $service->validateTimeLogic($startTime, $endTime, $breakTime);

        foreach ($errors as $error) {
            $validator->errors()->add('time_logic', $error);
        }
    }

    /**
     * バリデーション失敗時のレスポンス
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => '入力値が正しくありません。',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY) // 422 Unprocessable Entity
        );
    }

    /**
     * リクエストデータの前処理
     */
    protected function prepareForValidation(): void
    {
        // 時刻データの正規化
        $this->merge([
            'start_time' => trim($this->input('start_time', '')),
            'end_time' => trim($this->input('end_time', '')),
            'break_time' => trim($this->input('break_time', '')),
            'rounding_unit' => $this->input('rounding_unit') ? intval($this->input('rounding_unit')) : null,
        ]);
    }
}
