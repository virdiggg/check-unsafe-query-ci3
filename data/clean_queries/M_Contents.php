<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_Contents extends CI_model
{
    /**
     * Default table name.
     * 
     * @param string $table
     */
    private $table = 'contents';

    /**
     * Default table name.
     * 
     * @param string $table
     */
    private $details = 'content_details';

    /**
     * DB Connection name.
     * 
     * @param string $conn
     */
    private $conn = 'default';

    /**
     * DB Connection.
     * 
     * @param object $db
     */
    private $db;

    /**
     * Path upload
     * 
     * @param string $path
     */
    public $path = 'bd'.DIRECTORY_SEPARATOR.'contents';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database($this->conn, TRUE);
    }

    /**
     * Insert data to database.
     * 
     * @param array $param
     * 
     * @return int
     */
    public function create($param)
    {
        $this->db->insert($this->table, $param);
        return $this->db->insert_id();
    }

    /**
     * Insert data to database.
     * 
     * @param array $param
     * 
     * @return bool
     */
    public function insertDetails($param)
    {
        $this->db->insert_batch($this->details, $param);
        return true;
    }

    /**
     * Datatables.
     * 
     * @param string|int  $length
     * @param string|int  $start
     * @param string|null $search
     * 
     * @return array
     */
    public function datatables($length = 10, $start = 0, $search = NULL)
    {
        $result = $this->queryDatatables($length, $start, $search);
        $countResult = count($result);

        if ($countResult >= $length) {
            $resultNextPage = $this->queryDatatables($length, $start + $length, $search);
            $countResultNextPage = count($resultNextPage);
            if ($countResultNextPage >= $length) {
                $totalRecords = $start + (2 * $length);
            } else {
                $totalRecords = $start + $length + $countResultNextPage;
            }
        } else {
            $totalRecords = $start + $countResult;
        }

        return [
            'totalRecords' => $totalRecords,
            'data' => $result ? $this->parse($result, $start) : [],
        ];
    }

    /**
     * Datatables.
     * 
     * @param string|int  $length
     * @param string|int  $start
     * @param string|null $search
     * 
     * @return array
     */
    public function queryDatatables($length = 10, $start = 0, $search = NULL)
    {
        $this->db->select("c.id, to_char(c.content_date, 'YYYY-MM-DD') AS content_date, c.contents, c.image,
            cast(cdsum.total_cust AS VARCHAR) AS total_cust, to_char(c.create_date, 'YYYY-MM-DD') AS terkirim_pada");
        $this->db->from($this->table . ' c');
        $this->db->join("(SELECT cd.content_id, count(cd.id) AS total_cust FROM " . $this->details . " AS cd GROUP BY cd.content_id) AS cdsum", "cdsum.content_id = c.id", "LEFT", FALSE);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like("to_char(c.content_date, 'YYYY-MM-DD')", $search);
            $this->db->or_like('c.contents', $search);
            $this->db->or_like('cast(cdsum.total_cust AS VARCHAR)', $search);
            $this->db->or_like("to_char(c.create_date, 'YYYY-MM-DD')", $search);
            $this->db->group_start();
        }
        $this->db->order_by('1', 'DESC');
        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }


    /**
     * Parse Datatables.
     *
     * @param array      $result
     * @param string|int $start
     *
     * @return array
     */
    public function parse($result, $start = 0)
    {
        $this->load->library('upload_custom');
        $return = [];

        foreach ($result as $r) {
            $start++;
            $no = $start;

            $action = '<a href="#" id="detailCust" class="btn btn-warning btn-minier" data-id="' . $r->id . '"><i class="fa fa-eye"></i></a>';
            $return[] = [
                'no' => $no,
                'content_date' => '<small class="text-uppercase">' . $r->content_date . '</small>',
                'contents' => '<small class="text-uppercase">' . $r->contents . '</small>',
                'total_cust' => '<small class="text-uppercase">' . $r->total_cust . '</small>',
                'terkirim_pada' => '<small class="text-uppercase">' . $r->terkirim_pada . '</small>',
                'image' => empty($r->image) ? '<small class="text-uppercase">NO-IMAGE</small>' : '<img src="' . $this->upload_custom->encryptFile($this->path . DIRECTORY_SEPARATOR . $r->image) . '" alt="' . $r->image . '" class="img img-fluid" style="max-width:50px">',
                'action' => $action,
            ];
        }

        return $return;
    }
}
