<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';


class Rafiq extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index_get()
    {
        $cities = ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna'];

        if (!is_null($cities)) {
            $this->response(array('response' => $cities), 200);
        } else {
            $this->response(array('error' => 'No hay ciudades en la base de datos...'), 404);
        }
    }

    public function index_post()
    {
        if ($this->post('city')) {
            $this->response(array('city' => $this->post('city')), 200);
        } else{
            $this->response(null, 400);
        }

        $id = $this->cities_model->save($this->post('city'));

        if (!is_null($id)) {
            $this->response(array('response' => $id), 200);
        } else {
            $this->response(array('error', 'Algo se ha roto en el servidor...'), 400);
        }
        
//        if (!$this->post('city')) {
//            $this->response(null, 400);
//        }
//
//        $id = $this->cities_model->save($this->post('city'));
//
//        if (!is_null($id)) {
//            $this->response(array('response' => $id), 200);
//        } else {
//            $this->response(array('error', 'Algo se ha roto en el servidor...'), 400);
//        }
    }
}