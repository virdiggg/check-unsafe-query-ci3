<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_wh_market_return extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->ob = $this->load->database('ob', TRUE);
    }

    function select_where_in($idArr)
    {
        $this->db->select('*');
        $this->db->from('bd_penerimaan_return');
        $this->db->where_in('id', $idArr);
        return $this->db->get()->result_array();
    }

    function select_with_param($param)
    {
        $this->db->select('*');
        $this->db->from($param['table']);
        if (isset($param['where'])) {
            $this->db->where($param['where']);
        }
        if (isset($param['where_in'])) {
            $this->db->where_in($param['where_in']);
        }
        if (isset($param['order'])) {
            $this->db->order_by($param['order']);
        }
        if (isset($param['result'])) {
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result_array();
        }
    }

    public function insert_with_param($param)
    {
        $this->db->insert($param['table'], $param['data']);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function update_with_param($param)
    {
        $this->db->set($param['data']);
        $this->db->where($param['where']);
        $this->db->update($param['table']);
        return $this->db->affected_rows();
    }

    public function delete_with_param($param)
    {
        $this->db->where($param['where']);
        $this->db->delete($param['table']);
        return $this->db->affected_rows();
    }

    function totalRecords($status)
    {

        $this->db->select('*');
        $this->db->where('kirim_wh_at !=', NULL);

        if ($status == 'sudah') {
            $this->db->where('terima_wh_at !=', NULL);
            $this->db->where('kirim_qc_at =', NULL);
        } else if ($status == 'belum') {
            $this->db->where('terima_wh_at =', NULL);
        } else if ($status == 'kirim') {
            $this->db->where('kirim_qc_at !=', NULL);
            $this->db->where('status !=', '0');
        }

        $this->db->from('bd_penerimaan_return');
        return $this->db->count_all_results();
    }

    function totalRecorsWithFilter($searchValue, $status, $tanggal = NULL)
    {
        $this->db->select('*');
        $this->db->from('bd_penerimaan_return');

        if ($searchValue) {
            $this->db->like('CONCAT(UPPER(no_resi), UPPER(no_pesanan), UPPER(nama_produk), UPPER(no_so)) ', strtoupper($searchValue));
        }

        $this->db->where('kirim_wh_at !=', NULL);

        if ($status == 'sudah') {
            $this->db->where('terima_wh_at !=', NULL);
            $this->db->where('kirim_qc_at =', NULL);
        } else if ($status == 'belum') {
            $this->db->where('terima_wh_at =', NULL);
        } else if ($status == 'kirim') {
            $this->db->where('kirim_qc_at !=', NULL);
            $this->db->where('status !=', '0');
        }

        // if (!empty($tanggal)) {
        //     $this->db->where("TO_CHAR(diserahkan_expedisi_at,'YYYY-MM-DD')", $tanggal);
        // }
        // $this->db->order_by('1', 'DESC');

        return $this->db->count_all_results();
    }

    function results($searchValue, $rowperpage, $start, $status, $tanggal = NULL)
    {
        $this->db->select("bpr.no_so,bpr.no_resi,to_char(bpr.created_at,'yyyy-mm-dd') created_at, reg.gudang_name as marketplace, COUNT(bpr.nama_produk) as jumlah_item");
        $this->db->from('bd_penerimaan_return bpr');
        $this->db->join('bd_daftar_pesanan_reguler reg', 'bpr.no_pesanan=reg.no_pesanan', 'left');

        if ($searchValue) {
            $this->db->like('CONCAT(UPPER(bpr.no_resi), UPPER(bpr.no_pesanan), UPPER(reg.gudang_name)) ', strtoupper($searchValue));
        }

        $this->db->where('bpr.kirim_wh_at !=', NULL);

        if ($status == 'sudah') {
            $this->db->where('bpr.terima_wh_at !=', NULL);
            $this->db->where('bpr.kirim_qc_at =', NULL);
        } else if ($status == 'belum') {
            $this->db->where('bpr.terima_wh_at =', NULL);
        } else if ($status == 'kirim') {
            $this->db->where('bpr.kirim_qc_at !=', NULL);
            $this->db->where('bpr.status !=', '0');
        }

        $this->db->group_by('bpr.no_resi, bpr.no_so,reg.gudang_name, bpr.created_at');

        $this->db->limit($rowperpage, $start);

        return $this->db->get()->result();
    }

    public function locators($locatorID = [])
    {
        $this->ob->select('M_LOCATOR_ID AS id, VALUE AS text, BARCODE');
        $this->ob->from('M_LOCATOR');
        $this->ob->where('ISACTIVE', 'Y');
        $this->ob->order_by('VALUE', 'ASC');
        if (count($locatorID) > 0) {
            $this->ob->where_in('M_LOCATOR_ID', $locatorID);
        }
        return $this->ob->get()->result_array();
    }

    public function staggingTerima($noResi)
    {
        $this->db->select('bpr.*, batch.batch');
        $this->db->from('bd_penerimaan_return bpr');
        $this->db->join('bd_daftar_pesanan_reguler_detail_list_batch batch', 'bpr.id_detail_pesanan=batch.id_detail_pesanan', 'left');
        $this->db->where('bpr.no_resi', $noResi);
        return $this->db->get()->result();
    }
}
