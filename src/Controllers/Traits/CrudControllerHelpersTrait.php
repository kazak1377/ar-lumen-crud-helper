<?php


namespace ARCrud\Controllers\Traits;


use ArHelpers\Errors\ValidationError;
use ArHelpers\Models\Base\BaseModel;
use ArHelpers\Response\DataReturnResponse;

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

    private function hasRelation($modelClass, $relation) {
        $relations = explode('.', $relation);
        /** @var BaseModel $model */
        $model = new $modelClass();
        foreach ($relations as $rel) {
            if (method_exists($model, $rel)) {
                $model = $model->$rel();
            } else {
                return false;
            }
        }
        return true;
    }

    private function validateModelRelations($relations) {
        $errors = [];
        foreach ($relations as $relation) {
            if (!$this->hasRelation($this->classname, $relation)) {
                $errors[$relation] =
                    "This relation is not exists ".
                    "for this model ({$this->classname})";
            }
        }
        if (count($errors) > 0) {
            $error = new ValidationError($this->classname);
            $error->setDescription($errors);
            (new DataReturnResponse())
                ->setError($error)
                ->sendAndDie();
        }
    }
}