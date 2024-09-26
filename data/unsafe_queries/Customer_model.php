<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends CI_Model
{
    public $ob;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    // search by code or cust name
    function Cust_info($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $para = strtoupper($para);
        return $this->ob->query("select area.name as cabang, sarea.name as area, bp.value as kodeMR, bp.name as namaMR, cus.value as kodeCustomer, cus.name as namaCustomer, cod.grandtotal,
        col.m_product_id, col.qtyorder, col.linenetamt, cus.c_bpartner_id
        from c_bpartner bp
               left join c_bpartner cus on bp.c_bpartner_id=cus.salesrep_id
               left join (select co.c_bpartner_id as c_bpartner_id, sum(co.grandtotal) as grandtotal
                     from c_order co where to_char(co.dateordered,'yyyy-mm') = to_char(sysdate,'yyyy-mm')
                     group by co.c_bpartner_id) cod on cus.c_bpartner_id=cod.c_bpartner_id
               left join (select col.c_bpartner_id, mp.value as m_product_id, sum(col.qtyordered) as qtyorder, sum(col.linenetamt) as linenetamt
                     from c_order co, c_orderline col, m_product mp
                     where co.c_order_id=col.c_order_id and mp.m_product_id=col.m_product_id
                     group by col.c_bpartner_id, mp.value) col on col.c_bpartner_id=cus.c_bpartner_id,
             gai_partner_area pa
              left join gai_master_area area on pa.gai_master_area_id=area.gai_master_area_id
              left join gai_master_subarea sarea on pa.gai_master_subarea_id=sarea.gai_master_subarea_id
        where bp.issalesrep='Y' and bp.c_bpartner_id=pa.c_bpartner_id
        and (upper(cus.value) LIKE '%$para%' OR upper(cus.name) LIKE '%$para%')
        order by 1, 2, 3, 5, 7
        ")->result();
    }

    function Last_order($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $para = strtoupper($para);
        return $this->ob->query("select max(ci.dateordered) as last_order from c_invoice ci
        left join c_bpartner cus on ci.c_bpartner_id = cus.c_bpartner_id
        where ci.issotrx='Y' and ci.docstatus='CO' and cus.c_bpartner_id = '$para'
        order by ci.dateordered desc")->row();
    }

    function Total_order($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $para = strtoupper($para);
        return $this->ob->query("select sum(totallines) AS grandtotal from c_invoice
        where issotrx='Y' and docstatus='CO' and c_bpartner_id='$para'
        and to_char(DATEORDERED,'mm-yyyy')=to_char(sysdate,'mm-yyyy')")->row();
    }

    function Total_order_last_month($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $para = strtoupper($para);
        return $this->ob->query("select sum(totallines) AS grandtotal from c_invoice
        where issotrx='Y' and docstatus='CO' and c_bpartner_id='$para'
        and to_char(DATEORDERED,'mm-yyyy')=to_char(LAST_DAY(ADD_MONTHS(sysdate, -1)),'mm-yyyy')")->row();
    }

    function Total_outstanding($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $para = strtoupper($para);
        return $this->ob->query("select sum(grandtotal-totalpaid) AS outstanding from c_invoice
        where issotrx='Y' and docstatus='CO' and c_bpartner_id='$para'")->row();
    }

    function Total_order_per_product($para)
    {

        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select mpc.name as m_product_id,
        sum(col.qtyinvoiced) as qtyorder, sum(col.linenetamt) as linenetamt
                     from c_invoice ci, c_invoiceline col, m_product mp, m_product_category mpc
                     where ci.issotrx='Y' and ci.docstatus='CO' and ci.c_bpartner_id='$para' --[id customer yang dipilih]
                     and ci.c_invoice_id=col.c_invoice_Id
                     and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(sysdate,'mm-yyyy')
                     and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
                     group by mpc.name")->result();
    }

    function Total_order_per_product_last_month($para)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select mpc.name as m_product_id,
        sum(col.qtyinvoiced) as qtyorder, sum(col.linenetamt) as linenetamt
                     from c_invoice ci, c_invoiceline col, m_product mp, m_product_category mpc
                     where ci.issotrx='Y' and ci.docstatus='CO' and ci.c_bpartner_id='$para' --[id customer yang dipilih]
                     and ci.c_invoice_id=col.c_invoice_Id
                     and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(LAST_DAY(ADD_MONTHS(sysdate, -1)),'mm-yyyy')
                     and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
                     group by mpc.name")->result();
    }

    function Last_month_cust_trend()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("-- last month customer trend
        select bp.value, bp.name, sum(col.linenetamt) as linenetamt
                     from c_invoice ci, c_invoiceline col, c_bpartner bp --, m_product_category mpc
                     where ci.issotrx='Y' and ci.docstatus='CO' --and ci.c_bpartner_id='99881FDA57A44F47AD9E2C17039BB071' --[id customer yang dipilih]
                     and ci.c_invoice_id=col.c_invoice_Id
                     and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(LAST_DAY(ADD_MONTHS(sysdate, -1)),'mm-yyyy')
                     and ci.c_bpartner_id=bp.c_bpartner_id
                     --and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
                     group by bp.value, bp.name
                     having sum(col.linenetamt) > 300000000
                     order by 3 desc
        ")->result();
    }

    function This_month_cust_trend()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select bp.value, bp.name, sum(col.linenetamt) as linenetamt
        from c_invoice ci, c_invoiceline col, c_bpartner bp --, m_product_category mpc
        where ci.issotrx='Y' and ci.docstatus='CO' --and ci.c_bpartner_id='99881FDA57A44F47AD9E2C17039BB071' --[id customer yang dipilih]
        and ci.c_invoice_id=col.c_invoice_Id
        and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(sysdate,'mm-yyyy')
        and ci.c_bpartner_id=bp.c_bpartner_id
        --and mp.m_product_id=col.m_product_id  and mp.m_product_category_id=mpc.m_product_category_id
        group by bp.value, bp.name
        having sum(col.linenetamt) > 300000000
        order by 3 desc
        ")->result();
    }

    function trends()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select x.namacustomer, sum(x.penjualanM) AS sekarang, sum(x.penjualanM0) AS kemarin
        from (
        select case
                when y.linenetamtM<300000000 then 'Gab Cus Les300Jt'
                else to_char(y.nama) end as namacustomer, sum(y.linenetamtM) penjualanM, 0 as penjualanM0
        from (
                select bp.name as nama, sum(col.linenetamt) as linenetamtM, 0 as linenetamtM0
                     from c_invoice ci, c_invoiceline col, c_bpartner bp --, m_product_category mpc
                     where ci.issotrx='Y' and ci.docstatus='CO' --and ci.c_bpartner_id='99881FDA57A44F47AD9E2C17039BB071' --[id customer yang dipilih]
                     and ci.c_invoice_id=col.c_invoice_Id
                     and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(sysdate,'mm-yyyy')
                     and ci.c_bpartner_id=bp.c_bpartner_id
                     group by bp.name
                ) y
        group by (case
                when y.linenetamtM<300000000 then 'Gab Cus Les300Jt'
                else to_char(y.nama) end)
        union all
        select case
                when y.linenetamtM<300000000 then 'Gab Cus Les300Jt'
                else to_char(y.nama) end as namacustomer, 0 penjualanM, sum(y.linenetamtM) as penjualanM0
        from (
                select bp.name as nama, sum(col.linenetamt) as linenetamtM, 0 as linenetamtM0
                     from c_invoice ci, c_invoiceline col, c_bpartner bp --, m_product_category mpc
                     where ci.issotrx='Y' and ci.docstatus='CO' --and ci.c_bpartner_id='99881FDA57A44F47AD9E2C17039BB071' --[id customer yang dipilih]
                     and ci.c_invoice_id=col.c_invoice_Id
                     and to_char(ci.DATEORDERED,'mm-yyyy')=to_char(LAST_DAY(ADD_MONTHS(sysdate, -1)),'mm-yyyy')
                     and ci.c_bpartner_id=bp.c_bpartner_id
                     group by bp.name
                ) y
        group by (case
                when y.linenetamtM<300000000 then 'Gab Cus Les300Jt'
                else to_char(y.nama) end)
        ) x
        group by x.namacustomer")->result();
    }

    function cash_flow()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("select to_char(paymentdate,'mm') as M, sum(decode(fp.isreceipt,'Y',fp.amount, (fp.amount*-1))) cf
        from fin_payment fp
        where --fp.isreceipt ='N'and
        to_char(paymentdate,'yyyy')=to_char(sysdate,'yyyy')
        group by to_char(paymentdate,'mm')
        order by 1
        ")->result();
    }

    function list_cust_by_kodemr($kodemr)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("SELECT * FROM gai_outstanding WHERE upper(salesrep_id) ='$kodemr'")->result();
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

    function list_faktur_by_kodemr_and_cust($kodemr, $kode_cust)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query(" SELECT AO.NAME AS ORGANISASI, CUS.VALUE KODECUST, CUS.NAME NAMACUST, CP.VALUE SALESREP_ID, CP.NAME SALESREP_NAME, CI.DOCUMENTNO FAKTUR, CI.DATEINVOICED TGLFAKTUR, FS.DUEDATE,
        CASE WHEN FS.DUEDATE < SYSDATE THEN FS.OUTSTANDINGAMT ELSE 0 END AS OVERDUE,
        CASE WHEN (FS.DUEDATE BETWEEN SYSDATE AND (SYSDATE+14)) THEN FS.OUTSTANDINGAMT ELSE 0 END AS NEXT14DAY,
        FS.OUTSTANDINGAMT PIUTANG
        FROM FIN_PAYMENT_SCHEDULEDETAIL SD, FIN_PAYMENT_SCHEDULE FS, C_INVOICE CI, AD_USER AU, C_BPARTNER CP, C_BPARTNER CUS, AD_ORG AO
        WHERE SD.FIN_PAYMENT_DETAIL_ID IS NULL
        AND FS.OUTSTANDINGAMT > 0
        AND SD.FIN_PAYMENT_SCHEDULE_INVOICE=FS.FIN_PAYMENT_SCHEDULE_ID
        AND FS.C_INVOICE_ID=CI.C_INVOICE_ID
        AND CI.ISSOTRX='Y' -- UNTUK AR
        AND CI.SALESREP_ID=AU.AD_USER_ID
        AND AU.C_BPARTNER_ID=CP.C_BPARTNER_ID
        AND CI.C_BPARTNER_ID=CUS.C_BPARTNER_ID
        AND SD.AD_ORG_ID=AO.AD_ORG_ID
        AND TRIM(UPPER(CP.VALUE)) ='$KODEMR' AND TRIM(UPPER(CUS.VALUE)) ='$KODE_CUST'
        AND CASE WHEN FS.DUEDATE < SYSDATE THEN FS.OUTSTANDINGAMT ELSE 0 END > 0
       
       
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto10 sd, fin_payment_schedule@linkto10 fs, c_invoice@linkto10 ci, ad_user@linkto10 au, c_bpartner@linkto10 cp, c_bpartner@linkto10 cus, ad_org@linkto10 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0
        
        
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto20 sd, fin_payment_schedule@linkto20 fs, c_invoice@linkto20 ci, ad_user@linkto20 au, c_bpartner@linkto20 cp, c_bpartner@linkto20 cus, ad_org@linkto20 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0

		
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto30 sd, fin_payment_schedule@linkto30 fs, c_invoice@linkto30 ci, ad_user@linkto30 au, c_bpartner@linkto30 cp, c_bpartner@linkto30 cus, ad_org@linkto30 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0

		
        
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto40 sd, fin_payment_schedule@linkto40 fs, c_invoice@linkto40 ci, ad_user@linkto40 au, c_bpartner@linkto40 cp, c_bpartner@linkto40 cus, ad_org@linkto40 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0
 	    
        
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto50 sd, fin_payment_schedule@linkto50 fs, c_invoice@linkto50 ci, ad_user@linkto50 au, c_bpartner@linkto50 cp, c_bpartner@linkto50 cus, ad_org@linkto50 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0

	    
        -- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto60 sd, fin_payment_schedule@linkto60 fs, c_invoice@linkto60 ci, ad_user@linkto60 au, c_bpartner@linkto60 cp, c_bpartner@linkto60 cus, ad_org@linkto60 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0

		-- UNION ALL
		-- 		select ao.name as organisasi, cus.value kodecust, cus.name namacust, cp.value salesrep_id, cp.name salesrep_name, ci.documentno faktur, ci.dateinvoiced tglfaktur, fs.duedate,
        -- case when fs.duedate < sysdate then fs.outstandingamt else 0 end as overdue,
        -- case when (fs.duedate between sysdate and (sysdate+14)) then fs.outstandingamt else 0 end as next14day,
        -- fs.outstandingamt piutang
        -- from FIN_Payment_ScheduleDetail@linkto70 sd, fin_payment_schedule@linkto70 fs, c_invoice@linkto70 ci, ad_user@linkto70 au, c_bpartner@linkto70 cp, c_bpartner@linkto70 cus, ad_org@linkto70 ao
        -- where sd.fin_payment_detail_id is null
        -- and fs.outstandingamt > 0
        -- and sd.fin_payment_schedule_invoice=fs.fin_payment_schedule_id
        -- and fs.c_invoice_Id=ci.c_invoice_Id
        -- and ci.issotrx='Y' -- untuk AR
        -- and ci.salesrep_id=au.ad_user_id
        -- and au.c_bpartner_id=cp.c_bpartner_id
        -- and ci.c_bpartner_id=cus.c_bpartner_id
        -- and sd.ad_org_id=ao.ad_org_id
        -- and trim(upper(cp.value)) ='$kodemr' and trim(upper(cus.value)) ='$kode_cust'
        -- and case when fs.duedate < sysdate then fs.outstandingamt else 0 end > 0
       ")->result();
    
    }

    // total outstanding
    function totalosd($kodemr)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        return $this->ob->query("SELECT SUM(osdlewat) AS totalosdlewat, SUM(osd14days) AS total14days, SUM(allosd) AS totalall FROM gai_outstanding WHERE upper(salesrep_id)='$kodemr'")->row();
    }

    function totalosd_new($kodemr,$customer)
    {

        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        if ($kodemr!='' && $customer == '' ) {
            return $this->ob->query("SELECT SUM(osdlewat) AS totalosdlewat, SUM(osd14days) AS total14days, SUM(allosd) AS totalall FROM gai_outstanding WHERE upper(salesrep_id)='$kodemr'")->row();
        } elseif ($kodemr =='' && $customer != '' ) {
            return $this->ob->query("SELECT SUM(osdlewat) AS totalosdlewat, SUM(osd14days) AS total14days, SUM(allosd) AS totalall FROM gai_outstanding WHERE upper(kodecust)='$customer'")->row();
        }elseif ($kodemr =='' && $customer == '' ) {
            return $this->ob->query("SELECT SUM(osdlewat) AS totalosdlewat, SUM(osd14days) AS total14days, SUM(allosd) AS totalall FROM gai_outstanding ")->row();

        }else {
            return $this->ob->query("SELECT SUM(osdlewat) AS totalosdlewat, SUM(osd14days) AS total14days, SUM(allosd) AS totalall FROM gai_outstanding WHERE upper(salesrep_id)='$kodemr' and upper(kodecust)='$customer'")->row();

        }
    }

    /**
     * @var int|string $angka
     *
     * @return string
     *  9.000.000
     */
    function format_ribuan($angka) {
		return number_format($angka,0,',','.');
    }

    /**
     * @var int|string $angka
     *
     * @return string
     *  9,000,000.00
     */
    function rupiah_excel($angka) {
		return number_format($angka,2,'.',',');
	}

      // jika punya mr maka itu atasan
    // apa bila tidak punya itu MR
    public function cek_mr_atau_bukan($user)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $user = strtoupper($user);
        return $this->ob->query("select umr.username as namaUser, umr.name as nama, bp.value as kodeMR, bp.name as namaMR
            from ad_user umr, gai_salesrep_area sa, ad_user au, c_bpartner bp
            where umr.ad_user_id=sa.ad_user_id and sa.sales_rep=au.ad_user_id and au.c_bpartner_id=bp.c_bpartner_id
            and UPPER(umr.username)='$user' order by 3 ")->result();
    }

    function get_all_mr()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $user = strtoupper($this->session->userdata('username')) ;
        $check_user = $this->cek_mr_atau_bukan($user);

        $id_group = $this->session->userdata('id_group');
        $filter = $this->session->userdata('filter');


        if ($id_group == 1 || $filter == 0) {
            return $this->ob->query(" select   bp.value as kodeMR,  bp.name as namaMR
            from c_bpartner bp
            order by 1 ")->result();
        }else{

            if (count($check_user) > 0 ) {
                return $this->ob->query("select * from gai_userdanmr mr where
                --x.kodeMR=mr.kodemrc and
                UPPER(mr.namauser) = '$user' order by 3 ")->result();
            }else{
                return $this->ob->query("select * from gai_userdanmr mr where
                --x.kodeMR=mr.kodemr and
                UPPER(mr.namauser) = '$user' order by 3 ")->result();
            }

        }
    }

    function get_customer()
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $user = $this->session->userdata('username');

        if ($user = 'admin') {
            return $this->ob->query(" select * from rcrm_businesspartner  order by namapartner")->result();
        }else{

            $check_user = $this->cek_mr_atau_bukan($user);

            if ( count($check_user) > 0) {
                $where_in =" ( ";
                for ($i=0; $i < count($check_user) ; $i++) {

                    if ($i == 0 ) {
                        $where_in .=" trim(upper(kodemr)) like '".$check_user[$i]->KODEMR."'";
                    }else{
                        $where_in .=" or trim(upper(kodemr)) like '".$check_user[$i]->KODEMR."'";

                    }
                }
                $where_in .=" ) ";
                return $this->ob->query(" select * from rcrm_businesspartner where $where_in order by namapartner")->result();
            }else{
                $user = trim(strtoupper($user));
                return $this->ob->query(" select * from rcrm_businesspartner where trim(upper(kodemr)) like '$user' order by namapartner")->result();

            }


        }
    }


    function get_customer_by_mr($user)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $user = trim(strtoupper($user));
        return $this->ob->query(" select * from rcrm_businesspartner where trim(upper(kodemr)) like '$user' order by namapartner")->result();

    }

    function get_list_by_mr_customer($kodemr,$customer)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        if ($kodemr !='' && $customer == '' ) {

            return $this->ob->query("SELECT * FROM gai_outstanding WHERE upper(salesrep_id) ='$kodemr'")->result();

        } elseif ($kodemr =='' && $customer != '' ) {
            return $this->ob->query("SELECT * FROM gai_outstanding WHERE upper(kodecust) ='$customer'")->result();

        }elseif ($kodemr =='' && $customer == '' ) {
            $kodemr = $this->session->userdata('username');

            $cek_mr = $this->cek_mr_atau_bukan($kodemr);
            if (count($cek_mr) > 0) {
                $where = " Where (";
                foreach ($cek_mr as $key => $value) {

                    if ($key==0) {
                        $where .= "  upper(salesrep_id) =upper('$value->KODEMR')";
                    } else {
                        $where .= " OR  upper(salesrep_id) =upper('$value->KODEMR')";
                    }

                }

                $where .= ")";

                return $this->ob->query("SELECT * FROM gai_outstanding $where")->result();

            } else {
            return $this->ob->query("SELECT * FROM gai_outstanding ")->result();

            }

        }else {

            return $this->ob->query("SELECT * FROM gai_outstanding WHERE  upper(salesrep_id) ='$kodemr' and upper(kodecust) ='$customer'")->result();

        }
    }

    function get_nama_cust_by_code($kode_cust)
    {

        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


        $kode_cust = trim(strtoupper($kode_cust));
        return $this->ob->query(" select * from c_bpartner where value='$kode_cust'")->result();

    }

    function carimr($key)
    {
        # load database di masing2 function
        $this->ob = $this->load->database("ob", TRUE);


            $user =$this->session->userdata('username');
            $user = strtoupper($user);
            $id_group = $this->session->userdata('id_group');
            $filter = $this->session->userdata('filter');
            $key = strtoupper($key);
            if ($filter !=0 || $id_group == 1) {
                if ($key == '') {
                    return $this->ob->query(" select   bp.value as kodeMR,  bp.name as namaMR
                            from c_bpartner bp where  bp.isactive ='Y' and bp.issalesrep='Y' and bp.iscustomer='N' and bp.isemployee='Y'
                            order by 1 ")->result();
                } else {
                     return $this->ob->query(" select   bp.value as kodeMR,  bp.name as namaMR
                                from c_bpartner bp where UPPER(CONCAT(bp.value, bp.name)) like '%$key%'  and bp.isactive ='Y' and bp.issalesrep='Y' and bp.iscustomer='N' and bp.isemployee='Y'
                                order by 1 ")->result();
                }

            } else {
                if ($key=='') {
                    return $this->ob->query("select umr.username as namaUser, umr.name as nama, bp.value as kodeMR, bp.name as namaMR
                    from ad_user umr, gai_salesrep_area sa, ad_user au, c_bpartner bp
                    where umr.ad_user_id=sa.ad_user_id and sa.sales_rep=au.ad_user_id and au.c_bpartner_id=bp.c_bpartner_id and bp.isactive ='Y' and bp.issalesrep='Y' and bp.iscustomer='N' and bp.isemployee='Y'
                    and UPPER(umr.username)='$user'  order by 1 ")->result();
                } else {
                    return $this->ob->query("select umr.username as namaUser, umr.name as nama, bp.value as kodeMR, bp.name as namaMR
                    from ad_user umr, gai_salesrep_area sa, ad_user au, c_bpartner bp
                    where umr.ad_user_id=sa.ad_user_id and sa.sales_rep=au.ad_user_id and au.c_bpartner_id=bp.c_bpartner_id and bp.isactive ='Y' and bp.issalesrep='Y' and bp.iscustomer='N' and bp.isemployee='Y'
                    and UPPER(umr.username)='$user' and UPPER(CONCAT(bp.value, bp.name)) like '%$key%' order by 1 ")->result();
                }
            }
    }
}
