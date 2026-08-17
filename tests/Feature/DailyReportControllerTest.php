<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_一覧画面に登録済みの日報が表示される(): void
    {
        DailyReport::factory()->create(['title' => 'テスト日報タイトル']);

        $response = $this->get(route('daily_reports.index'));

        $response->assertStatus(200);
        $response->assertSee('テスト日報タイトル');
    }

    public function test_新規作成画面が表示される(): void
    {
        $response = $this->get(route('daily_reports.create'));

        $response->assertStatus(200);
        $response->assertViewIs('daily_reports.create');
    }

    public function test_有効なデータで登録すると一覧にリダイレクトされ日報が保存される(): void
    {
        $data = [
            'date' => '2026-08-17',
            'title' => '新規日報',
            'content' => '本文は10文字以上です。',
            'status' => 'draft',
        ];

        $response = $this->post(route('daily_reports.store'), $data);

        $response->assertRedirect(route('daily_reports.index'));
        $this->assertDatabaseHas('daily_reports', [
            'title' => '新規日報',
            'status' => 'draft',
        ]);
    }

    public function test_バリデーションエラーがある場合は登録されず入力画面に戻る(): void
    {
        $data = [
            'date' => '2026-08-17',
            'title' => '',
            'content' => '本文は10文字以上です。',
            'status' => 'draft',
        ];

        $response = $this->post(route('daily_reports.store'), $data);

        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseCount('daily_reports', 0);
    }

    public function test_編集画面に対象の日報の値が表示される(): void
    {
        $dailyReport = DailyReport::factory()->create(['title' => '編集対象タイトル']);

        $response = $this->get(route('daily_reports.edit', $dailyReport));

        $response->assertStatus(200);
        $response->assertSee('編集対象タイトル');
    }

    public function test_有効なデータで更新すると一覧にリダイレクトされ日報が更新される(): void
    {
        $dailyReport = DailyReport::factory()->create(['title' => '更新前タイトル']);

        $data = [
            'date' => '2026-08-17',
            'title' => '更新後タイトル',
            'content' => '更新後の本文は10文字以上です。',
            'status' => 'submitted',
        ];

        $response = $this->put(route('daily_reports.update', $dailyReport), $data);

        $response->assertRedirect(route('daily_reports.index'));
        $this->assertDatabaseHas('daily_reports', [
            'id' => $dailyReport->id,
            'title' => '更新後タイトル',
            'status' => 'submitted',
        ]);
    }

    public function test_バリデーションエラーがある場合は更新されず編集画面に戻る(): void
    {
        $dailyReport = DailyReport::factory()->create(['title' => '更新前タイトル']);

        $data = [
            'date' => '2026-08-17',
            'title' => '',
            'content' => '更新後の本文は10文字以上です。',
            'status' => 'submitted',
        ];

        $response = $this->put(route('daily_reports.update', $dailyReport), $data);

        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseHas('daily_reports', [
            'id' => $dailyReport->id,
            'title' => '更新前タイトル',
        ]);
    }

    public function test_削除すると一覧にリダイレクトされ日報がデータベースから削除される(): void
    {
        $dailyReport = DailyReport::factory()->create();

        $response = $this->delete(route('daily_reports.destroy', $dailyReport));

        $response->assertRedirect(route('daily_reports.index'));
        $this->assertDatabaseMissing('daily_reports', ['id' => $dailyReport->id]);
    }
}
