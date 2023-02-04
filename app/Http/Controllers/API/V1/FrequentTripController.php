<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FrequentTripController extends Controller
{
    public function __invoke()
    {
        $frequent_trips = DB::table('orders')
            ->select([
                'user_id',
                'pickup_destination as frequentBook',
                'users.email as email',
            ])
            ->selectRaw('count(*) as count')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->groupBy(['user_id', 'pickup_destination'])
            ->havingRaw(
                '
                    COUNT(pickup_destination) = (
                        SELECT COUNT(*) FROM orders o
                        WHERE orders.user_id = o.user_id
                        GROUP BY o.user_id, o.pickup_destination
                        ORDER BY COUNT(*) DESC
                        LIMIT 1
                    )
                '
            )
            ->orderByDesc(DB::raw('count(*)'))
            ->get();

        return $this->respond($frequent_trips);
    }
}
