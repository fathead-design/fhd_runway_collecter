<?php

class Collecter_Config
{
    private $db;
    private $table;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = PERCH_DB_PREFIX . 'fhd_collecter_profiles';
    }

    public function all()
    {
        return $this->db->get_rows('SELECT * FROM ' . $this->table . ' ORDER BY profileLabel ASC') ?: [];
    }

    public function find($id)
    {
        return $this->db->get_row('SELECT * FROM ' . $this->table . ' WHERE profileID=' . $this->db->pdb((int)$id));
    }

    public function save(array $input, $id = 0)
    {
        $data = [
            'profileLabel'     => trim((string)($input['profileLabel'] ?? '')),
            'collectionID'     => (int)($input['collectionID'] ?? 0),
            'primarySetSlug'   => $this->slug($input['primarySetSlug'] ?? ''),
            'secondarySetSlug' => $this->slug($input['secondarySetSlug'] ?? ''),
            'titleField'       => $this->field($input['titleField'] ?? ''),
            'orderField'       => $this->field($input['orderField'] ?? 'item_order'),
            'profileUpdated'   => date('Y-m-d H:i:s'),
        ];
        if ($data['profileLabel'] === '' || !$data['collectionID'] || $data['primarySetSlug'] === '' || $data['orderField'] === '') {
            throw new InvalidArgumentException('Label, Collection, primary category set, and ordering field are required.');
        }
        if ($data['secondarySetSlug'] !== '' && $data['secondarySetSlug'] === $data['primarySetSlug']) {
            throw new InvalidArgumentException('Primary and secondary category sets must be different.');
        }
        if ($id) {
            $this->db->update($this->table, $data, 'profileID', (int)$id);
            return (int)$id;
        }
        $data['profileCreated'] = $data['profileUpdated'];
        return (int)$this->db->insert($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, 'profileID', (int)$id);
    }

    private function slug($value)
    {
        $value = trim((string)$value);
        return preg_match('/^[a-z0-9][a-z0-9_-]*$/', $value) ? $value : '';
    }

    private function field($value)
    {
        $value = trim((string)$value);
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) ? $value : '';
    }
}
