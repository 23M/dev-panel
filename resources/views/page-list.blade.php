declare(strict_types=1);

namespace TTM\DevPanel\Resources;

use app\Filament\Resources\DeviceResource;
use App\Models\Device;
use App\Models\DeviceGroup;
use DB;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class {{ $pageClass }} extends ListRecords
{
    protected static string $resource = {{ $resourceClass }}::class;
}
