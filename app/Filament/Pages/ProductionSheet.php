<?php
namespace App\Filament\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
class ProductionSheet extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel = 'جدول التجهيز';
    protected static string|UnitEnum|null $navigationGroup = 'المبيعات';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'جدول التجهيز';
    protected string $view = 'filament.pages.production-sheet';
    public ?string $date = null;
    public array $rows = [];
    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->loadRows();
    }
    public function loadRows(): void
    {
        $items = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->whereDate('delivery_date', $this->date)->whereNotIn('status', ['cancelled', 'refunded']))
            ->get();
        $grouped = [];
        foreach ($items as $item) {
            $key = $item->name_ar.'|'.($item->variant_name ?? '');
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['name' => $item->name_ar, 'variant' => $item->variant_name, 'qty' => 0, 'notes' => []];
            }
            $grouped[$key]['qty'] += $item->quantity;
            if (!empty($item->customization['cake_text'] ?? null)) {
                $grouped[$key]['notes'][] = $item->customization['cake_text'];
            }
        }
        $this->rows = array_values($grouped);
    }
    public function updatedDate(): void { $this->loadRows(); }
}
