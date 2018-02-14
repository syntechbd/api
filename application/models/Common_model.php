<?php

/**
 * Contains all common methods to be used in entire project 
 *
 * @author Rafiqul Islam <rafiq.kuet@gmail.com>
 * @date September 14, 2017 10:15
 */

class Common_model extends CI_Model{
    
    /**
     * Checks whether a value exists in a table
     * 
     * @param string $table     : table name to check
     * @param string $column    : column name to check
     * @param string $value     : value to be matched in the column of the table
     * @return boolean
     */
    public function isExist($table, $column, $value) {
        $tot = $this->db
            ->where($column, $value)
            ->count_all_results($table);
        
        return $tot>0 ? TRUE : FALSE;
    }
    
    /**
     * Checks whether a value exists (except checking a column) in a table
     * 
     * @param string $table         : table name to check
     * @param string $chk_column    : column name to check
     * @param string $chk_value     : value to be matched in the column of the table
     * @param string $expt_column   : column name to be skipped checking
     * @param string $expt_value    : value to be skipped checking
     * @return boolean
     */
    public function isExistExcept($table, $chk_column, $chk_value, $expt_column='', $expt_value='') {
        $this->db->where($chk_column, $chk_value);
        if(!empty($expt_value)){
            $this->db->where($expt_column.' !=', $expt_value);
        }
        $tot = $this->db
            ->where('status_id', 1)
            ->count_all_results($table);
        
        return $tot>0 ? TRUE : FALSE;
    }
    
	/*$data = [
		[
			'title' => 'My title',
			'name' => 'My Name',
			'date' => 'My date'
		],
		[
			'title' => 'Another title',
			'name' => 'Another Name',
			'date' => 'Another date'
		]
	];*/
    public function batchInsert($table, $data) {
        
        $this->db->insert_batch($table, $data);
    }
    
    public function commonInsert($table, $data) {
        $this->db->insert($table, $data);
       // $this->saveLog($this->db->last_query());
        return $this->db->insert_id();
    }
    
    public function commonUpdate($table, $set_data, $check_data, $version_option=FALSE) {
        foreach ($check_data as $key=>$val) {
            $this->db->where($key, $val);
        }
        if ($version_option) {
            $this->db->set('version', '`version`+1', FALSE);
        }
        $update = $this->db->update($table, $set_data);

       // $this->saveLog($this->db->last_query());
		
        if ($update) {
            return true;
        }
        return false;
    }
    
    public function commonDelete($table, $column, $value){
        $this->db->where($column, $value);
        $this->db->delete($table);
        //$this->saveLog($this->db->last_query());
    }
    
    public function saveLog($txt){
        $data = [
            'log' => $txt,
            'dtt_add' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('logs', $data);
    }
}
