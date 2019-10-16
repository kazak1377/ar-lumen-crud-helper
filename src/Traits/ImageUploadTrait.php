<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-06-27
 * Time: 13:27
 */

namespace ARCrud\Traits;

use ARCrud\Helpers\Image;
use ArHelpers\Errors\DeletingError;
use ArHelpers\Response\ImageDeletedResponse;
use ArHelpers\Response\ImageUploadedResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Constraint;

trait ImageUploadTrait {

    public function uploadImage() {
        /** @var UploadedFile $image */
        $image = Request::file('image');
        $image = new Image($image);
        $image->img->resize(1024, NULL,
            function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        $image->upload();
        return (new ImageUploadedResponse())
            ->setData([
                'imgPath' => $image->publicPath
            ])->send();
    }

    public function uploadWithoutResizing() {
        /** @var UploadedFile $image */
        $image = Request::file('image');
        $image = new Image($image);
        $image->upload();
        return (new ImageUploadedResponse())
            ->setData([
                'imgPath' => $image->publicPath
            ])->send();
    }

    public function deleteImage() {
        $imgPublicUrl = Request::input('url');
        $imgPathExpl = explode('/', $imgPublicUrl);
        $fileName = end($imgPathExpl);
        $localPath = "/public/uploads/{$fileName}";
        if (unlink($localPath)) {
            return (new ImageDeletedResponse())
                ->send();
        } else {
            return (new ImageDeletedResponse())
                ->setError(new DeletingError($imgPublicUrl))
                ->send();
        }
    }
}
