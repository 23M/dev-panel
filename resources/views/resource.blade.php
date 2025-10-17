
namespace TTM\DevPanel\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use {{ $modelClass }};

class {{ $resourceClass }} extends Resource
{
    protected static ?string $model = \{{ $modelClass }}::class;

    public static function form(Schemas\Schema $schema): Schemas\Schema
    {
        return $schema
    ->components([
@foreach ($formFields as $field)
            {!! $field !!}
@endforeach
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
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
        ->toolbarActions([
@if($hasSoftDeletes)
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
                \Filament\Actions\ForceDeleteBulkAction::make(),
                \Filament\Actions\RestoreBulkAction::make(),
            ]),
@else
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
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
