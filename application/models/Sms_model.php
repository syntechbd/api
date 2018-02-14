<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }
    
    public function get_subscription_data($client_id) {
        $data = false;
        $this->db->select("
          c.first_name
        , c.last_name
        , c.email_address
        , c.mobile
        , s.package
        , s.pos_cat_id
        , pc.name         AS pos_name
        , s.store         AS subscribed_store
        , s.station       AS subscribed_station
        , s.user          AS subscribed_user
        , s.price         AS subscribed_price
        , s.subscription_expire_date
        , ''              AS subscription_from_date
        , ''              AS subscription_to_date
        , s.subscription_type
        , p.package_name
        , p.store         AS package_store
        , p.station       AS package_station
        , p.user          AS package_user
        , p.price         AS package_price
        , p.discount      AS package_discount", FALSE);
        $this->db->from("client_info AS c");
        $this->db->join('subscription_package AS s', 's.client_id = c.id');
        $this->db->join('package AS p', 'p.id = s.package', 'left');
        $this->db->join('pos_category AS pc', 'pc.id = s.pos_cat_id');
        $this->db->where("c.id", $client_id);
        $this->db->order_by('s.id', 'DESC');
        $this->db->limit('1');
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
			$data = $q->row_array();
        }
        
        return $data;
    }
    
    function getCategoryName($id){
        $this->db->select("name");
        $this->db->from('pos_category');
        $this->db->where('id',$id);
        $this->db->order_by("id","ASC");
        $query = $this->db->get();
        $row = $query->row_array();
        return isset($row['name']) ? $row['name'] : '';
    }
}
