<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-06-21
 * Time: 12:22
 */

namespace ARCrud\Controllers;


use ARCrud\Controllers\Traits\CrudControllerHelpersTrait;
use ArHelpers\Errors\RestoringError;
use ArHelpers\Errors\ValidationError;
use ArHelpers\Helpers\RequestValidationTrait;
use ArHelpers\Models\Base\BaseModel;
use ArHelpers\Response\DataReturnResponse;
use ArHelpers\Response\ErrorResponse;
use ArHelpers\Response\RestoredResponse;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Support\Facades\Request;


class CrudController extends Controller {
    protected $classname;
    use RequestValidationTrait;
    use CrudControllerHelpersTrait;

    public function create() {
        $data = Request::input();
        $newObject = new $this->classname();
        $vhelper = $this->updateFields(
            $newObject, $data, $this->classname
        );
        return $this->saveCreateModel($newObject, $vhelper);
    }

    public function read($id) {
        /** @noinspection PhpUndefinedMethodInspection */
        $model = $this->classname::whereId($id)->first();
        return $this->sendOneEntity($model, $this->classname);
    }

    public function update($id) {
        /** @noinspection PhpUndefinedMethodInspection */
        $model = $this->classname::whereId($id)->first();
        $data = Request::input();
        $vhelper = $this->updateFields($model, $data, $this->classname);
        return $this->saveUpdateModel($model, $vhelper, $this->classname, $id);
    }

    public function delete($id) {
        /** @noinspection PhpUndefinedMethodInspection */
        $model = $this->classname::whereId($id)->first();
        return $this->deleteModel($model, $this->classname, $id);
    }

    public function destroy($id) {
        /** @var BaseModel $class */
        $class = $this->classname;
        $model = $class::find($id);
        return $this->destroyModel($model, $this->classname, $id);
    }

    public function getList() {
        $perPage = Request::input('perPage') ?? 0;
        /** @noinspection PhpUndefinedMethodInspection */
        $data = $perPage != 0 ? $this->classname::paginate($perPage) :
            $this->classname::all();
        return (new DataReturnResponse())
            ->setData($data)
            ->send();
    }

    public function restore($id) {
        /** @noinspection PhpUndefinedMethodInspection */
        if ($this->classname::withTrashed()->where('id', $id)->restore()) {
            return (new RestoredResponse($this->classname, $id))
                ->send();
        } else {
            return (new RestoredResponse($this->classname, $id))
                ->setError(new RestoringError($this->classname, $id))
                ->send();
        }
    }

    public function listDeleted() {
        /** @noinspection PhpUndefinedMethodInspection */
        $data = $this->classname::onlyTrashed()->get();
        return (new DataReturnResponse())
            ->setData($data)
            ->send();
    }

    public function listByIds() {
        $this->validateListByIdsRequest();
        $ids = Request::input('ids');
        $relations = Request::input('relations') ?? [];
        $perPage = Request::input('perPage') ?? 0;
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $data = $perPage != 0 ?
                $this->classname::whereIn('id', $ids)
                    ->with($relations)->paginate($perPage) :
                $this->classname::whereIn('id', $ids)
                    ->with($relations)->get();
            return (new DataReturnResponse())
                ->setData($data)
                ->send();
        } catch (RelationNotFoundException $e) {
            $error = new ValidationError($this->classname);
            $error->setDescription([$e->getMessage()]);
            return (new ErrorResponse())
                ->setError($error)
                ->send();
        }
    }
}
