declare(strict_types=1);

namespace TTM\DevPanel\Resources;

use Filament\Resources\Pages\CreateRecord;

class {{ $pageClass }} extends CreateRecord
{
    protected static string $resource = {{ $resourceClass }}::class;
}
