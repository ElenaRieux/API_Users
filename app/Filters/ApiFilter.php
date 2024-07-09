<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
    protected $allowesParms = [];

    protected $operatorMap = [];

    public function transform(Request $request)
    {
        $eloQuery = [];
        foreach ($this->allowesParms as $parm => $operators) {
            $query = $request->query($parm);

            if (!isset($query)) {
                continue;
            }
            $column = $parm;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }       
        }
       

        return $eloQuery;
    }
}
