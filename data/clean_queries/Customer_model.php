<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends CI_Model
{
    public $ob;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function list_cust()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);
        return $this->ob->get_where('C_BPARTNER', ['ISACTIVE' => 'Y'])->result();
        // return $this->ob->query("SELECT * FROM c_bpartner WHERE isactive = 'Y'")->result();
    }
    function list_all_cust()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("SELECT * FROM gai_outstanding ")->result();
    }
}
