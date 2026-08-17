@extends('layouts.app')

@section('title', '日報編集')

@section('content')
    <h1>日報編集</h1>

    @include('daily_reports._form', [
        'dailyReport' => $dailyReport,
        'action' => route('daily_reports.update', $dailyReport),
        'method' => 'PUT',
    ])

    <p><a href="{{ route('daily_reports.index') }}">一覧に戻る</a></p>
@endsection
