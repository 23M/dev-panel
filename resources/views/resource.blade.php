
namespace TTM\DevPanel\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use {{ $modelClass }};

class {{ $resourceClass }} extends Resource
{
    protected static ?string $model = \{{ $modelClass }}::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
    ->schema([
@foreach ($formFields as $field)
            {!! $field !!}
@endforeach
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make(),
            ])
            ->filters([
@if($hasSoftDeletes)
                \Filament\Tables\Filters\TrashedFilter::make(),
@endif
            ])
            ->columns([
@foreach ($tableColumns as $column)
                {!! $column !!}
@endforeach
        ])
        ->bulkActions([
@if($hasSoftDeletes)
            \Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
                \Filament\Tables\Actions\ForceDeleteBulkAction::make(),
                \Filament\Tables\Actions\RestoreBulkAction::make(),
            ]),
@else
            \Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]),
@endif
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => {{ $pageListClass }}::route('/'),
            'edit' => {{ $pageEditClass }}::route('/{record}/edit'),
            'create' => {{ $pageCreateClass }}::route('/create'),
        ];
    }
}
