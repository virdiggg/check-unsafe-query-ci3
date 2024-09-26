<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DApprove_action_mr_model extends CI_model {
	public function __construct()
	{
		parent::__construct();
        $this->load->database();
	}

    function get_all_results($wherein = "")
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);


        $this->db->select("approval_request_mr.*,rcrm_mrvisit.*")
        ->from("approval_request_mr")
        ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id')
        ->group_start()
        ->where('approval_request_mr.isapproved !=', 'Y')
        ->or_where('approval_request_mr.isapproved IS NULL', NULL, FALSE)
        ->group_end();

        if ($wherein != "") {
            $this->db->where('rcrm_mrvisit.kodemr', $wherein);
        }

        return  $this->db->get()->result();
    }


    function results($rowperpage,$start,$wherein, $searchvalue)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $filter = $this->session->userdata('filter');

        if ($filter == 1) {

             if (count($wherein) > 0) {
                $searchvalue = strtoupper($searchvalue);
                $this->db->select("approval_request_mr.*,approval_request_mr.type_request as typerequest,rcrm_mrvisit.alamat,rcrm_mrvisit.handphone,rcrm_mrvisit.signature,rcrm_mrvisit.tglvisit,rcrm_mrvisit.kodecustomer,rcrm_mrvisit.kodelead,rcrm_mrvisit.tipe_kunjungan,rcrm_mrvisit.keterangan")
                ->from("approval_request_mr")
                ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id','left')
                ->group_start()
                ->where('approval_request_mr.isapproved !=', 'Y')
                ->or_where('approval_request_mr.isapproved IS NULL', NULL, FALSE)
                ->group_end();

                if (count($wherein) > 0) {
                    $this->db->group_start();
                    $this->db->where('rcrm_mrvisit.kodemr', $wherein[0]);
                    foreach ($wherein as $key => $value) {
                        $this->db->or_where('rcrm_mrvisit.kodemr', $value);
                    }
                    $this->db->group_end();
                }

                $this->db->group_start();
                $this->db->like('upper(type_request)', $searchvalue);
                $this->db->or_like("to_char(tglvisit, 'yyyy-mm-dd')", $searchvalue);
                $this->db->or_like('upper(keterangan)', $searchvalue);
                $this->db->group_end();

                $this->db->order_by('tglvisit', 'DESC');
                $this->db->limit($rowperpage, $start);

                return  $this->db->get()->result();
             }else{
                return array();
             }
        }else{
            $searchvalue = strtoupper($searchvalue);
            $this->db->select("approval_request_mr.*,approval_request_mr.type_request as typerequest,rcrm_mrvisit.alamat,rcrm_mrvisit.handphone,rcrm_mrvisit.signature,rcrm_mrvisit.tglvisit,rcrm_mrvisit.kodecustomer,rcrm_mrvisit.kodelead,rcrm_mrvisit.tipe_kunjungan,rcrm_mrvisit.keterangan")
            ->from("approval_request_mr")
            ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id','left')
            ->group_start()
            ->where('approval_request_mr.isapproved !=', 'Y')
            ->or_where('approval_request_mr.isapproved IS NULL', NULL, FALSE)
            ->group_end();

            $this->db->group_start();
            $this->db->like('upper(type_request)', $searchvalue);
            $this->db->or_like("to_char(tglvisit, 'yyyy-mm-dd')", $searchvalue);
            $this->db->or_like('upper(keterangan)', $searchvalue);
            $this->db->group_end();

            $this->db->order_by('tglvisit', 'DESC');
            $this->db->limit($rowperpage, $start);

            return  $this->db->get()->result();
        }


    }

    function approve($id,$approveby)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $data = ['isapproved'=> 'Y', 'approvedby'=>$approveby ];
        $this->db->where('approval_request_mr_id',$id);
        $this->db->update('approval_request_mr',$data);
    }

    function get_data_approve($id)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);


        $this->db->select("approval_request_mr.approval_request_mr_id,
        approval_request_mr.rcrm_visit_id,
        approval_request_mr.isapproved,
        approval_request_mr.type_request,
        approval_request_mr.isopportunity,
        approval_request_mr.islead,
        rcrm_mrvisit.tglvisit,
        rcrm_mrvisit.kodemr,
        rcrm_mrvisit.startwork,
        rcrm_mrvisit.kodecustomer,
        rcrm_mrvisit.kodelead,
        rcrm_mrvisit.keterangan,
        rcrm_mrvisit.signature,
        rcrm_mrvisit.handphone,
        rcrm_mrvisit.tipe_kunjungan,
        rcrm_mrvisit.alamat,
        rcrm_mrvisit.email,
        rcrm_mrvisit.rcrm_opportunity_id,
        rcrm_mrvisit.endwork")
        ->from("approval_request_mr")
        ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id','left')
        ->where('approval_request_mr.approval_request_mr_id ', $id);
        return  $this->db->get()->result();

    }

    function get_lead_by_id($id)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $this->db->select("*")
        ->from("rcrm_lead")
        ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = rcrm_lead.rcrm_visit_id','left')
        ->where('rcrm_lead.rcrm_lead_id', $id);
        return  $this->db->get()->result();

    }

    function get_opportunity_by_id($id)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);


        // get by visit id
        $this->db->select("*")
        ->from("rcrm_opportunity")
        ->where('rcrm_visit_id', $id);
        return  $this->db->get()->result();

    }

    public function addLead($data)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);


                $this->db->insert('tbl_login', $data);
        return  $this->db->insert_id();
    }

    function flagLead($id,$data)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);


        $this->db->where('rcrm_lead_id',$id);
        $this->db->update('rcrm_lead',$data);
    }

    function flagOpportunity($id,$data)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $this->db->where('rcrm_lead_id',$id);
        $this->db->update('rcrm_lead',$data);
    }

    function detail($id)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $this->db->select("approval_request_mr.*,approval_request_mr.type_request as typerequest,rcrm_mrvisit.alamat,rcrm_mrvisit.email,rcrm_mrvisit.handphone,rcrm_mrvisit.signature,rcrm_mrvisit.tglvisit,rcrm_mrvisit.kodecustomer,rcrm_mrvisit.kodelead,rcrm_mrvisit.tipe_kunjungan,rcrm_mrvisit.keterangan")
        ->from("approval_request_mr")
        ->join('rcrm_mrvisit', 'rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id','left')
        ->group_start()
        ->where('approval_request_mr.isapproved !=', 'Y')
        ->or_where('approval_request_mr.isapproved IS NULL', NULL, FALSE)
        ->group_end()
        ->where('approval_request_mr.approval_request_mr_id', $id);

        return  $this->db->get()->result();
    }
}
