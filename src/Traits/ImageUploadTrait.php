<?php
/**
 * User: kazak
 * Email: mk@altrecipe.com
 * Date: 2019-06-27
 * Time: 13:27
 */

namespace ARCrud\Traits;

use ARCrud\Helpers\Image;
use ArHelpers\Response\ImageUploadedResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Constraint;

trait ImageUploadTrait {

    public function uploadImage() {
        /** @var UploadedFile $image */
        $image = Input::file('image');
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
}