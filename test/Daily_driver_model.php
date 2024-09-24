<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Daily_driver_model extends CI_Model
{

    private $table = 'ga_daily_driver';
    private $id = 'id';
    public function __construct()
    {
        parent::__construct();
    }

    public function cek($nik, $username)
    {
        $this->db->where('nik', $nik);
        $this->db->where('username', $username);
        $this->db->where('finish_at IS NULL', NULL);
        return $this->db->get($this->table)->row();
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function datatables($rowperpage, $start, $q = null, $username = null)
    {
        $this->db->select("*");
        if ($username) {
            $this->db->where('username', $username);
        }
        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('UPPER(fullname)', $q);
        $this->db->or_like('UPPER(nik)', $q);
        $this->db->or_like('UPPER(keterangan_finish)', $q);
        $this->db->or_like('UPPER(keterangan_start)', $q);
        $this->db->or_like('UPPER(CAST(start_at AS VARCHAR))', $q);
        $this->db->or_like('UPPER(CAST(finish_at AS VARCHAR))', $q);
        $this->db->group_end();
        $this->db->limit($rowperpage, $start);
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result();
    }

    public function totalRecorsWithFilter($q = null, $username = null)
    {
        $this->db->from($this->table);
        if ($username) {
            $this->db->where('username', $username);
        }
        $this->db->group_start();
        $this->db->like('UPPER(fullname)', $q);
        $this->db->or_like('UPPER(nik)', $q);
        $this->db->or_like('UPPER(keterangan_finish)', $q);
        $this->db->or_like('UPPER(keterangan_start)', $q);
        $this->db->or_like('UPPER(CAST(start_at AS VARCHAR))', $q);
        $this->db->or_like('UPPER(CAST(finish_at AS VARCHAR))', $q);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        return $this->db->count_all_results();
    }

    public function totalRecords($username = null)
    {
        $this->db->from($this->table);
        if ($username) {
            $this->db->where('username', $username);
        }
        $this->db->order_by('id', 'DESC');
        return $this->db->count_all_results();
    }

    public function get_for_excel($dari, $sampai, $driver = NULL)
    {
        $this->db->select("*");
        if ($driver) {
            $this->db->where('nik', $driver);
        }
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_driver()
    {
        $this->db->select("nik, fullname");
        $this->db->from($this->table);
        $this->db->where('id >', 5);
        $this->db->group_by('nik, fullname');
        $this->db->order_by('fullname', 'ASC');
        return $this->db->get()->result();
    }
}