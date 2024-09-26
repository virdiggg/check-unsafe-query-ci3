<?php

class Sales_target_omset_model extends CI_model
{

    public function getNameMr($cabang)
    {
        $myOB = $this->load->database("ob", TRUE);
        $where_cabang = $cabang != '' ? " WHERE TRIM(CABANG)='" . trim($cabang) . "'" : "";
        $getAreaMr  =  $myOB->query("select DISTINCT MRCODE,MRNAME FROM gai_kacapspvmr " . $where_cabang)->result();
        return $getAreaMr;
    }
    public function getAreaMr($cabang)
    {
        $myOB = $this->load->database("ob", TRUE);
        $where_cabang = $cabang != '' ? " WHERE TRIM(CABANG)='" . trim($cabang) . "'" : "";
        $getAreaMr  =  $myOB->query("select DISTINCT AREA,MRCODE FROM gai_kacapspvmr " . $where_cabang)->result();
        return $getAreaMr;
    }
    public function getAreaSv($cabang)
    {
        $myOB = $this->load->database("ob", TRUE);
        $where_cabang = $cabang != '' ? " WHERE TRIM(CABANG)='" . trim($cabang) . "'" : "";
        $getAreaMr  =  $myOB->query("select DISTINCT trim(AREA) AS AREA,SPV FROM gai_kacapspvmr " . $where_cabang)->result();
        return $getAreaMr;
    }

    public function getKacapCabang($cabang)
    {
        $myOB = $this->load->database("ob", TRUE);
        $where_cabang = $cabang != '' ? " WHERE TRIM(CABANG)='" . trim($cabang) . "'" : "";
        $getAreaMr  =  $myOB->query("select DISTINCT TRIM(CABANG) AS CABANG,KACAP FROM gai_kacapspvmr  " . $where_cabang)->result();
        return $getAreaMr;
    }


    public function getCabangAndAreasNew($cabang)
    {

        $myOB = $this->load->database("ob", TRUE);
        $where_cabang = $cabang != '' ? " WHERE CABANGCODE='" . trim($cabang) . "'" : "";
        $cabang_s =  $myOB->query("select DISTINCT CABANGCODE AS CABANG,CABANG AS CABANG_NAME from GAI_STRUKTURMR " . $where_cabang)->result();
        $fix = [];
        foreach ($cabang_s as $key => $value) {
            $areas  =  $myOB->query("select DISTINCT AREA FROM gai_kacapspvmr WHERE TRIM(CABANG) like '" . $value->CABANG . "'")->result();
            $mr     =  $myOB->query("select DISTINCT MRCODE FROM gai_kacapspvmr JOIN C_bpartner on C_bpartner.VALUE=gai_kacapspvmr.MRCODE
            WHERE C_bpartner.ISACTIVE='Y'  AND TRIM(CABANG) like '" . $value->CABANG . "' ")->result();
            $fix[] = [
                'cabang' => $value->CABANG,
                'areas' => array_column($areas, 'AREA'),
                'salesrepcode' => array_column($mr, 'MRCODE'),
            ];
        }
        $myReturn = (object) $fix;

        return $myReturn;
    }
    function getOmsetTargets_ethical($monthyear)
    {
        # load ob database
        $myOB = $this->load->database("ob", TRUE);

        return $myOB->query("select bulan,salesrepcode,namasales , fakturs, MAX(target_sales) as target , SUM(pencapaian2) as pencapaian_sales, SUM(em_gai_totalsponsorship) as diskon   from (
        SELECT
         to_char( ci.dateinvoiced, 'MM-YYYY' ) AS bulan,
         sum(st.nilai) as target_sales,
         mr.value AS salesrepcode,
         mr.name AS namasales,
         substr( ci.documentno, 1, 2 ) as fakturs,
         stp.nilai AS targetproduct,
         ci.C_Invoice_ID,
         ( GAI_GET_TOTALLINE ( ci.C_Invoice_ID ) ) AS pencapaian2,
         ci.em_gai_totalsponsorship
         FROM
         c_invoice ci
         LEFT JOIN c_order co ON ci.c_order_id = co.c_order_id
         JOIN ad_user au ON au.ad_user_id = ci.salesrep_id
         JOIN c_bpartner mr ON mr.c_bpartner_id = au.c_bpartner_id
         LEFT JOIN gai_salestarget st ON st.ad_user_id = au.ad_user_id
         AND EXISTS ( SELECT 1 FROM c_period cp WHERE cp.c_period_id = st.c_period_id AND ci.dateinvoiced BETWEEN cp.startdate AND cp.enddate )

         LEFT JOIN gai_stproduct stp ON st.gai_salestarget_id = stp.gai_salestarget_id

        WHERE
         ci.issotrx = 'Y'
         AND to_char( ci.dateinvoiced, 'MM-YYYY' ) = '$monthyear'
         AND ci.docstatus = 'CO'
         AND substr( ci.documentno, 1, 2 )='DE'
         GROUP BY

         mr.value,
         mr.name,
         to_char( ci.dateinvoiced, 'MM-YYYY' ),
         ci.C_Invoice_ID,
         ci.em_gai_totalsponsorship,
         stp.nilai,
         substr( ci.documentno, 1, 2 )) x
         GROUP BY bulan,salesrepcode,namasales , fakturs
         ")->result();
    }


    function getOmsetTargets_retail($monthyear)
    {
        # load ob database
        $myOB = $this->load->database("ob", TRUE);

        return $myOB->query("
         select bulan,salesrepcode,namasales , fakturs, MAX(target_sales) as target , SUM(pencapaian2) as pencapaian_sales, SUM(em_gai_totalsponsorship) as diskon   from (
         SELECT
         to_char( ci.dateinvoiced, 'MM-YYYY' ) AS bulan,
         sum(st.nilai) as target_sales,
         mr.value AS salesrepcode,
         mr.name AS namasales,
         substr( ci.documentno, 1, 2 ) as fakturs,
         stp.nilai AS targetproduct,
         ci.C_Invoice_ID,
         ( GAI_GET_TOTALLINE ( ci.C_Invoice_ID ) ) AS pencapaian2,
         ci.em_gai_totalsponsorship
         FROM
         c_invoice ci
         LEFT JOIN c_order co ON ci.c_order_id = co.c_order_id
         JOIN ad_user au ON au.ad_user_id = ci.salesrep_id
         JOIN c_bpartner mr ON mr.c_bpartner_id = au.c_bpartner_id
         LEFT JOIN gai_salestarget st ON st.ad_user_id = au.ad_user_id
         AND EXISTS ( SELECT 1 FROM c_period cp WHERE cp.c_period_id = st.c_period_id AND ci.dateinvoiced BETWEEN cp.startdate AND cp.enddate )

         LEFT JOIN gai_stproduct stp ON st.gai_salestarget_id = stp.gai_salestarget_id

        WHERE
         ci.issotrx = 'Y'
         AND to_char( ci.dateinvoiced, 'MM-YYYY' ) = '$monthyear'
         AND ci.docstatus = 'CO'
         AND substr( ci.documentno, 1, 2 )='FC'
         GROUP BY

         mr.value,
         mr.name,
         to_char( ci.dateinvoiced, 'MM-YYYY' ),
         ci.C_Invoice_ID,
         ci.em_gai_totalsponsorship,
         stp.nilai,
         substr( ci.documentno, 1, 2 )) x

         GROUP BY bulan,salesrepcode,namasales , fakturs
         ")->result();
    }

    function query_utama($bulan_tahun, $cabang = NULL, $faktur = NULL, $SELECT = NULL)
    {
        if ($faktur != NULL) {
            $faktur_where = " and decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical','Y','5.Youthera') ='$faktur' ";
        } else {
            $faktur_where = "  ";
        }
        $date_xx = "01-" . $bulan_tahun;
        $last_month = date('m-Y', strtotime($date_xx . ' - 1 months'));;
        $where_bulan_tahun = $SELECT ? "and to_char(ci.dateinvoiced,'MM-YYYY') <= '$bulan_tahun' and to_char(ci.dateinvoiced,'MM-YYYY') >= '$last_month'" : " and to_char(ci.dateinvoiced,'MM-YYYY') = '$bulan_tahun'";

        $where_cabang = ($cabang == "" || $cabang == NULL)  ? "" : " AND cabang = '$cabang'";
        $query_utama = "with tbl_temp1 as (select
        bulan,cabang, kacap, area, spv, salesrepcode, namasales, faktur, TIPEDISKON, targetproduct, sum(pencapaian2) as pencapaian, sum(em_gai_totalsponsorship) as diskon
        from
        (


        select to_char(ci.dateinvoiced,'MM-YYYY') as bulan
        , ksm.cabang, ksm.kacap, ksm.area, ksm.spv
        , mr.value as salesrepcode, mr.name as namasales
                , decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical','Y','5.Youthera') as faktur
               ,stp.nilai as targetproduct
               , ci.C_Invoice_ID
                ,

			(
                CASE WHEN (
                            decode(
                                substr( ci.documentno, 1, 1 ),
                                'F',
                                '1.Theraskin',
                                'E',
                                '2.Makloon',
                                'C',
                                '3.Dermarich',
                                'G',
                                '4.Ethical',
                                'Y',
                                '5.Youthera'
                            ) = '5.Youthera'
                            ) THEN
                            (( SELECT sum( QTYINVOICED * PRICELIMIT )  FROM C_INVOICELINE WHERE 				C_INVOICE_ID = ci.C_Invoice_ID ) -  ci.em_gai_totalsponsorship)

                            ELSE
                            (GAI_GET_TOTALLINE ( ci.C_Invoice_ID ))

                END
                )
                AS pencapaian2

                        ,co.em_gai_tipedsc as TIPEDISKON
                        , ci.em_gai_totalsponsorship
                from c_invoice ci
                       left join c_order co on ci.c_order_id=co.c_order_id
                       join ad_user au on au.ad_user_id=ci.salesrep_id
                       join c_bpartner mr on mr.c_bpartner_id=au.c_bpartner_id
                       join gai_salestarget st on st.ad_user_id=au.ad_user_id and exists (select 1 from c_period cp where cp.c_period_id=st.c_period_id and ci.dateinvoiced between cp.startdate and cp.enddate)
                       join m_product_category mpc on upper(mpc.name) like '%'||upper(decode(substr(ci.documentno,1,1),'F','Theraskin','E','Makloon','C','Dermarich','G','Ethical'))
                     join gai_kacapspvmr ksm on ksm.mrcode=mr.value
                       left join gai_stproduct stp on st.gai_salestarget_id=stp.gai_salestarget_id and stp.m_product_category_id=mpc.m_product_category_id
                where ci.issotrx='Y' $where_bulan_tahun and ci.docstatus = 'CO'

                $faktur_where
                group by co.em_gai_tipedsc,
                ksm.cabang, ksm.kacap, ksm.area, ksm.spv,
                mr.value, mr.name, to_char(ci.dateinvoiced,'MM-YYYY')
                ,ci.C_Invoice_ID
                ,ci.em_gai_totalsponsorship
                , stp.nilai
                , decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical','Y','5.Youthera')

        )
        where targetproduct is not null
        $where_cabang
        group by bulan,cabang, kacap, area, spv, salesrepcode, namasales, faktur, targetproduct, TIPEDISKON

        )
        ";

        return $query_utama;
    }

    function getOmsetTargets($monthYear = "", $cabang = "")
    {
        $myOB = $this->load->database("ob", TRUE);
        $monthYear_OB = date('M-y', strtotime('01-' . $monthYear));
        $monthYear_OB_2 = date('m-Y', strtotime('01-' . $monthYear));
        $cabang_param = trim($cabang);
        $where_cabang = $cabang == "" ? " " : " WHERE  trim(cabang) = '$cabang_param'";

        return $myOB->query("select * from ( ------------ Essential s
         select  bulan,trim(cabang) as cabang, kacap, trim(area) as area, spv, trim(salesrepcode) as salesrepcode, namasales, faktur, TIPEDISKON, SUM(target) as target, sum(pencapaian_sale) as pencapaian_sale,
          sum(DiscProduct) as DiscProduct ,sum(DiscPPH) as DiscPPH ,0 as persenpencapaian,sum(diskon) as diskon
         FROM
         (select
                  bulan,cabang, kacap, area, spv, salesrepcode, namasales, faktur, TIPEDISKON, SUM(targetproduct) as target, sum(pencapaian2) as pencapaian_sale, sum(fpamount) as DiscProduct,
                         0 AS persenpencapaian
                  ,
                          gai_getdiscpph('$monthYear_OB_2', salesrepcode) as DiscPPH
                          ,
                        SUM(em_gai_totalsponsorship) as diskon
                  from
                  (


          select
          to_char(ci.dateinvoiced,'MM-YYYY') as bulan
          ,
          ksm.cabang
          , ksm.kacap
          , ksm.area
          , ksm.spv
          , mr.value as salesrepcode
          , mr.name as namasales
          , TO_CHAR (decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical','Y','5.Youthera')) as faktur
         ,0 as targetproduct
          , ci.C_Invoice_ID
          ,
          (
          CASE WHEN (
                      decode(
                          substr( ci.documentno, 1, 1 ),
                          'F',
                          '1.Theraskin',
                          'E',
                          '2.Makloon',
                          'C',
                          '3.Dermarich',
                          'G',
                          '4.Ethical',
                          'Y',
                          '5.Youthera'
                      ) = '5.Youthera'
                      ) THEN
                      (( SELECT sum( QTYINVOICED * PRICELIMIT )  FROM C_INVOICELINE WHERE 				C_INVOICE_ID = ci.C_Invoice_ID ) )
                      ELSE
                      (GAI_GET_TOTALLINE ( ci.C_Invoice_ID ))

          END
          )
          AS  pencapaian2
          , co.em_gai_tipedsc as TIPEDISKON
          , co.em_gai_totalsponsorship AS em_gai_totalsponsorship
          , SUM(fp.amount) as fpamount
             from c_invoice ci
             left join c_order co on ci.c_order_id=co.c_order_id
             join ad_user au on au.ad_user_id=ci.salesrep_id
             join c_bpartner mr on mr.c_bpartner_id=au.c_bpartner_id
             join m_product_category mpc on upper(mpc.name) like '%'||upper(decode(substr(ci.documentno,1,1),'F','Theraskin','E','Makloon','C','Dermarich','G','Ethical','Y','Youthera'))
             join gai_kacapspvmr ksm on ksm.mrcode=mr.value
             left join c_bpartner cus on cus.salesrep_id=ksm.salesrep_id AND cus.iscustomer= 'Y'
             left join fin_payment fp on fp.c_bpartner_id=cus.salesrep_id
             where ci.issotrx='Y' and to_char(ci.dateinvoiced,'MM-YYYY') = '$monthYear' and ci.docstatus = 'CO'
          --   AND mr.value='G04'
             group by
             co.em_gai_tipedsc
             ,ksm.cabang
             ,ksm.kacap
             ,ksm.area
             ,ksm.spv
             ,mr.value
             ,mr.name
             ,to_char(ci.dateinvoiced,'MM-YYYY')
             ,ci.C_Invoice_ID
             ,ci.DOCUMENTNO
             , decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical','Y','5.Youthera')
             ,co.em_gai_totalsponsorship
             UNION ALL

             select
         to_char(TO_DATE(CONCAT('01-', cp.NAME)),'MM-YYYY') as bulan
         ,
         ksm.cabang
         ,ksm.kacap
         ,ksm.area
         ,ksm.spv
         ,mr.value as salesrepcode
         ,mr.name as namasales
         ,TO_CHAR(
             CASE
                     WHEN mpc.name LIKE '%Theraskin%' THEN '1.Theraskin'
                     WHEN mpc.name LIKE '%Makloon%' THEN '2.Makloon'
                     WHEN mpc.name LIKE '%Dermarich%' THEN '3.Dermarich'
                     WHEN mpc.name LIKE '%FG Produk Jadi Obat%' THEN '4.Ethical'
                     WHEN mpc.name LIKE '%Youthera%' THEN '5.Youthera'
             END
         ) as faktur
         ,stp.nilai as targetproduct
         ,'target' as C_Invoice_ID
         ,0 as pencapaian2
         ,'target' as TIPEDISKON
         , 0 as em_gai_totalsponsorship
         ,0 as fpamount
         from ad_user au
         join c_bpartner mr on mr.c_bpartner_id=au.c_bpartner_id
         join gai_salestarget st on st.ad_user_id=au.ad_user_id
         JOIN   gai_stproduct stp on st.gai_salestarget_id=stp.gai_salestarget_id
         JOIN c_period cp on cp.c_period_id=st.c_period_id
         join m_product_category mpc ON stp.m_product_category_id=mpc.m_product_category_id
         join gai_kacapspvmr ksm on ksm.mrcode=mr.value
         where
          cp.NAME='$monthYear_OB'
         group by
         ksm.cabang,
         ksm.kacap,
         ksm.area,
         ksm.spv,
         mr.value,
         mr.name,
         mpc.name,
         to_char(TO_DATE(CONCAT('01-', cp.NAME)),'MM-YYYY') ,
         stp.nilai
         )
         group by bulan,cabang, kacap, area, spv, salesrepcode, namasales, faktur, TIPEDISKON
         ) x group by bulan,cabang, kacap, area, spv, salesrepcode, namasales, faktur, TIPEDISKON

          ) xxx
   $where_cabang
         ")->result();
    }
    function ___getOmsetTargets($monthYear = "")
    {
        # load ob database
        $myOB = $this->load->database("ob", TRUE);

        return $myOB->query("select bulan, cabang, kacap, area, spv, salesrepcode, namasales
        ,faktur
        ,max(targetproduct) as target, sum(nvl(pencapaian,0)) as pencapaian
        ,round((sum(nvl(pencapaian,0))/case when max(targetproduct)=0 then 1 else max(targetproduct) end)*100,2) as persenpencapaian
        ,gai_getdiscpph('01-2022', salesrepcode) as DiscPPH
        ,gai_getdiscproduct('01-2022', salesrepcode) as DiscProduct,
        TIPEDISKON
        from
        (


 select to_char(ci.dateinvoiced,'MM-YYYY') as bulan, ksm.cabang, ksm.kacap, ksm.area, ksm.spv, mr.value as salesrepcode, mr.name as namasales
        , case when mpc.name='Diskon' then '5.Diskon' else decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical') end as faktur
        ,stp.nilai as targetproduct,abs(sum(cil.linenetamt))as pencapaian
        ,round((sum(cil.linenetamt)/case when stp.nilai=0 then 1 else stp.nilai end)*100,2) as PersenPencapaian
				,GDP.TIPEDSC as TIPEDISKON
        from c_invoice ci
              left join c_bpartner bp on ci.c_bpartner_id=bp.c_bpartner_id
               left join c_bpartner mr on bp.salesrep_id=mr.c_bpartner_id
               left join ad_user au on au.c_bpartner_id=mr.c_bpartner_id
               left join gai_salestarget st on st.ad_user_id=au.ad_user_id and exists (select 1 from c_period cp where cp.c_period_id=st.c_period_id and ci.dateinvoiced between cp.startdate and cp.enddate)
               left join c_order co on ci.c_order_id=co.c_order_id
               left join c_invoicetax cit on cit.c_invoice_id=ci.c_invoice_id
               left join c_invoiceline cil on ci.c_invoice_id=cil.c_invoice_id
               left join m_product mp on mp.m_product_id=cil.m_product_id
               left join m_product_category mpc on mpc.m_product_category_id=mp.m_product_category_id
               left join gai_kacapspvmr ksm on ksm.mrcode=mr.value
               left join gai_stproduct stp on st.gai_salestarget_id=stp.gai_salestarget_id and stp.m_product_category_id=mpc.m_product_category_id
							 LEFT JOIN GAI_DiscMax_Partner GDP ON CI.C_BPARTNER_ID = GDP.C_BPARTNER_ID
        where ci.issotrx='Y' and to_char(ci.dateinvoiced,'MM-YYYY') = '$monthYear' and ci.docstatus = 'CO'
        group by GDP.TIPEDSC,ksm.cabang, ksm.kacap, ksm.area, ksm.spv, mr.value, mr.name, to_char(ci.dateinvoiced,'MM-YYYY'), stp.nilai
        , case when mpc.name='Diskon' then '5.Diskon' else decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','C','3.Dermarich','G','4.Ethical') end
        having sum(cil.linenetamt)!= 0
				--order by mr.value
        /*
        order by to_char(ci.dateinvoiced,'MM-YYYY'), ksm.cabang, ksm.area, mr.value
        ,case when mpc.name='Diskon' then '5.Diskon' else decode(substr(ci.documentno,1,1),'F','1.Theraskin','E','2.Makloon','R','3.Dermarich','G','4.Ethical') end
        */



				)
        group by bulan, cabang, kacap, area, spv, salesrepcode, namasales,faktur,TIPEDISKON
        order by 1, 2, 4, 6, 8")->result();
    }

    function get_cabang()
    {
        # load ob database
        $myOB = $this->load->database("ob", TRUE);


        $monthYear = date('m-Y');
        $SELECT = TRUE;
        $cabang =  $myOB->query($this->query_utama($monthYear, NULL, NULL, $SELECT) . " select distinct cabang,kacap from tbl_temp1 order by 1 asc")->result();
        return $cabang;
    }
}
