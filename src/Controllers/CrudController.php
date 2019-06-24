<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-06-21
 * Time: 12:22
 */

namespace App\Http\Controllers;


use ArHelpers\Response\DataReturnResponse;
use Illuminate\Support\Facades\Input;

class CrudController extends Controller {
    protected $classname;

    public function create() {
        $data = Input::all();
        $newObject = new $this->classname();
        $vhelper = $this->updateFields($newObject, $data);
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
        $data = Input::all();
        $vhelper = $this->updateFields($model, $data);
        return $this->saveUpdateModel($model, $vhelper, $this->classname, $id);
    }

    public function delete($id) {
        /** @noinspection PhpUndefinedMethodInspection */
        $model = $this->classname::whereId($id)->first();
        return $this->deleteModel($model, $this->classname);
    }

    public function getList() {
        /** @noinspection PhpUndefinedMethodInspection */
        $data = $this->classname::all();
        return (new DataReturnResponse())
            ->setData($data)
            ->send();
    }
}