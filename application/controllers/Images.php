<?php

require APPPATH . 'libraries/REST_Controller.php';

class Images extends REST_Controller {

    function get_get($id = NULL) {
        $this->response(["images" => [$id]], REST_Controller::HTTP_OK);
    }

    function upload_get($id = NULL) {
        $this->response(["images" => [$id]], REST_Controller::HTTP_OK);
    }

}