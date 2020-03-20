<?php

namespace App\Http\Queries;

use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class UserQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(User::query());

        $this->allowedFilters(['account', 'nickname']);

        $this->allowedSorts(['created_at']);
    }
}