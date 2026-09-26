<form method="GET" class="bg-white p-4 rounded-xl shadow mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Preset</label>
        <select name="preset" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="today"       {{ request('preset') === 'today' ? 'selected' : '' }}>Today</option>
            <option value="yesterday"   {{ request('preset') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
            <option value="this_week"   {{ request('preset') === 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month"  {{ request('preset', 'this_month') === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="custom"      {{ request('preset') === 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
               class="border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
               class="border rounded px-3 py-2 text-sm">
    </div>
    <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Apply</button>
    <a href="{{ request()->fullUrlWithQuery(['preset' => 'this_month']) }}"
       class="text-gray-500 text-sm hover:underline">Reset</a>
</form>