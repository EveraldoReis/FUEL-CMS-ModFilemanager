<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once FUEL_PATH . 'models/base_module_model.php';

class Roxyfileman_model extends Base_module_model {

	public $record_class = 'File';
	public $required = array();

	public $boolean_fields = array('published');

	public $belongs_to = array();

	public $has_many = array();

	function __construct() {

		$CI = &get_instance();
		$this->config->module_load(FILEMAN_FOLDER, FILEMAN_FOLDER);
		$this->_tables = $CI->config->item('tables');
		parent::__construct('fileman_roxyfileman_files');

	}

	function list_items($limit = NULL, $offset = NULL, $col = 'id', $order = 'asc') {
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function get_item($id) {

		$query = $this->db->where('id', $id)->get('fileman_roxyfileman_files');

		return $query->first_row();

	}

	function form_fields($values = array()) {
		$fields = parent::form_fields($values);
		//unset($fields['created_at']);
		return $fields;
	}

	function options_list() {

		$items = $this->list_items();
		$options = array();
		foreach ($items as $item) {
			$options[$item['id']] = substr($item['local'], 0, 30);
		}

		return $options;
	}

	function options_list_ajax($id, $key = 'id') {

		return $this->find_all(array($key => $id));
	}

}

class File_model extends Base_module_record {
	private $_tables;

	function on_init() {
		$this->_tables = $this->_CI->config->item('tables');
	}

}
