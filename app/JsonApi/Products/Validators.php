<?php

namespace App\JsonApi\Products;

use Axiom\Rules\Decimal;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;

class Validators extends AbstractValidators
{

    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *      the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *      the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *      the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [];

    /**
     * Get resource validation rules.
     *
     * @param mixed|null $record
     *      the record being updated, or null if creating a resource.
     * @return mixed
     */
    protected function rules($record = null): array
    {
        $unique_rule_sku = 'unique:products';
        if ($record) {
            $unique_rule_sku = 'unique:products,sku,' . $record->id;
        }
        return [
            'name'=>['required', 'string'],
            'sku' =>['string', $unique_rule_sku],
            'quantity'=>['required', 'integer'],
            'description'=>['string'],
            'image'=>['string', 'max:1014'],
            'price'=>['required', new Decimal(8,2)],
        ];
    }

    /**
     * Get query parameter validation rules.
     *
     * @return array
     */
    protected function queryRules(): array
    {
        return [
            //
        ];
    }

}
