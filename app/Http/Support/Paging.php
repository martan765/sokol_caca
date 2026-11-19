<?php

namespace App\Http\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Sokol\Domain\Pagination\Page;

final class Paging
{
    public static function currentPage(): int
    {
        return Paginator::resolveCurrentPage();
    }

    public static function toPaginator(Page $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator($page->items, $page->total, $page->perPage, $page->page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }
}
