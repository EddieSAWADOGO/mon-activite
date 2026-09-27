@props([
    'headers' => [],
])

<div class="w-full overflow-x-auto rounded-2xl border border-slate-200/80 bg-white">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-xs sm:text-sm text-slate-700 min-w-[600px]']) }}>
        @if (!empty($headers))
            <thead class="bg-slate-50/80 text-xs sm:text-sm uppercase font-bold text-slate-500 border-b border-slate-200/80">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-4 py-3.5 sm:px-5 sm:py-4 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
