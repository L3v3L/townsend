<?php

namespace App\Http\Controllers\v2;

use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreProductResource;

class GetProductsController extends Controller
{
    /**
     * Dummy data for the purpose of the test, normally this would be set by a store builder class
     */
    public function __construct(
        public int $storeId = 3
    ) {
    }

    public function __invoke(
        Request $request,
        $section = '%'
    ): array {
        try {
            return $this->getStoreProductsBySectionWithPaginationAndSorting(
                storeId: $this->storeId,
                section: $section,
                number: $request->get('number') ?? null,
                page: $request->get('page') ?? null,
                sort: $request->get('sort') ?? 0
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'An error occurred while fetching the products'
                ],
                500
            );
        }
    }

    public function getStoreProductsBySectionWithPaginationAndSorting(
        int $storeId,
        int|string $section,
        ?int $number = null,
        ?int $page = null,
        int|string $sort = 0
    ): array {
        $number = $number < 1 ? 8 : $number;
        $page = $page < 1 ? 1 : $page;

        $query = StoreProduct::query()
            ->whereStoreId($storeId)
            ->available()
            ->excludeCountry($this->getGeocode()['country'])
            ->when(
                !isset($_SESSION['preview_mode']),
                function ($subquery) {
                    $subquery->launched();
                }
            )
            ->excludeRemoved()
            ->when(
                $section !== '%' && strtoupper($section) !== 'ALL',
                function ($subquery) use ($section) {
                    $subquery->hasSection($section);
                }
            );

        $query
            ->orderBy('position')
            ->orderByDesc('release_date');

        $result = $query->paginate($number);

        if ($result->isEmpty()) {
            return [
                'pages' => 0
            ];
        }

        $result->through(function ($storeProduct) {
            return StoreProductResource::make($storeProduct);
        });

        $response = array_reverse($result->items());
        $response['pages'] = $result->lastPage();
        return $response;
    }

    public function getGeocode(): array
    {
        //Return GB default for the purpose of the test
        return ['country' => 'GB'];
    }
}
