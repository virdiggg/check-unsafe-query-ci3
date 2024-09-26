<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approve_action_mr_model extends CI_model {
	public function __construct()
	{
		parent::__construct();
        $this->load->database();
	}

    function totalRecords($mr)
    {
        # load database di masing2 function
        $this->db = $this->load->database("default", TRUE);

        $filter = $this->session->userdata('filter');

        if ($filter == 1) {

            if (count($mr) > 0) {
                $wherein = ' ';
                foreach ($mr as $key => $value) {
                    if (count($mr) - 1 == $key) {
                        $wherein .= " rcrm_mrvisit.kodemr ='". $value."' ) ";
                    } else {
                        if ($key == 0) {
                            $wherein .= "AND ( rcrm_mrvisit.kodemr ='". $value."' OR ";
                        } else {
                            $wherein .= " rcrm_mrvisit.kodemr ='". $value."' OR ";
                        }

                    }
                }
                return count($this->db->query("select approval_request_mr.*,rcrm_mrvisit.*  FROM approval_request_mr  JOIN rcrm_mrvisit ON rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id  where ( approval_request_mr.isapproved !='Y' OR approval_request_mr.isapproved IS NULL) ".$wherein)->result());
            } else {
                return array();
            }


        } else {
            return count($this->db->query("select approval_request_mr.*,rcrm_mrvisit.*  FROM approval_request_mr  JOIN rcrm_mrvisit ON rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id  where ( approval_request_mr.isapproved !='Y' OR approval_request_mr.isapproved IS NULL) ")->result());
        }

    }

    function totalRecorsWithFilter($mr, $searchvalue)
    {
         # load database di masing2 function
         $this->db = $this->load->database("default", TRUE);

         $filter = $this->session->userdata('filter');

        if ($filter == 1) {

             if (count($mr) > 0) {
                $searchvalue = strtoupper($searchvalue);
                $wherein = ' ';
                foreach ($mr as $key => $value) {
                    if (count($mr) - 1 == $key) {
                        $wherein .= " rcrm_mrvisit.kodemr ='". $value."' ) ";
                    } else {
                        if ($key == 0) {
                            $wherein .= "AND ( rcrm_mrvisit.kodemr ='". $value."' OR ";
                        } else {
                            $wherein .= " rcrm_mrvisit.kodemr ='". $value."' OR ";
                        }

                    }
                }
                return count($this->db->query("select approval_request_mr.*,rcrm_mrvisit.*  FROM approval_request_mr  JOIN rcrm_mrvisit ON rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id  where ( approval_request_mr.isapproved !='Y' OR approval_request_mr.isapproved IS NULL) ".$wherein)->result());

             }else{

                return array();
             }
        }else{
            $searchvalue = strtoupper($searchvalue);
            $wherein = ' ';
            return count($this->db->query("select approval_request_mr.*,rcrm_mrvisit.*  FROM approval_request_mr  JOIN rcrm_mrvisit ON rcrm_mrvisit.rcrm_mrvisit_id = approval_request_mr.rcrm_visit_id  where ( approval_request_mr.isapproved !='Y' OR approval_request_mr.isapproved IS NULL) ".$wherein)->result());

        }

    }

    function get_mr_by_user($user)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);

        $user = strtoupper($user);
        return $this->ob->query("select umr.username as namaUser, umr.name as nama, bp.value as kodeMR, bp.name as namaMR
            from ad_user umr, gai_salesrep_area sa, ad_user au, c_bpartner bp
            where umr.ad_user_id=sa.ad_user_id and sa.sales_rep=au.ad_user_id and au.c_bpartner_id=bp.c_bpartner_id
            and UPPER(umr.username)='$user' order by 1 ")->result();

    }

}
