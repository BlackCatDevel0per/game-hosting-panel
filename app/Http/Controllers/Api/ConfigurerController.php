<?php

namespace App\Http\Controllers\Api;

use App\Game;
use App\Http\Controllers\Controller;
use App\Location;
use App\Node;
use App\Processors\CsgoProcessor;
use App\Processors\Processor;
use App\Services\GameService;
use Illuminate\Http\Request;

class ConfigurerController extends Controller
{
    public function games()
    {
        return Game::all()->keyBy('id');
    }

    public function locations()
    {
        return Location::all()->map(function (Location $location) {
            return $location->attributesToArray() + [
                    'available' => true,
                ];
        })->keyBy('id');
    }

    public function gameLocations(Game $game)
    {
        $locations = Location::with(['nodes', 'nodes.games'])->get();

        // Serialize Location and add 'available' field, if ANY node has $game
        return $locations->map(function (Location $location) use ($game) {
            $arr = $location->attributesToArray();
            // Check if we have a Node, that has $game
            $arr['available'] = $location->nodes->filter(function (Node $node) use ($game) {
                    return $node->games->filter(fn (Game $g) => $g->id === $game->id)->count() > 0;
                })->count() > 0;

            return $arr;
        })->keyBy('id');
    }

    public function computeResources(GameService $service, Request $request, Game $game, Location $location)
    {
        /** @var Processor $processor */
        $processor = $service->getProcessor($game);

        $cost = $processor->cost($request->all());
        $remainder = $request->only(['game', 'location']);

        return array_merge($cost, $remainder);
    }

    public function parameters(GameService $service, Request $request, Game $game, Location $location, $mode = 'simple'): ?array
    {
        if ($mode === 'simple') {
            /** @var CsgoProcessor $processor */
            $processor = $service->getProcessor($game);

            return $processor->calculate($request->all());
        }

        return [
            'cpu'       => [
                'name'        => 'CPU',
                'icon'        => 'cpu',
                'description' => 'Maximum core usage',
                'options'     => [
                    '1200'  => '1200 points',
                    '1800'  => '1800 points',
                    '2400' => '2400 points',
                ],
            ],
            'memory'    => [
                'name'        => 'Memory',
                'icon'        => 'memory',
                'description' => 'Maximum memory usage',
                'options'     => [
                    '1000' => '1 GB',
                    '2000' => '2 GB',
                    '3000' => '3 GB',
                    '4000' => '4 GB',
                    '5000' => '5 GB',
                ],
            ],
            'disk'      => [
                'name'        => 'Disk',
                'icon'        => 'disk',
                'description' => 'Maximum disk usage',
                'options'     => [
                    '5000'  => '5 GB',
                    '10000' => '10 GB',
                    '20000' => '20 GB',
                    '30000' => '30 GB',
                    '40000' => '40 GB',
                    '50000' => '50 GB',
                ],
            ],
            'databases' => [
                'name'        => 'Databases',
                'icon'        => 'databases',
                'description' => 'Maximum database tables',
                'options'     => [
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],
            ],
        ];
    }
}
