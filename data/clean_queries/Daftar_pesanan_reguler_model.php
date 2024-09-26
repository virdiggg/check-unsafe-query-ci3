<?php

class Daftar_pesanan_reguler_model extends CI_model
{

    function infoSubmodule($endpoint)
    {
        $myDB = $this->load->database("default", TRUE);
        return $myDB->get_where("tbl_submodule", ['url_sub' => $endpoint])->row();
    }

    function count_all_results($status_paket, $gudang, $search_from, $search_to)
    {
        $this->db->select('
        head.no_ref_move,
        head.gudang_name,
        head.no_resi,
        head.no_pesanan,
        head.waktu_pemesanan,
        head.status_pemesanan,
        head.status_pembatalan_or_pengembalian,
        head.pembeli,
        head.terakhir_dikirim,
        head.info_produk,
        head.opsi_pengiriman,
        head.nama_penerima,
        head.created_by,
        head.created_at');
        if (!empty($status_paket)) {
            $this->db->where('head.status_pemesanan', $status_paket);
        }
        if (!empty($gudang)) {
            $this->db->where('head.gudang_kode', $gudang);
        }
        if (!empty($search_from) && !empty($search_to)) {
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') >= '$search_from'");
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') <= '$search_to'");
        }
        $this->db->from('bd_daftar_pesanan_reguler head');
        $this->db->join('bd_daftar_pesanan_reguler_detail detail', 'detail.no_pesanan = head.no_pesanan', NULL);
        $this->db->join('bd_daftar_pesanan_reguler_detail_list_batch detail_batch', 'detail.id = detail_batch.id_detail_pesanan', NULL);
        return $this->db->count_all_results();
    }

    function total_record_with_filter($searchValue, $status_paket, $gudang, $search_from, $search_to)
    {
        $this->db->select('
        head.no_ref_move,
        head.gudang_name,
        head.no_resi,
        head.no_pesanan,
        head.waktu_pemesanan,
        head.status_pemesanan,
        head.status_pembatalan_or_pengembalian,
        head.pembeli,
        head.terakhir_dikirim,
        head.info_produk,
        head.opsi_pengiriman,
        head.nama_penerima,
        head.created_by,
        head.created_at,
        detail.nama_produk,
        detail.jumlah,
        detail.sku_induk');
        $this->db->from('bd_daftar_pesanan_reguler head');
        $this->db->join('bd_daftar_pesanan_reguler_detail detail', 'detail.no_pesanan = head.no_pesanan');
        $this->db->join('bd_daftar_pesanan_reguler_detail_list_batch detail_batch', 'detail.id = detail_batch.id_detail_pesanan');

        // $this->db->group_start();
        $this->db->like('CONCAT(UPPER(head.no_resi), UPPER(head.no_pesanan), UPPER(head.status_pemesanan), UPPER(head.status_pembatalan_or_pengembalian),UPPER(head.pembeli),UPPER(head.terakhir_dikirim),UPPER(head.info_produk), UPPER(head.opsi_pengiriman),
            UPPER(head.nama_penerima),
            UPPER(head.created_by),UPPER(head.no_ref_move),
            UPPER(head.gudang_name),UPPER(detail_batch.batch)) ', strtoupper($searchValue));
        // $this->db->group_end();
        if (!empty($status_paket)) {
            $this->db->where('head.status_pemesanan', $status_paket);
        }
        if (!empty($gudang)) {
            $this->db->where('head.gudang_kode', $gudang);
        }
        if (!empty($search_from) && !empty($search_to)) {
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') >= '$search_from'");
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') <= '$search_to'");
        }
        $this->db->order_by('1', 'DESC');

        return $this->db->count_all_results();
    }

    function results($searchValue, $rowperpage, $start, $status_paket, $gudang, $search_from, $search_to)
    {
        $this->db->select('
        head.no_ref_move,
        head.gudang_name,
        head.no_resi,
        head.no_pesanan,
        head.waktu_pemesanan,
        head.status_pemesanan,
        head.status_pembatalan_or_pengembalian,
        head.pembeli,
        head.terakhir_dikirim,
        head.info_produk,
        head.array_produk,
        head.opsi_pengiriman,
        head.nama_penerima,
        head.created_by,
        head.created_at,
        head.pick_by,
        head.pick_at,
        head.packing_by,
        head.packing_at,
        head.pick_status,
        head.move_at,
        head.move_by,
        head.diterima_pos_at,
        head.diterima_pos_by,
        head.diserahkan_expedisi_at,
        head.diserahkan_expedisi_by,
        detail.nama_produk,
        detail_batch.jumlah,
        detail.sku_induk,
        detail_batch.batch,
        head.packing_image


        ');
        $this->db->like('CONCAT(UPPER(head.no_resi), UPPER(head.no_pesanan), UPPER(head.status_pemesanan), UPPER(head.status_pembatalan_or_pengembalian),UPPER(head.pembeli),UPPER(head.terakhir_dikirim),UPPER(head.info_produk), UPPER(head.opsi_pengiriman),
        UPPER(head.nama_penerima),
        UPPER(head.created_by),  UPPER(head.no_ref_move),
        UPPER(head.gudang_name),UPPER(detail_batch.batch)) ', strtoupper($searchValue));
        // $this->db->group_end();
        if (!empty($status_paket)) {
            $this->db->where('head.status_pemesanan', $status_paket);
        }
        if (!empty($gudang)) {
            $this->db->where('head.gudang_kode', $gudang);
        }
        if (!empty($search_from) && !empty($search_to)) {
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') >= '$search_from'");
            $this->db->where("TO_CHAR(head.created_at,'YYYY-MM-DD') <= '$search_to'");
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($rowperpage, $start);

        $this->db->from('bd_daftar_pesanan_reguler head');
        $this->db->join('bd_daftar_pesanan_reguler_detail detail', 'detail.no_pesanan = head.no_pesanan');
        $this->db->join('bd_daftar_pesanan_reguler_detail_list_batch detail_batch', 'detail.id = detail_batch.id_detail_pesanan');

        return   $this->db->get()->result();
    }


    function export($status_paket, $gudang, $search_from, $search_to, $by_date)
    {
        $this->db->select('
        head.no_ref_move,
        head.gudang_name,
        head.no_resi,
        head.no_pesanan,
        head.waktu_pemesanan,
        head.status_pemesanan,
        head.status_pembatalan_or_pengembalian,
        head.pembeli,
        head.terakhir_dikirim,
        head.info_produk,
        head.array_produk,
        head.opsi_pengiriman,
        head.nama_penerima,
        head.created_by,
        head.created_at,
        head.pick_by,
        head.pick_at,
        head.packing_by,
        head.packing_at,
        head.pick_status,
        head.move_at,
        head.move_by,
        head.diterima_pos_at,
        head.diterima_pos_by,
        head.diserahkan_expedisi_at,
        head.diserahkan_expedisi_by,
        detail.nama_produk,
        detail_batch.jumlah,
        detail.sku_induk,
        detail_batch.batch,
        head.packing_image
        ');

        if (!empty($status_paket)) {
            $this->db->where('head.status_pemesanan', $status_paket);
        }
        if (!empty($gudang)) {
            $this->db->where('head.gudang_kode', $gudang);
        }
        if (!empty($search_from) && !empty($search_to)) {
            $this->db->where("TO_CHAR(head." . $by_date . ",'YYYY-MM-DD') >= '$search_from'");
            $this->db->where("TO_CHAR(head." . $by_date . ",'YYYY-MM-DD') <= '$search_to'");
        }
        $this->db->order_by('head.no_resi', 'ASC');

        $this->db->from('bd_daftar_pesanan_reguler head');
        $this->db->join('bd_daftar_pesanan_reguler_detail detail', 'detail.no_pesanan = head.no_pesanan');
        $this->db->join('bd_daftar_pesanan_reguler_detail_list_batch detail_batch', 'detail.id = detail_batch.id_detail_pesanan');

        return   $this->db->get()->result();
    }


    function gudang()
    {
        $this->ob = $this->load->database("ob", TRUE);
        $locator = $this->ob->query("select DISTINCT VALUE ,BARCODE, M_LOCATOR_ID from m_locator  where substr(VALUE,1,2)='09' AND  VALUE != '09' order by VALUE ")->result();
        return $locator;
    }

    function gudang_by_id($id)
    {
        $this->ob = $this->load->database("ob", TRUE);
        $locator = $this->ob->query("select DISTINCT VALUE ,BARCODE, M_LOCATOR_ID from m_locator  where M_LOCATOR_ID='$id'  ")->row();
        return $locator;
    }

    function count_market_place()
    {
        $this->db->distinct();
        $this->db->select('gudang_name');
        $this->db->from('bd_daftar_pesanan_reguler');
        return $this->db->count_all_results();
    }

    function count_customer_market_place()
    {
        $this->db->distinct();
        $this->db->select('pembeli');
        $this->db->from('bd_daftar_pesanan_reguler');
        return $this->db->count_all_results();
    }

    function count_customer_by_market_place($marketplace)
    {
        $this->db->distinct();
        $this->db->select('pembeli');
        $this->db->where('gudang_value', $marketplace);
        $this->db->from('bd_daftar_pesanan_reguler');
        return $this->db->count_all_results();
    }

    function count_produk_market_place()
    {
        $this->db->distinct();
        $this->db->select('nama_produk');
        $this->db->from('bd_daftar_pesanan_reguler_detail');
        return $this->db->count_all_results();
    }

    function count_paket_terkirim_market_place()
    {
        $this->db->where('status_pemesanan', 'diserahkan_expedisi');
        $this->db->from('bd_daftar_pesanan_reguler');

        return $this->db->count_all_results();
    }

    function count_produk_by_market_place($marketplace)
    {
        $this->db->distinct();
        $this->db->select('D.nama_produk');
        $this->db->where('gudang_value', $marketplace);
        $this->db->from('bd_daftar_pesanan_reguler_detail D');
        $this->db->join('bd_daftar_pesanan_reguler H', 'H.no_pesanan = D.no_pesanan');
        return $this->db->count_all_results();
    }
}
