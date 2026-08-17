<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\DailyReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DailyReportController extends Controller
{
    public function index(): View
    {
        // 同一日付の日報が複数あっても表示順が安定するよう id を第2ソートキーにする
        $dailyReports = DailyReport::orderByDesc('date')->orderByDesc('id')->get();

        return view('daily_reports.index', compact('dailyReports'));
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
