# Git search API test
An API that unifies the search response structure of different Git hosting API's.

## Requirements
* PHP >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension

[Learn more](https://lumen.laravel.com/docs/5.6#server-requirements)

## Installation
* `composer install`
* `cp .env.example .env`
* `php artisan key:generate`
* Make sure that your `.env` is correct.

## Usage
* GET `/` Welcome message
* GET `/search` Search endpoint

### Request data requirements

Note: if a property has a default value you don't need to send it in the request even if it's required.

| Property | Type | Validation |
|:---:|:---:|:---:|
| term | string | required |
| per_page | integer | required, max: 100, default: 25 |
| page | integer | required, default: 1 |
| order | string | required, in: [asc, desc], default: desc |
| sortBy | string | required |

### Provider specific requirements

#### Github

| Property | Type | Validation |
|:---:|:---:|:---:|
| sortBy | string | required, in: [stars, forks, updated], default: stars |

#### Gitlab

| Property | Type | Validation |
|:---:|:---:|:---:|
| sortBy | string | required, in: [id, name, path, created_at, updated_at, last_activity_at], default: name |

#### Bitbucket

| Property | Type | Validation |
|:---:|:---:|:---:|
| username | string | required |
| sortBy | string | required, in: [id, name, path, created_at, updated_at, last_activity_at], default: name |

### Successful request response
Returns json structure:
```
{
    'per_page': integer - results per page,
    'page': integer - current page,
    'sort': string - currently sroting by,
    'order': string - currently ordered by,
    'term': string - your request term,
    'total': integer - total repositories found (can be unsupported by some hosts),
    'repositories': array - repositories found {
        'name': string - repository name,
        'full_name': string - author/repository name,
        'description': string - repository description,
        'author': string - author username,
        'raiting': integer - repository rating (stars) (can be unsupported by some hosts),
        'url': string - link to the repository,
        'created_at': string - date created,
        'updated_at': string - date updated
    }
};
```

## TODO
Test test test