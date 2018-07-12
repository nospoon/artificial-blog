<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilters
{
    /** @var Request */
    protected $request;

    /** @var Builder */
    protected $builder;

    /**
     * Declare mappings for query parameters if needed (useful when a corresponding method name is restricted)
     * @var array
     */
    protected $mappings = [];

    /**
     * QueryFilters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply existing query filters
     * @param Builder|Query $builder
     * @return Builder|Query
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $name = camel_case($name);

            if (method_exists($this, $name)) {
                $this->callFilterMethod($name, $value);
            } elseif (array_key_exists($name, $this->mappings) && method_exists($this, $this->mappings[$name])) {
                $this->callFilterMethod($this->mappings[$name], $value);
            }
        }

        return $this->builder;
    }

    /**
     * Get the query filters from request
     * @return array
     */
    public function filters(): array
    {
        return $this->request->all();
    }

    /**
     * @param $name
     * @param $value
     */
    protected function callFilterMethod($name, $value)
    {
        call_user_func_array([$this, $name], $value === null || trim($value) === '' ? [] : [$value]);
    }
}
