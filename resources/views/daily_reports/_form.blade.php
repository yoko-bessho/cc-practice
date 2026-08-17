@if ($errors->any())
    <div class="error">
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <div class="field">
        <label for="date">日付</label>
        <input type="date" id="date" name="date" value="{{ old('date', $dailyReport->date?->format('Y-m-d')) }}">
    </div>

    <div class="field">
        <label for="title">タイトル</label>
        <input type="text" id="title" name="title" value="{{ old('title', $dailyReport->title) }}">
    </div>

    <div class="field">
        <label for="content">内容</label>
        <textarea id="content" name="content" rows="6">{{ old('content', $dailyReport->content) }}</textarea>
    </div>

    <div class="field">
        <label for="status">ステータス</label>
        <select id="status" name="status">
            <option value="draft" @selected(old('status', $dailyReport->status ?? 'draft') === 'draft')>下書き</option>
            <option value="submitted" @selected(old('status', $dailyReport->status ?? 'draft') === 'submitted')>提出済</option>
        </select>
    </div>

    <button type="submit">保存</button>
</form>
