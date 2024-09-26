<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_Self_Presence extends CI_model
{
    /**
     * Tabel utama yang dipake di model ini
     * 
     * @return string
     */
    private $table = 'self_presence';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil self_presence berdasarkan id
     * 
     * @param int|string $val
     * 
     * @return object|null
     */
    public function find($val)
    {
        return $this->findBy($val);
    }

    /**
     * Ambil self_presence berdasarkan $var
     * 
     * @param mixed $val
     * 
     * @return object|null
     */
    public function findBy($val)
    {
        if (!is_array($val)) {
            $where = ['id' => $val];
        } else {
            $where = $val;
        }

        return $this->db->select()->from($this->table)->where($where)->get()->row();
    }

    /**
     * Insert ke table self_presence
     * 
     * @param array $param
     * 
     * @return object
     */
    public function insert($param)
    {
        $this->db->insert($this->table, $param);
        return $this->find($this->db->insert_id());
    }

    /**
     * Insert ke table self_presence kalo gak ada record-nya, kalo ada ambil aja.
     * 
     * @param array $param
     * 
     * @return array
     */
    public function firstOrCreate($param)
    {
        $where = [
            "TO_CHAR(created_at,'YYYY-MM-DD')" => date('Y-m-d'),
            'nik' => $param['nik'],
        ];
        if ($result = $this->findBy($where)) {
            return [
                'status' => false,
                'data' => $result,
            ];
        }

        $this->db->insert($this->table, $param);
        return [
            'status' => true,
            'data' => $this->find($this->db->insert_id()),
        ];
    }

    /**
     * Ambil semua presensi dengan limit
     *
     * @param int|string      $length
     * @param int|string      $start
     * @param int|string|null $q
     * 
     * @return array
     */
    public function datatables($length, $start, $q = null)
    {
        return $this->db->select("id, nik, CONCAT(suhu_tubuh, '°') AS suhu_tubuh, latitude, longitude, created_at")
            ->from($this->table)
            ->group_start()
                ->like('nik', $q)
            ->group_end()
            ->order_by('id', 'asc')
            ->limit($length, $start)
            ->get()->result();
    }

    /**
     * Hitung semua presensi dengan search
     *
     * @param int|string $q
     * 
     * @return array
     */
    public function totalRecordsWithFilter($q = null)
    {
        return $this->db->select('id')
            ->from($this->table)
            ->group_start()
                ->like('nik', $q)
            ->group_end()
            ->count_all_results();
    }

    /**
     * Hitung semua presensi
     *
     * @return array
     */
    public function totalRecords()
    {
        return $this->db->select('id')->from($this->table)->count_all_results();
    }
}