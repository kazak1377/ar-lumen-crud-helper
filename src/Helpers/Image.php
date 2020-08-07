<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-06-27
 * Time: 13:47
 */

namespace ARCrud\Helpers;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as IImage;
use Throwable;

/**
 * Class Image
 * @package ARCrud\Helpers
 *
 * @property string uploadPath
 * @property string fileName
 * @property string publicPath
 */
class Image {
    /** @var UploadedFile */
    private $file;
    /** @var Image */
    public $img;
    private $uploadFolder;

    public function __construct(UploadedFile $file) {
        $this->file = $file;
        $this->img = IImage::make($this->file->getRealPath());
        $this->uploadFolder = base_path() . '/public/uploads/';
    }

    protected function getFileName() {
        return time()
            . "."
            . $this->file->extension();
    }

    public function prepareFolder() {
        if (!file_exists($this->uploadFolder)) {
            mkdir($this->uploadFolder, 0777, true);
        }
    }

    protected function getUploadPath() {
        return $this->uploadFolder . $this->fileName;
    }

    protected function getPublicPath() {
        return '/uploads/'.$this->fileName;
    }

    public function upload() {
        $this->prepareFolder();
        $this->img->save($this->uploadPath, 80);
    }

    public function __get($name) {
        try {
            $methodName = 'get' . ucfirst($name);
            return $this->$methodName();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function __set($name, $value) {
        try {
            $methodName = 'set' . ucfirst($name);
            return $this->$methodName($value);
        } catch (Throwable $e) {
            return false;
        }
    }
}