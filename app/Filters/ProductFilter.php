<?php

namespace App\Filters;

use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ProductFilter extends ApiFilter
{
    protected $allowesParms = [
        'title' => ['eq'],
        'price' => ['eq', 'gt', 'lt', 'lte', 'gte'],
        'category' => ['eq'],
        'rating_rate' => ['eq', 'gt', 'lt', 'lte', 'gte'],
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];

}
