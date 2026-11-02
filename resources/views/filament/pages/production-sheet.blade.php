<x-filament-panels::page>
    <div class="space-y-4">
        <input type="date" wire:model.live="date" class="rounded-lg border px-3 py-2" />
        <div class="overflow-hidden rounded-xl bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-right">
                    <tr>
                        <th class="p-3">الصنف</th>
                        <th class="p-3">الحجم</th>
                        <th class="p-3">الكمية</th>
                        <th class="p-3">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr class="border-t">
                            <td class="p-3">{{ $row['name'] }}</td>
                            <td class="p-3">{{ $row['variant'] }}</td>
                            <td class="p-3 font-bold">{{ $row['qty'] }}</td>
                            <td class="p-3">{{ implode(' · ', $row['notes']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">مفيش أصناف لليوم ده</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
