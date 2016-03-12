<?php

require APPPATH . 'libraries/REST_Controller.php';

class Images extends REST_Controller {

    function index_get($id = NULL) {
        $this->response(["images" => []], REST_Controller::HTTP_OK);
    }

}