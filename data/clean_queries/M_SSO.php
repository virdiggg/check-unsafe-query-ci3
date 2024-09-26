<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_SSO extends CI_Model
{
	// private $sso = 'sso';
	// private $db;
	public function __construct()
	{
		parent::__construct();
		// $this->db = $this->load->database($this->sso, TRUE);
	}

	/**
	 * Ambil detail user sso sesuai email
	 * 
	 * @param string $email
	 * 
	 * @return object
	 */
	public function getUser($email) {
		return $this->findBy($email, 'email');
	}

	/**
	 * Ambil detail user sso sesuai email
	 * 
	 * @param string $val
	 * @param string $var
	 * 
	 * @return object
	 */
	public function findBy($val, $var = 'email') {
		return $this->db->get_where('sso_users', [$var => $val, 'is_block' => 'N'])->row();
	}

	/**
	 * Ambil role id dari session dan role id yang udah di-assign ke apps
	 * 
	 * @param int|string $nik
	 * @param int|string $role
	 * 
	 * @return array|null
	 */
	public function getRoleId($nik) {
		$this->load->helper('arr');
		$user = $this->findBy($nik, 'nik');
		if ($user) {
			$roles = $this->db->select('r.role_id')
				->from('sso_user_roles AS r')
				->join('sso_users AS u', 'u.id=r.user_id', 'left')
				->where('u.nik', $nik)
				->get()->result_array();

			$rolesId = pluck($roles, 'role_id');
			$rolesId[] = $user->role_id;

			return arr_unique($rolesId);
		} else {
			return null;
		}
	}

	/**
	 * Ambil apps yang bisa ditampilin buat user yang login
	 * 
	 * @param array $roles
	 * 
	 * @return array
	 */
	public function getMyApps($roles) {
		$this->db->select('app_id AS id, app_name AS name, app_link AS url, app_icon AS icon, is_main, app_order');
		$this->db->distinct();
		$this->db->from('v_apps_sso');
		$this->db->where('app_id !=', 1);
		// $this->db->where_in('role_id', $roles);

		if (!empty($search)) {
			$this->db->like('LOWER(app_name)', $search);
		}

		$this->db->order_by('is_main', 'desc');
		$this->db->order_by('app_order', 'asc');
		$result = $this->db->get()->result();

		return $result ? $result : [];
	}

	/**
	 * Ambil apps sesuai id
	 * 
	 * @param array $id
	 * 
	 * @return object
	 */
	public function findApp($id) {
		$apps = $this->db->select('id, app_name AS name, app_link AS url, is_link_pure AS is_static')
			->from('sso_applications')
			->where('id', $id)
			->where('app_status', 'Y');

		return $apps->get()->row();
	}

    public function get_user_sso($nik)
    {
        $this->db->where('nik', $nik);
        $this->db->where('is_block', 'N');
        $query = $this->db->get('sso_users')->row();
        if ($query) {
            return $query;
        }

		$this->db->select('*, NIK as nik, username as email');
		$this->db->where('NIK', $nik);
		$this->db->group_start();
			$this->db->where('tipe_customer IS NULL', NULL);
			$this->db->or_where('tipe_customer', 'Employee');
		$this->db->group_end();
		return $this->db->get('tbl_login')->row();
    }

	/**
	 * Cek pake akun email dokumen apa bukan
	 * 
	 * @param string|int $id
	 * 
	 * @return true
	 */
	public function isEmailDoc($id) {
		$result = $this->db->select('email_dokumen')
			->where('email_dokumen IS NOT NULL', NULL)
			->like('username', '_doc')
			->where('id_login', $id)
			->get('tbl_login')->row();

		return !empty($result) ? false : true;
	}

	/**
	 * Ambil detail akun (login)
	 * 
	 * @param string $email
	 * @param string $password
	 * 
	 * @return object
	 */
	public function get_user_by_email_password($email, $password)
	{
		$user = $this->db->where('email', $email)
			->where('password', $password)->get('sso_users')->row();

		if ($user) {
			return $user;
		} else {
			$user = $this->db->select('*, NIK as nik')->where('username', $email)
			->where('password', $password)->get('tbl_login')->row();
			return $user;
		}
	}

	/**
	 * Ambil role id sesuai group
	 * 
	 * @param string $nik
	 * 
	 * @return string|null
	 */
	public function getRoleIdByGroup($nik)
	{
		$this->db->select('role_sso');
		$this->db->join('tbl_group tb2', 'tb2.id_group=tb1.id_group');
		$this->db->where('NIK', $nik);
		$query = $this->db->get('tbl_login tb1')->row();
		return $query ? json_decode($query->role_sso) : null;
	}
}