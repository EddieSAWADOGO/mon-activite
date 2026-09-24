@props([
    'headers' => [],
])

<div class="w-full overflow-x-auto rounded-lg border border-slate-200">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-slate-700']) }}>
        @if (!empty($headers))
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3 sm:px-6 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-200 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
