<?php

namespace App\JsonApi\Users;

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
        $unique_rule_username = $unique_rule_email = 'unique:users';

        if ($record) {
            $unique_rule_username = 'unique:users,username,' . $record->id;
            $unique_rule_email = 'unique:users,email,' . $record->id;
        }
        return [
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required', 'alpha_num', 'max:191', $unique_rule_username],
            'email' => ['required', 'email', 'max:191', $unique_rule_email],
            'password' => ['required', 'string', 'min:10', 'max:30'],
            'phone_number' => ['required', 'numeric'],
            'birthday' => ['required', 'date'],
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

    protected function messages($record = null): array
    {
        return [
            'name.required' => 'User must have a name.',
        ];
    }

}
