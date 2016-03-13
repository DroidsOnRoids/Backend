<?php

require APPPATH . 'libraries/REST_Controller.php';

class ImagesResponse {
    const ERROR_FILESIZE_LIMIT_EXCEEDED = "Exceeded filesize limit.";
    const ERROR_NO_FILE_SENT = "No file sent.";
    const ERROR_INVALID_FILE_FORMAT = "Invalid file format.";
    const ERROR_UNKNOWN = "Unknown error.";
    const ERROR_FAILED_TO_UPLOAD = "Failed to move uploaded file.";
    const ERROR_NO_FROM_USER_ID_SPECIFIED = "You didn't specify parameter from_userId.";
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
        // Filtering user data
        $id = intval($id);

        // Prepare arrays
        $files = []; // arrays with filtered files
        $files_unfiltered = []; // arrays with unfiltered files (e.g. might be dirs)

        // Fill the array with paths and files in path
        $all_path = $this->getPathForUser();
        $files_unfiltered[] = ["path" => $all_path, "files" => scandir($all_path)];
        if ($id > 0) {
            $user_path = $this->getPathForUser($id);
            $files_unfiltered[] = ["path" => $user_path, "files" => scandir($user_path)];
        }

        // Filter array, get only files and now with url instead of path
        foreach ($files_unfiltered as $files_array) {
            $path = $files_array['path'];
            foreach ($files_array['files'] as $file) {
                $file = $path.$file;
                if (is_file($file)) {
                    $files[] = base_url().substr($file, 2);
                }
            }
        }

        $this->response(["images" => $files], REST_Controller::HTTP_OK);
    }

    function upload_post() {
        // Filtering user data
        $to_userId = intval($this->post('to_userId')); // To which user the image was sent
        $from_userId = intval($this->post('from_userId')); // From which user the image was sent

        if ($from_userId <= 0) {
            $this->throwError(ImagesResponse::ERROR_NO_FROM_USER_ID_SPECIFIED);
        }

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

            $path = $this->getPathForUser($to_userId);
            $file_name = sprintf('%s%s_%s_%s.%s',
                $path,
                $from_userId,
                date('Y_m_d'),
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