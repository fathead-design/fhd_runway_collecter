<?php

class Collecter_Items
{
    private $db;

    public function __construct($db) { $this->db = $db; }

    public function categories($setSlug)
    {
        return $this->db->get_rows('SELECT c.catTitle, c.catPath FROM '.PERCH_DB_PREFIX.'categories c JOIN '.PERCH_DB_PREFIX.'category_sets s ON s.setID=c.setID WHERE s.setSlug='.$this->db->pdb($setSlug).' ORDER BY c.catTreePosition ASC') ?: [];
    }

    public function usableSecondaryCategories(array $profile, $primaryPath)
    {
        return $this->db->get_rows('SELECT DISTINCT c.catTitle, c.catPath FROM '.PERCH_DB_PREFIX.'categories c JOIN '.PERCH_DB_PREFIX.'category_sets s ON s.setID=c.setID JOIN '.PERCH_DB_PREFIX.'collection_index secondary_index ON secondary_index.indexKey='.$this->db->pdb('_category').' AND secondary_index.indexValue=c.catPath JOIN '.PERCH_DB_PREFIX.'collection_index primary_index ON primary_index.itemID=secondary_index.itemID AND primary_index.collectionID=secondary_index.collectionID AND primary_index.itemRev=secondary_index.itemRev AND primary_index.indexKey='.$this->db->pdb('_category').' AND primary_index.indexValue='.$this->db->pdb($primaryPath).' WHERE s.setSlug='.$this->db->pdb($profile['secondarySetSlug']).' AND secondary_index.collectionID='.(int)$profile['collectionID'].' ORDER BY c.catTreePosition ASC') ?: [];
    }

    public function matching(array $profile, $primaryPath, $secondaryPath = '')
    {
        $sql = 'SELECT DISTINCT ci.itemRowID, ci.itemID, ci.itemRev, ci.itemJSON FROM '.PERCH_DB_PREFIX.'collection_items ci JOIN '.PERCH_DB_PREFIX.'collection_revisions r ON r.itemID=ci.itemID AND r.itemLatestRev=ci.itemRev JOIN '.PERCH_DB_PREFIX.'collection_index primary_index ON primary_index.itemID=ci.itemID AND primary_index.collectionID=ci.collectionID AND primary_index.itemRev=ci.itemRev AND primary_index.indexKey='.$this->db->pdb('_category').' AND primary_index.indexValue='.$this->db->pdb($primaryPath);
        if ($secondaryPath !== '') {
            $sql .= ' JOIN '.PERCH_DB_PREFIX.'collection_index secondary_index ON secondary_index.itemID=ci.itemID AND secondary_index.collectionID=ci.collectionID AND secondary_index.itemRev=ci.itemRev AND secondary_index.indexKey='.$this->db->pdb('_category').' AND secondary_index.indexValue='.$this->db->pdb($secondaryPath);
        }
        $rows = $this->db->get_rows($sql.' WHERE ci.collectionID='.(int)$profile['collectionID']) ?: [];
        foreach ($rows as &$row) $row['fields'] = PerchUtil::json_safe_decode($row['itemJSON'], true) ?: [];
        unset($row);
        $field = $profile['orderField'];
        usort($rows, function ($a, $b) use ($field) {
            $aOrder = isset($a['fields'][$field]) && is_numeric($a['fields'][$field]) ? (float)$a['fields'][$field] : PHP_FLOAT_MAX;
            $bOrder = isset($b['fields'][$field]) && is_numeric($b['fields'][$field]) ? (float)$b['fields'][$field] : PHP_FLOAT_MAX;
            return $aOrder === $bOrder ? ((int)$a['itemID'] <=> (int)$b['itemID']) : ($aOrder <=> $bOrder);
        });
        return $rows;
    }

    public function pathIsIn(array $categories, $path)
    {
        foreach ($categories as $category) if ($category['catPath'] === $path) return true;
        return false;
    }
}
