<?php

class Collecter_Order
{
    private $db;
    public function __construct($db) { $this->db = $db; }

    /** Updates only verified, current-revision items in the supplied filtered set. */
    public function save(array $profile, array $items, array $submitted)
    {
        $allowed = [];
        foreach ($items as $item) $allowed[(string)$item['itemID']] = $item;
        $positions = [];
        foreach ($submitted as $id => $position) {
            if (!isset($allowed[(string)$id]) || !is_numeric($position)) throw new InvalidArgumentException('The submitted order contains an item outside this filtered group.');
            $positions[(string)$id] = (int)$position;
        }
        if (count($positions) !== count($allowed)) throw new InvalidArgumentException('Every item in this filtered group must be submitted. Refresh the page and try again.');
        asort($positions, SORT_NUMERIC);
        $orderField = $profile['orderField'];
        $position = 1;
        foreach (array_keys($positions) as $id) {
            $item = $allowed[$id];
            $fields = $item['fields'];
            $fields[$orderField] = $position;
            $this->db->update(PERCH_DB_PREFIX.'collection_items', ['itemJSON' => PerchUtil::json_safe_encode($fields)], 'itemRowID', (int)$item['itemRowID']);
            $this->db->execute('DELETE FROM '.PERCH_DB_PREFIX.'collection_index WHERE itemID='.$this->db->pdb((int)$id).' AND collectionID='.$this->db->pdb((int)$profile['collectionID']).' AND itemRev='.$this->db->pdb((int)$item['itemRev']).' AND indexKey='.$this->db->pdb($orderField));
            $this->db->insert(PERCH_DB_PREFIX.'collection_index', ['itemID'=>(int)$id, 'collectionID'=>(int)$profile['collectionID'], 'itemRev'=>(int)$item['itemRev'], 'indexKey'=>$orderField, 'indexValue'=>(string)$position]);
            $position++;
        }
    }
}
