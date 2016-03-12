<?php

require APPPATH . 'libraries/REST_Controller.php';

class ImagesResponse {
    const ERROR_FILESIZE_LIMIT_EXCEEDED = "Exceeded filesize limit.";
    const ERROR_NO_FILE_SENT = "No file sent.";
    const ERROR_INVALID_FILE_FORMAT = "Invalid file format.";
    const ERROR_UNKNOWN = "Unknown error.";
    const SUCCESS_IMAGE_UPLOADED = "Image uploaded correctly.";
}

class Images extends REST_Controller {

    function get_get($id = NULL) {
        $this->response(["images" => [$id]], REST_Controller::HTTP_OK);
    }

    function upload_post($id = NULL) {
        $file = $_FILES['file'];

        if (!isset($file)) {
            $this->throwError(ImagesResponse::ERROR_NO_FILE_SENT);
        }

        if ($file['error'] == UPLOAD_ERR_OK) {

            if ($file['size'] > 1000000) {
                $this->throwError(ImagesResponse::ERROR_FILESIZE_LIMIT_EXCEEDED);
            }

            $file_info = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search(
                    $file_info->file($file['tmp_name']),
                    array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ),
                    true
                )) {
                $this->throwError(ImagesResponse::ERROR_INVALID_FILE_FORMAT);
            }

            $this->success(ImagesResponse::SUCCESS_IMAGE_UPLOADED);
        } else {
            $error = '';
            switch ($file['error']) {
                case UPLOAD_ERR_NO_FILE:
                    $error = ImagesResponse::ERROR_NO_FILE_SENT;
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = ImagesResponse::ERROR_FILESIZE_LIMIT_EXCEEDED;
                    break;
                default:
                    $error = ImagesResponse::ERROR_UNKNOWN;
                    break;
            }
            $this->throwError($error);
        }
    }

    private function success($success) {
        $this->response(["Success" => $success], REST_Controller::HTTP_OK);
        die();
    }

    private function throwError($error) {
        $this->response(["error" => $error], REST_Controller::HTTP_BAD_REQUEST);
        die();
    }
}