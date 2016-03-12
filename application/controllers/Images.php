<?php

require APPPATH . 'libraries/REST_Controller.php';

class ImagesResponse {
    const ERROR_FILESIZE_LIMIT_EXCEEDED = "Exceeded filesize limit.";
    const ERROR_NO_FILE_SENT = "No file sent.";
    const ERROR_INVALID_FILE_FORMAT = "Invalid file format.";
    const ERROR_UNKNOWN = "Unknown error.";
    const ERROR_FAILED_TO_UPLOAD = "Failed to move uploaded file.";
    const SUCCESS_IMAGE_UPLOADED = "Image uploaded correctly.";
}

class Images extends REST_Controller {

    const PATH_ALL_FILES = "./images/all/";
    const PATH_USER_FILES = "./images/user_{ID}/";

    private function getPathForUser($userId = NULL) {
        $userId = intval($userId);
        $path = '';
        if ($userId > 0) {
            $path = str_replace("{ID}", $userId, Images::PATH_USER_FILES);
        } else {
            $path = Images::PATH_ALL_FILES;
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    function get_get($id = NULL) {
        $files = [];
        $files_unfiltered = [];
        $all_path = $this->getPathForUser();
        $files_unfiltered[] = ["path" => $all_path, "files" => scandir($all_path)];
        if ($id > 0) {
            $user_path = $this->getPathForUser($id);
            $files_unfiltered[] = ["path" => $user_path, "files" => scandir($user_path)];
        }
        foreach ($files_unfiltered as $files_array) {
            $path = $files_array['path'];
            echo "Path: $path <br />";
            foreach ($files_array['files'] as $file) {
                $file = $path.'/'.$file;
                echo "File: $file <br />";
                if (is_file($file)) {
                    $files[] = $file;
                }
            }
        }
        print_r($files);

        $this->response(["images" => $files], REST_Controller::HTTP_OK);
    }

    function upload_post($id = NULL) {
        $id = intval($id);

        if (!isset($_FILES['file'])) {
            $this->throwError(ImagesResponse::ERROR_NO_FILE_SENT);
        }

        $file = $_FILES['file'];
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

            $path = $this->getPathForUser($id);
            $file_name = sprintf('%s%s.%s',
                $path,
                sha1_file($file['tmp_name']),
                $ext
            );
            if (!move_uploaded_file($file['tmp_name'], $file_name)) {
                $this->throwError(ImagesResponse::ERROR_FAILED_TO_UPLOAD);
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