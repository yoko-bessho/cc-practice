<?php

namespace Tests\Unit;

use App\Http\Requests\StoreDailyReportRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreDailyReportRequestTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function validData(): array
    {
        return [
            'date' => '2026-08-16',
            'title' => 'タイトル',
            'content' => '本文は10文字以上です。',
            'status' => 'draft',
        ];
    }

    private function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = (new StoreDailyReportRequest)->rules();

        return Validator::make($data, $rules);
    }

    public function test_全項目が正しい場合はバリデーションを通過する(): void
    {
        $validator = $this->validate($this->validData());

        $this->assertTrue($validator->passes());
    }

    public function test_dateが未入力の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['date' => '']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_dateが日付形式でない場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['date' => 'not-a-date']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    public function test_titleが未入力の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['title' => '']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_titleが51文字以上の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['title' => str_repeat('あ', 51)]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_titleが50文字の場合はバリデーションを通過する(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['title' => str_repeat('あ', 50)]));

        $this->assertTrue($validator->passes());
    }

    public function test_contentが未入力の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['content' => '']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('content', $validator->errors()->toArray());
    }

    public function test_contentが10文字未満の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['content' => str_repeat('あ', 9)]));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('content', $validator->errors()->toArray());
    }

    public function test_contentが10文字の場合はバリデーションを通過する(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['content' => str_repeat('あ', 10)]));

        $this->assertTrue($validator->passes());
    }

    public function test_statusが未入力の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['status' => '']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_statusがdraftまたはsubmitted以外の場合はエラーになる(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['status' => 'published']));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_statusがsubmittedの場合はバリデーションを通過する(): void
    {
        $validator = $this->validate(array_merge($this->validData(), ['status' => 'submitted']));

        $this->assertTrue($validator->passes());
    }
}
