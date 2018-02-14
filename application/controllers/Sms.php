<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';


class Sms extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sms_model');
    }
    
    public function client_subscription_info_get($client_id)
    {
        $data = $this->Sms_model->get_subscription_data($client_id);
        if ($data) {
            if(todayExceeds($data['subscription_expire_date'])){
                $data['subscription_from_date'] = date('Y-m-d', strtotime($data['subscription_expire_date'] . ' +1 day'));
            } else{
                $data['subscription_from_date'] = date('Y-m-d');
            }

            if($data['subscription_type']=='1'){
                $data['subscription_to_date'] = date('Y-m-d', strtotime($data['subscription_from_date'] . ' +1 month'));
                $data['subscription_price'] = $data['package_price'];
                $data['subscription_discount'] = 0;
                
            } else{
                $data['subscription_to_date'] = date('Y-m-d', strtotime($data['subscription_from_date'] . ' +1 year'));
                $data['subscription_price'] = $data['package_price'] * 12;
                $data['subscription_discount'] = round($data['subscription_price'] * $data['package_discount'] / 100);
            }
            $data['subscription_total_price'] = $data['subscription_price'] - $data['subscription_discount'];

            $data['subscription_type_txt'] = $data['subscription_type']=='1' ? 'Monthly' : 'Yearly';

            $this->response(array('response' => $data), 200);
        } else {
            $this->response(array('error' => 'No data found...'), 404);
        }
    }

    public function save_payment_log_post(){
        //$data = json_decode($json, true);
        $data = $_POST;
        $sts = $this->commonmodel->commonInsert('invoice_payment_logs', $data);
        if($sts){
            //$this->response(array('response' => $sts), 200);
            $this->response(array('response' => 'Payment log saved successfully...'), 200);
        } else{
            $this->response(array('error' => 'Payment log could not save...'), 404);
        }
    }

    public function upd_subs_info_post($data=[]){
        /*$data = array (
            'first_name' => 'Sazzad',
            'last_name' => 'Hossain',
            'email_address' => 'skbros16@gmail.com',
            'mobile' => '01778413500',
            'package' => '1',
            'pos_cat_id' => '1',
            'pos_name' => 'Departmental Store POS',
            'subscribed_store' => '1',
            'subscribed_station' => '1',
            'subscribed_user' => '2',
            'subscribed_price' => '1290',
            'subscription_expire_date' => '2018-02-25',
            'subscription_from_date' => '2018-02-26',
            'subscription_to_date' => '2019-02-26',
            'subscription_type' => '2',
            'package_name' => 'Basic',
            'package_store' => '1',
            'package_station' => '1',
            'package_user' => '2',
            'package_price' => '1290',
            'package_discount' => '3',
            'subscription_price' => 15480,
            'subscription_discount' => 464,
            'subscription_total_price' => 15016,
            'subscription_type_txt' => 'Yearly',
            'status' => 'successed',
            'client_id' => '2',
          );
        echo '<pre>'; var_export($data); echo '</pre>';*/
        
        $data = $_POST;
        $this->commonmodel->saveLog(json_encode($data));

        $now = date('Y-m-d H:i:s');

        try{
            $this->db->trans_start();

            $client_data = [
                'subscription_date_from' => $data['subscription_from_date'],
                'subscription_date_to'   => $data['subscription_to_date'],
                'status' => 1,
                'update_date' => $now,
            ];
            $this->commonmodel->commonUpdate('client_info', $client_data, ['id'=>$data['client_id']]);
            
            $subscription_data = [
                'client_id' => $data['client_id'],
                'package' => $data['package'],
                'pos_cat_id' => $data['pos_cat_id'],
                'store' => $data['subscribed_store'],
                'station' => $data['subscribed_station'],
                'user' => $data['subscribed_user'],
                'price' => $data['subscription_total_price'],
                'subscription_type' => $data['subscription_type'],
                'subscription_start_date' => $data['subscription_from_date'],
                'subscription_expire_date' => $data['subscription_to_date'],
                'installation_fee' => 0,
                'subscription_fee' => $data['subscription_price'],
                'discount' => $data['subscription_discount'],
                'final_subscription_fee' => $data['subscription_total_price'],
                'payment_method' => 1, // Online Payment
                'status' => 1,
                'payment_confirm_date' => $now,
                'create_date' => $now,
            ];
            $subscription_id = $this->commonmodel->commonInsert('subscription_package', $subscription_data);

            $invoice_data = [
                'invoice_number' => 'PA-'.time(),
                'subscription_id' => $subscription_id,
                'pos_cat_id' => $data['pos_cat_id'],
                'client_id' => $data['client_id'],
                'invoice_status' => 1,
                'invoice_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime("+5 days")),
                'email_status' => 0,
                'payment_status' => 1,
                'payment_confirm_date' => $now,
                'payment_method_id' => 1, // Online Payment
                'create_date' => $now,
            ];
            $invoice_id = $this->commonmodel->commonInsert('invoice', $invoice_data);

            $temporary_invoice_data = [
                'client_id' => $data['client_id'],
                'invoice_number' => $invoice_data['invoice_number'],
                'date' => date('Y-m-d'),
                'category' => $this->Sms_model->getCategoryName($data['pos_cat_id']),
                'subscription' => get_key($this->config->item('subscription_types'), $data['subscription_type']),
                'installation_fee' => 0,
                'invoice_discount' => $data['subscription_discount'],
                'invoice_subscription_fee' => $data['subscription_price'],
                'final_subscription_fee' => $data['subscription_total_price'],
                'create_date' => $now,
            ];
            $tmp_invoice_id = $this->commonmodel->commonInsert('temporary_package_invoice', $temporary_invoice_data);

            $this->db->trans_complete();
            
            // if ($this->db->trans_status() === FALSE) {
            //     throw new Exception("Customer Signup failed.");
            // }
            
            $sts = TRUE;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $sts = FALSE;
        }

        if($sts){
            $this->response(array('response' => 'Subscription record saved successfully...'), 200);
        } else{
            $this->response(array('error' => 'Subscription record save failed...'), 404);
        }
    }
}