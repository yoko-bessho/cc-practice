<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\DailyReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(Request $request): View
    {
        // 想定外の値が来てもビューの選択状態と絞り込み結果がずれないよう「全て」(null)に丸める
        $status = in_array($request->query('status'), ['draft', 'submitted'], true)
            ? $request->query('status')
            : null;

        // 日報は運用上そこまで件数が増えない想定のため、ページネーションせず全件取得する
        $dailyReports = DailyReport::query()
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            // 同一日付の日報が複数あっても表示順が安定するよう id を第2ソートキーにする
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('daily_reports.index', compact('dailyReports', 'status'));
    }

    public function create(): View
    {
        // フォームの部分ビューで空の値を参照できるよう未保存のインスタンスを渡す
        $dailyReport = new DailyReport;

        return view('daily_reports.create', compact('dailyReport'));
    }

    public function store(StoreDailyReportRequest $request): RedirectResponse
    {
        DailyReport::create($request->validated());

        return redirect()->route('daily_reports.index')->with('status', '日報を作成しました。');
    }

    public function edit(DailyReport $dailyReport): View
    {
        return view('daily_reports.edit', compact('dailyReport'));
    }

    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport): RedirectResponse
    {
        $dailyReport->update($request->validated());

        return redirect()->route('daily_reports.index')->with('status', '日報を更新しました。');
    }

    public function destroy(DailyReport $dailyReport): RedirectResponse
    {
        $dailyReport->delete();

        return redirect()->route('daily_reports.index')->with('status', '日報を削除しました。');
    }
}
