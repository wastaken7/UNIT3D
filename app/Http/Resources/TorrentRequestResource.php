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

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin \App\Models\TorrentRequest
 */
class TorrentRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'category_id'    => $this->category_id,
            'type_id'        => $this->type_id,
            'resolution_id'  => $this->resolution_id,
            'user'           => $this->anon ? 'anonymous' : $this->user->username,
            'tmdb'           => $this->tmdb_movie_id ?: $this->tmdb_tv_id,
            'imdb'           => $this->imdb,
            'tvdb'           => $this->tvdb,
            'mal'            => $this->mal,
            'igdb'           => $this->igdb,
            'season_number'  => $this->season_number,
            'episode_number' => $this->episode_number,
            'bounty'         => $this->bounties?->sum('seedbonus') ?? 0,
            'status'         => $this->filled_by ? ($this->approved_by ? 'filled' : 'pending') : ($this->claim ? 'claimed' : 'unfilled'),
            'claimed'        => (bool) $this->claim,
            'claimed_by'     => $this->claim ? ($this->claim->anon ? 'anonymous' : ($this->claim->user?->username ?? null)) : null,
            'filled_by'      => $this->filled_by ? ($this->filled_anon ? 'anonymous' : ($this->filler?->username ?? null)) : null,
            'created'        => $this->created_at->toIso8601String(),
            'updated_at'     => $this->updated_at->toIso8601String(),
        ];
    }
}
