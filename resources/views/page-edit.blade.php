declare(strict_types=1);

namespace TTM\DevPanel\Resources;

use Filament\Resources\Pages\EditRecord;

class {{ $pageClass }} extends EditRecord
{
    protected static string $resource = {{ $resourceClass }}::class;
}
