@extends('layouts.app')

@section('title', '日報一覧')

@section('content')
    <h1>日報一覧</h1>

    <p><a href="{{ route('daily_reports.create') }}">新規作成</a></p>

    @forelse ($dailyReports as $dailyReport)
        @if ($loop->first)
            <table class="table-striped">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>タイトル</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
        @endif
                    <tr>
                        <td>{{ $dailyReport->date->format('Y-m-d') }}</td>
                        <td>{{ $dailyReport->title }}</td>
                        <td>
                            <span class="badge {{ $dailyReport->status === 'draft' ? 'badge-draft' : 'badge-submitted' }}">
                                {{ $dailyReport->status === 'draft' ? '下書き' : '提出済' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('daily_reports.edit', $dailyReport) }}">編集</a>
                            <form method="POST" action="{{ route('daily_reports.destroy', $dailyReport) }}" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        </td>
                    </tr>
        @if ($loop->last)
                </tbody>
            </table>
        @endif
    @empty
        <p>日報がありません。</p>
    @endforelse
@endsection
