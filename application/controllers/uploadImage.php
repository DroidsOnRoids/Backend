<?php

require APPPATH . 'libraries/REST_Controller.php';

class UploadImage extends REST_Controller {

    function index_post($id = NULL) {
        $this->response(["images" => []], REST_Controller::HTTP_OK);
    }

}