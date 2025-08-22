<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TorrentRequestResource;
use App\Models\TorrentRequest;
use App\Enums\AuthGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Request search filter.
     */
    public function filter(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = TorrentRequest::query()
            ->with(['category', 'type', 'resolution', 'user', 'bounties', 'claim.user'])
            ->withSum('bounties as bounty', 'seedbonus');

        if ($request->filled('name')) {
            $searchTerm = str_replace(' ', '%', $request->input('name'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('description', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        if ($request->filled('categories')) {
            $query->whereIntegerInRaw('category_id', (array) $request->input('categories'));
        }

        if ($request->filled('types')) {
            $query->whereIntegerInRaw('type_id', (array) $request->input('types'));
        }

        if ($request->filled('resolutions')) {
            $query->whereIntegerInRaw('resolution_id', (array) $request->input('resolutions'));
        }

        if ($request->filled('tmdb')) {
            $query->where(function ($query) use ($request) {
                $tmdb = (int) $request->input('tmdb');
                $query->where('tmdb_movie_id', '=', $tmdb)
                    ->orWhere('tmdb_tv_id', '=', $tmdb);
            });
        }

        if ($request->filled('imdb')) {
            $imdb = preg_replace('/[^0-9]/', '', $request->input('imdb'));
            $query->where('imdb', '=', $imdb);
        }

        if ($request->filled('tvdb')) {
            $query->where('tvdb', '=', (int) $request->input('tvdb'));
        }

        if ($request->filled('mal')) {
            $query->where('mal', '=', (int) $request->input('mal'));
        }

        if ($request->filled('filled')) {
            if ($request->boolean('filled')) {
                $query->whereNotNull('filled_by');
            } else {
                $query->whereNull('filled_by');
            }
        }

        if ($request->filled('claimed')) {
            if ($request->boolean('claimed')) {
                $query->has('claim');
            } else {
                $query->doesntHave('claim');
            }
        }

        $sortField = match ($request->input('sortField', 'created_at')) {
            'name', 'created_at', 'updated_at' => $request->input('sortField', 'created_at'),
            'bounty' => 'bounty',
            default => 'created_at'
        };
        
        $sortDirection = match (strtolower($request->input('sortDirection', 'desc'))) {
            'asc' => 'asc',
            default => 'desc'
        };

        $query->orderBy($sortField, $sortDirection);

        $perPage = min((int) $request->input('perPage', 25), 100);
        $page = max((int) $request->input('page', 1), 1);
        $requests = $query->paginate(
            perPage: $perPage,
            page: $page
        );

        return response()->json([
            'results' => TorrentRequestResource::collection($requests),
            'total_pages' => $requests->lastPage(),
            'total_results' => $requests->total(),
        ]);
    }

    /**
     * View a single request.
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $request = TorrentRequest::with(['category', 'type', 'resolution', 'user', 'bounties', 'claim.user'])
            ->withSum('bounties as bounty', 'seedbonus')
            ->find($id);

        if (!$request) {
            return response()->json(['error' => 'Torrent request not found.', 'id' => $id,], 404);
        }

        return response()->json([
            'results' => new TorrentRequestResource($request),
        ]);
    }
}
