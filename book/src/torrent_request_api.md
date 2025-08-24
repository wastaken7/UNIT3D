# Torrent Requests API Documentation

## Endpoints

### Filter Requests

`GET /api/requests/filter`

Filter, sort, and paginate torrent requests.

#### Query Parameters

| Parameter       | Type    | Description                                             | Default     |
|-----------------|---------|---------------------------------------------------------|-------------|
| `name`          | string  | Search by name                                          | -           |
| `categories[]`  | array   | Filter by category ID(s)                                | -           |
| `types[]`       | array   | Filter by type ID(s)                                    | -           |
| `resolutions[]` | array   | Filter by resolution ID(s)                              | -           |
| `tmdb`          | integer | Filter by TMDB ID                                       | -           |
| `imdb`          | string  | Filter by IMDB ID                                       | -           |
| `tvdb`          | integer | Filter by TVDB ID                                       | -           |
| `mal`           | integer | Filter by MAL ID                                        | -           |
| `filled`        | boolean | Filter by filled status                                 | -           |
| `claimed`       | boolean | Filter by claimed status                                | -           |
| `sortField`     | string  | Field to sort by (name, created_at, updated_at, bounty) | created_at  |
| `sortDirection` | string  | Sort direction (asc, desc)                              | desc        |
| `page`          | integer | Page number                                             | 1           |
| `perPage`       | integer | Items per page (max: 100)                               | 25          |

#### Example Request

```bash
curl -X GET "https://unit3d.site/api/requests/filter?tmdb=2508" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json"
```

#### Example Response

```json
{
  "results": [
    {
      "id": 1,
      "name": "Mind Your Language S04",
      "description": "Example description.",
      "category_id": 2,
      "type_id": 2,
      "resolution_id": 6,
      "user": "anonymous",
      "tmdb": 2508,
      "imdb": 75537,
      "tvdb": 78286,
      "mal": null,
      "igdb": null,
      "season_number": 4,
      "episode_number": 0,
      "bounty": 125000,
      "status": "unfilled",
      "claimed": false,
      "claimed_by": null,
      "filled_by": null,
      "created": "2025-08-01T11:02:22+00:00",
      "updated_at": "2025-08-21T12:40:27+00:00"
    }
  ],
  "total_pages": 1,
  "total_results": 1
}
```

### Get Single Request

`GET /api/requests/{id}`

Filter information on a single request ID.

#### Parameters

| Parameter | Type    | Description         |
|-----------|---------|---------------------|
| `id`      | integer | Torrent request ID  |

#### Example Request

```bash
curl -X GET "https://unit3d.site/api/requests/1" \
-H "Authorization: Bearer YOUR_API_KEY_HERE" \
-H "Accept: application/json"
```

#### Example Response

```json
{
  "results": {
    "id": 1,
    "name": "Mind Your Language S04",
    "description": "Example description.",
    "category_id": 2,
    "type_id": 2,
    "resolution_id": 6,
    "user": "anonymous",
    "tmdb": 2508,
    "imdb": 75537,
    "tvdb": 78286,
    "mal": null,
    "igdb": null,
    "season_number": 4,
    "episode_number": 0,
    "bounty": 125000,
    "status": "unfilled",
    "claimed": false,
    "claimed_by": null,
    "filled_by": null,
    "created": "2025-08-01T11:02:22+00:00",
    "updated_at": "2025-08-21T12:40:27+00:00"
  }
}
```

## Response Fields

| Field           | Type    | Description                                               |
|-----------------|---------|-----------------------------------------------------------|
| `id`            | integer | Request ID                                                |
| `name`          | string  | Request title                                             |
| `description`   | string  | Request description                                       |
| `category_id`   | integer | Category ID                                               |
| `type_id`       | integer | Release type ID, null if set to "any"                     |
| `resolution_id` | integer | Resolution ID, null if set to "any"                       |
| `user`          | string  | Username of requester or "anonymous"                      |
| `tmdb`          | integer | TMDB ID                                                   |
| `imdb`          | integer | IMDB ID                                                   |
| `tvdb`          | integer | TVDB ID                                                   |
| `mal`           | integer | MyAnimeList ID                                            |
| `igdb`          | integer | IGDB ID                                                   |
| `season_number` | integer | Season number                                             |
| `episode_number`| integer | Episode number                                            |
| `bounty`        | integer | Total bounty amount                                       |
| `status`        | string  | Request status, unfilled, claimed, pending, or filled     |
| `claimed`       | boolean | Whether or not the request is claimed                     |
| `claimed_by`    | string  | Username of claimer, "anonymous", null if unclaimed       |
| `filled_by`     | string  | Username of filler, "anonymous", null if unfilled         |
| `created`       | string  | Creation date                                             |
| `updated_at`    | string  | Last update date                                          |
