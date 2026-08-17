@extends('layouts.app')

@section('title', '日報作成')

@section('content')
    <h1>日報作成</h1>

    @include('daily_reports._form', [
        'dailyReport' => $dailyReport,
        'action' => route('daily_reports.store'),
        'method' => 'POST',
    ])

    <p><a href="{{ route('daily_reports.index') }}">一覧に戻る</a></p>
@endsection
