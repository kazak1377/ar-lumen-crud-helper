<?php


namespace ARCrud\Controllers\Traits;


use ArHelpers\Models\Base\BaseModel;

trait CrudControllerHelpersTrait {
    private function validateListByIdsRequest() {
        /** @var BaseModel $model */
        $model = new $this->classname();
        $tableName = $model->getTable();
        $rules = [
            'ids' => 'required|array',
            'ids.*' => "integer|exists:{$tableName},id",
            'relations' => 'array',
            'relations.*' => 'string'
        ];
        $messages = [
            'ids.*.exists' => "Requested ID not found in {$tableName} table"
        ];
        $this->validateRequest($rules, $messages);
    }
}