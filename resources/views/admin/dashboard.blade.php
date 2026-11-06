<x-admin-layout>
    <x-admin.page-header title="Dashboard" />

    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['label' => 'Member', 'value' => $memberCount, 'icon' => 'users'],
            ['label' => 'Langganan aktif', 'value' => $activeSubscriptionCount, 'icon' => 'card'],
            ['label' => 'Konten', 'value' => $contentCount, 'icon' => 'film'],
        ] as $stat)
            <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-card p-5">
                <span class="flex size-12 items-center justify-center rounded-xl bg-brand/20 text-brand-hover"><x-icon :name="$stat['icon']" /></span>
                <div>
                    <p class="text-3xl font-bold tabular-nums">{{ number_format($stat['value'], 0, ',', '.') }}</p>
                    <p class="text-sm text-muted">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="mt-8 mb-3 text-lg font-semibold">Top 5 ditonton</h2>
    <x-admin.table>
        <thead class="text-xs text-muted uppercase">
            <tr class="border-b border-white/10">
                <th class="px-4 py-3 font-medium">Judul</th>
                <th class="px-4 py-3 font-medium">Jenis</th>
                <th class="px-4 py-3 text-right font-medium">Ditonton</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topContents as $content)
                <tr class="border-b border-white/5 last:border-0">
                    <td class="px-4 py-3 font-medium">{{ $content->title }}</td>
                    <td class="px-4 py-3 text-muted">{{ $content->type->label() }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($content->views, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-admin.table>
</x-admin-layout>
