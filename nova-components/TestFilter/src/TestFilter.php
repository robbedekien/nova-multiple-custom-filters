<?php

namespace Nova\TestFilter;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class TestFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'test-filter';
    public $name;
    public $attribute;
    public $options;

    public function __construct($name, $attribute, $opions)
    {
        $this->name = $name;
        $this->attribute = $attribute;
        $this->options = $opions;
    }

    public function name()
    {
        return $this->name;
    }

    // When adding the key function, the filter stops working!
    // public function key(){
    //     return uniqid();
    // }

    /**
     * Set the default options for the filter.
     *
     * @return array|mixed
     */
    public function default()
    {
        return [];
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $values)
    {
        if ($values) {
            $relationSteps = explode('.', $this->attribute);

            return $this->whereHasRecursive($query, $relationSteps, $values);
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return $this->options;
    }

    private function whereHasRecursive($query, $steps, $value)
    {
        if (count($steps) > 1) {
            $step = array_shift($steps);
            $query->whereHas($step, function ($query) use ($steps, $value) {
                $this->whereHasRecursive($query, $steps, $value);
            });
        } else {
            $step = array_shift($steps);

            $query->where($step, $value);
        }

        return $query;
    }
}
