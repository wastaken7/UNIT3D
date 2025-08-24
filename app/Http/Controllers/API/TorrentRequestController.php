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
use Illuminate\Http\Request;

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
            $query->where(function ($query) use ($searchTerm): void {
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
            $query->where(function ($query) use ($request): void {
                $tmdb = $request->integer('tmdb');
                $query->where('tmdb_movie_id', '=', $tmdb)
                    ->orWhere('tmdb_tv_id', '=', $tmdb);
            });
        }

        if ($request->filled('imdb')) {
            $imdb = preg_replace('/[^0-9]/', '', $request->input('imdb'));
            $query->where('imdb', '=', $imdb);
        }

        if ($request->filled('tvdb')) {
            $query->where('tvdb', '=', $request->integer('tvdb'));
        }

        if ($request->filled('mal')) {
            $query->where('mal', '=', $request->integer('mal'));
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
                $query->whereNotNull('claim');
            } else {
                $query->whereNull('claim');
            }
        }

        $sortField = match ($request->input('sortField', 'created_at')) {
            'name', 'created_at', 'updated_at' => $request->input('sortField', 'created_at'),
            'bounty' => 'bounty',
            default  => 'created_at'
        };

        $sortDirection = match (strtolower($request->input('sortDirection', 'desc'))) {
            'asc'   => 'asc',
            default => 'desc'
        };

        $query->orderBy($sortField, $sortDirection);

        $perPage = min($request->integer('perPage', 25), 100);
        $page = max($request->integer('page', 1), 1);
        $requests = $query->paginate(
            perPage: $perPage,
            page: $page
        );

        return response()->json([
            'results'       => TorrentRequestResource::collection($requests),
            'total_pages'   => $requests->lastPage(),
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
            ->findOrFail($id);

        return (new TorrentRequestResource($request))->response();
    }
}
