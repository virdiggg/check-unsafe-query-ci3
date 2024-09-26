<?php defined('BASEPATH') or exit('No direct script access allowed');

class Chart_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        
    }

    public function last_month()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select mpc.name as m_product_id,
                    sum(col.qtyinvoiced) as qtyorder, sum(col.linenetamt) as linenetamt
                    from c_invoice ci, c_invoiceline col, m_product mp, m_product_category mpc  
                    where ci.issotrx='Y' and ci.docstatus='CO'
                    and ci.c_invoice_id=col.c_invoice_Id
                    and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(sysdate,'mm-yyyy')
                    and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
                    HAVING sum(col.linenetamt) > 10000000
                    group by mpc.name")->result();
    }
    public function before_month($monthyear, $monthyear_before)
    {

        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select x.categories, sum(x.penjualanM) AS sekarang, sum(x.penjualanM0) AS kemarin
        from (
        select case
                when y.linenetamtM>10000000 then to_char(y.nama)
                else to_char(y.nama) end as categories, sum(y.linenetamtM) penjualanM, 0 as penjualanM0
        from (
        		select mpc.name as nama,
                    sum(col.linenetamt) as linenetamtM, 0 as linenetamtM0
                    from c_invoice ci, c_invoiceline col, m_product mp, m_product_category mpc  
                    where ci.issotrx='Y' and ci.docstatus='CO'
                    and ci.c_invoice_id=col.c_invoice_Id
                    and to_char(ci.DATEORDERED,'mm-yyyy')='$monthyear'
                    and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
                    HAVING sum(col.linenetamt) > 10000000
                    group by mpc.name
                ) y
        group by (case
                when y.linenetamtM>10000000 then to_char(y.nama)
                else to_char(y.nama) end)
        UNION ALL
        select case
                when y.linenetamtM>10000000 then to_char(y.nama)
                else to_char(y.nama) end as categories, 0 as penjualanM, sum(y.linenetamtM) as penjualanM0
        from (
        		select mpc.name as nama,
                    sum(col.linenetamt) as linenetamtM, 0 as linenetamtM0
		            from c_invoice ci, c_invoiceline col, m_product mp, m_product_category mpc  
		            where ci.issotrx='Y' and ci.docstatus='CO' --and ci.c_bpartner_id='99881FDA57A44F47AD9E2C17039BB071' --[id customer yang dipilih]
		            and ci.c_invoice_id=col.c_invoice_Id
		            and to_char(ci.DATEORDERED,'mm-yyyy')='$monthyear_before'
		            and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
		            HAVING sum(col.linenetamt) > 10000000
		            group by mpc.name
                ) y
        group by (case
                when y.linenetamtM>10000000 then to_char(y.nama)
                else to_char(y.nama) end)
        ) x
        group by x.categories")->result();
    }
}
