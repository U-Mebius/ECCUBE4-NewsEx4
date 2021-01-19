<?php

namespace Plugin\NewsEx4\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class EditorUploadController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/upload/ckeditor", name="plugin_ckeditor_upload_image")
     *
     * @param Application $app
     * @param Request $request
     * @return string
     */
    public function index(Application $app, Request $request)
    {
        $type = $request->query->get('type');
        $CKEditor = $request->query->get('CKEditor');
        $funcNum = $request->query->get('CKEditorFuncNum');
        $message = "";

        $allowed_extension = array(
            "png", "jpg", "jpeg", "gif"
        );

        $str = '';

        // Get image file extension
        $file_extension = pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowed_extension)) {
            // 移動
            $File = $request->files->get('upload');
            if ($File) {
                $mimeType = $File->getMimeType();
                if (0 !== strpos($mimeType, 'image')) {
                    throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');
                }

                $baseFilePath = $this->getParameter('eccube_save_image_dir').'/editor';
                $fs = new Filesystem();

                if (!$fs->exists($baseFilePath)) {
                    $fs->mkdir($baseFilePath, 0775);
                }

                $extension = $File->getClientOriginalExtension();
                $filename = date('mdHis') . uniqid('_') . '.' . $extension;
                $File->move($baseFilePath.'/', $filename);

                $manager = $this->container->get('assets.packages');
                $url = $manager->getUrl('editor'.'/'.$filename, 'save_image');

                $str =  '<script>window.parent.CKEDITOR.tools.callFunction(' . $funcNum . ', "' . $url . '", "' . $message . '")</script>';
            }
        }

        return new Response($str);
    }
}
