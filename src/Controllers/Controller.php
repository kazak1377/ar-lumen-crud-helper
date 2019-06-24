<?php

namespace ARCrud\Controllers;

use ArHelpers\Errors\DeletingError;
use ArHelpers\Errors\NoSuchEntityError;
use ArHelpers\Errors\SavingError;
use ArHelpers\Helpers\Reflection;
use ArHelpers\Helpers\ValidationHelper;
use ArHelpers\Response\CreatedResponse;
use ArHelpers\Response\DataReturnResponse;
use ArHelpers\Response\DeletedResponse;
use ArHelpers\Response\UpdatedResponse;
use ArHelpers\Models\Base\BaseModel;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Validator;

class Controller extends BaseController
{
    /**
     * @param BaseModel|null $model
     * @param $fields
     *
     * @return ValidationHelper
     */
    public function updateFields(&$model, $fields) {
        $class = Reflection::modelName($model);
        if (is_null($model)) {
            new ValidationHelper($class);
        }

        $v = Validator::make($fields, $model->rules);
        foreach ($fields as $key => $value) {
            if ($model->have($key) && !empty($value)) {
                $model->$key = $value ?? ' ';
            }
        }

        return new ValidationHelper($class, $v);
    }

    /**
     * @param BaseModel $model
     * @param ValidationHelper $vhelper
     * @param bool $created
     *
     * @return ResponseFactory|Response
     */
    private function saveModel($model, $vhelper, $created = false) {
        $class = Reflection::modelName($model);
        $resp = $created ?
            new CreatedResponse($class) :
            new UpdatedResponse($class, $model->id);
        if ($vhelper->hasErrors) {
            $resp->setError($vhelper->error);
            return $resp->send();
        }
        if ($model->save()) {
            return $resp->send();
        } else {
            $error = new SavingError($class);
            return $resp->setError($error)
                ->setError($error)
                ->send();
        }
    }

    /**
     * @param BaseModel|null $model
     * @param ValidationHelper $vhelper
     * @param string $name
     *
     * @param int $id
     *
     * @return Response
     */
    public function saveUpdateModel(&$model, $vhelper, $name = "", $id = -1) {
        if (is_null($model)) {
            return (new UpdatedResponse($name, $id))
                ->setError(new NoSuchEntityError($name))
                ->send();
        }
        return $this->saveModel($model, $vhelper, false);
    }

    public function saveCreateModel(BaseModel &$model, $vhelper) {
        return $this->saveModel($model, $vhelper, true);
    }

    /**
     * @param BaseModel|null $model
     *
     * @param string $name
     *
     * @return Response
     */
    public function sendOneEntity($model, $name = '') {
        if (!empty($model)) {
            return (new DataReturnResponse())
                ->setData($model)
                ->send();
        } else {
            return (new DataReturnResponse())
                ->setError(new NoSuchEntityError($name))
                ->send();
        }
    }

    /**
     * @param BaseModel|null $model
     * @param $name
     *
     * @return Response
     */
    public function deleteModel($model, $name) {
        $key = 'deleted';
        if (empty($model)) {
            return (new DeletedResponse($name))
                ->setError(new NoSuchEntityError($name))
                ->send();
        } elseif ($model->have($key)) {
            try {
                $model->delete();
                return (new DeletedResponse($name))
                    ->send();
            } catch (Exception $e) {
                $error = new DeletingError($name);
                $error->description = [
                    'message' =>
                        "this model can't be deleted with current method"
                ];
                $error->description['data'] = $e;
                return (new DeletedResponse($name))
                    ->setError($error)
                    ->send();
            }
        } else {
            $error = new DeletingError($name);
            $error->description = [
                'message' => "this model can't be deleted with current method"
            ];
            return (new DeletedResponse($name))
                ->setError($error)
                ->send();
        }
    }
}
