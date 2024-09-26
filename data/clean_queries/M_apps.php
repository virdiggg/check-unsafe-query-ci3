<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_apps extends CI_Model {
    private $table = 'apps';
    private $user = 'apps_to_user';
    private $db;

    function infoSubmodule($endpoint)
    {
        $myDB = $this->load->database("default", TRUE);
        return $myDB->get_where("tbl_submodule", ['url_sub' => $endpoint])->row();
    }

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('default', TRUE);
    }

    public function getAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function get($row, $start, $search)
    {
        $this->db->select();

        if (!empty($search)) {
            $this->d->group_start();
                $this->d->or_like("UPPER(name)", strtoupper($search));
            $this->d->group_end();
        }

        $this->db->order_by('id', 'ASC');
        $this->db->limit($row, $start);
        return $this->db->get($this->table)->result();
    }

    public function find($id)
    {
        return $this->findBy($id, 'id');
    }

    public function findBy($param, $field = 'id')
    {
        return $this->db->get_where($this->table, [$field => $param])->row();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
    }

    public function insert($data)
    {
        $this->db->insert_batch($this->table, $data);
        return $this->db->insert_id();
    }
}