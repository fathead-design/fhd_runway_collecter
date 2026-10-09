<?php
include '../../../core/inc/api.php';

$API = new PerchAPI(1.0, 'fhd_runway_collecter');
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Lang = $API->get('Lang');
$Perch->add_css($API->app_path().'/assets/css/collecter.css');
$Perch->page_title = 'FHD Collection Organizer';

include_once PERCH_CORE.'/runway/apps/content/PerchContent_Collections.class.php';
include_once PERCH_CORE.'/runway/apps/content/PerchContent_Collection.class.php';
require_once __DIR__.'/lib/Collecter_Config.class.php';
require_once __DIR__.'/lib/Collecter_Items.class.php';
require_once __DIR__.'/lib/Collecter_Order.class.php';

if (!$CurrentUser->has_priv('fhd_runway_collecter')) PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/');

$db = PerchDB::fetch();
$Config = new Collecter_Config($db);
$Items = new Collecter_Items($db);
$Profiles = $Config->all();
$VisibleProfiles = [];
$Collections = new PerchContent_Collections();
foreach ($Profiles as $candidate) {
    $candidateCollection = $Collections->find((int)$candidate['collectionID']);
    if ($candidateCollection && $candidateCollection->role_may_edit($CurrentUser)) $VisibleProfiles[] = $candidate;
}
$profileID = (int)PerchRequest::get('profile');
$profile = $Config->find($profileID);
$Collection = $profile ? $Collections->find((int)$profile['collectionID']) : false;
if ($profile && (!$Collection || !$Collection->role_may_edit($CurrentUser))) $profile = false;

$primary = '';
$secondary = '';
$primaryCategories = $profile ? $Items->categories($profile['primarySetSlug']) : [];
if ($profile && $Items->pathIsIn($primaryCategories, PerchRequest::get('primary'))) $primary = PerchRequest::get('primary');
$secondaryCategories = ($profile && $primary !== '' && $profile['secondarySetSlug'] !== '') ? $Items->usableSecondaryCategories($profile, $primary) : [];
if ($profile && $profile['secondarySetSlug'] !== '' && $Items->pathIsIn($secondaryCategories, PerchRequest::get('secondary'))) $secondary = PerchRequest::get('secondary');
$ready = $profile && $primary !== '' && ($profile['secondarySetSlug'] === '' || $secondary !== '');
$items = $ready ? $Items->matching($profile, $primary, $secondary) : [];

if ($ready && $Form->posted() && $Form->validate()) {
    try {
        $Order = new Collecter_Order($db);
        $Order->save($profile, $items, $Form->find_items('item_'));
        $Collection->update(['collectionUpdated' => date('Y-m-d H:i:s')]);
        $Alert->set('success', 'The filtered item order has been saved.');
        $items = $Items->matching($profile, $primary, $secondary);
    } catch (InvalidArgumentException $e) {
        $Alert->set('error', $e->getMessage());
    }
}

include PERCH_CORE.'/inc/top.php';
?>
<div class="inner fhd-collecter">
  <?php echo $HTML->title_panel(['heading' => 'FHD Collection Organizer', 'button' => $CurrentUser->has_priv('fhd_runway_collecter.configure') ? ['text' => 'Configure organizers', 'link' => $API->app_path().'/settings.php'] : false]); ?>
  <?php if (!$VisibleProfiles): ?>
    <p class="fhd-collecter__status">No organizer profiles have been configured. An administrator can add one from the configuration screen.</p>
  <?php else: ?>
    <form method="get" class="form-simple fhd-collecter__filters">
      <div class="field-wrap"><label for="profile">Organizer</label><div class="form-entry"><select id="profile" name="profile" onchange="this.form.submit()"><option value="">Choose an organizer</option><?php foreach ($VisibleProfiles as $candidate): ?><option value="<?php echo (int)$candidate['profileID']; ?>"<?php echo $profile && (int)$candidate['profileID']===(int)$profile['profileID'] ? ' selected' : ''; ?>><?php echo PerchUtil::html($candidate['profileLabel']); ?></option><?php endforeach; ?></select></div></div>
      <?php if ($profile): ?>
        <div class="field-wrap"><label for="primary">Primary category</label><div class="form-entry"><select id="primary" name="primary" onchange="this.form.submit()"><option value="">Choose a category</option><?php foreach ($primaryCategories as $category): ?><option value="<?php echo PerchUtil::html($category['catPath'], true); ?>"<?php echo $category['catPath']===$primary ? ' selected' : ''; ?>><?php echo PerchUtil::html($category['catTitle']); ?></option><?php endforeach; ?></select></div></div>
        <?php if ($profile['secondarySetSlug'] !== '' && $primary !== ''): ?><div class="field-wrap"><label for="secondary">Secondary category</label><div class="form-entry"><select id="secondary" name="secondary" onchange="this.form.submit()"><option value="">Choose a category</option><?php foreach ($secondaryCategories as $category): ?><option value="<?php echo PerchUtil::html($category['catPath'], true); ?>"<?php echo $category['catPath']===$secondary ? ' selected' : ''; ?>><?php echo PerchUtil::html($category['catTitle']); ?></option><?php endforeach; ?></select></div></div><?php endif; ?>
      <?php endif; ?>
    </form>
    <?php if ($profile && !$Collection): ?><p class="fhd-collecter__status">This profile’s Collection no longer exists.</p><?php endif; ?>
    <?php if ($ready): ?><form method="post" class="reorder form-simple fhd-collecter__organizer"><p class="fhd-collecter__status">Dragging updates the public <code><?php echo PerchUtil::html($profile['orderField']); ?></code> field only. It does not change Perch’s global Collection order. Click an item name to edit it in a new tab.</p><?php if ($items): ?><ol class="basic-sortable sortable-tree fhd-collecter__list" data-start="1"><?php foreach ($items as $position => $item): $title = $profile['titleField'] !== '' ? ($item['fields'][$profile['titleField']] ?? '') : ($item['fields']['_title'] ?? ''); if ($title === '') $title = 'Untitled item'; $editUrl = PERCH_LOGINPATH.'/core/apps/content/collections/edit/?id='.(int)$profile['collectionID'].'&amp;itm='.(int)$item['itemID']; ?><li class="fhd-collecter__item"><div><a class="fhd-collecter__item-title fhd-collecter__item-link" href="<?php echo $editUrl; ?>" target="_blank" rel="noopener" onmousedown="event.stopPropagation();" onclick="event.stopPropagation();" title="Edit this Collection item in a new tab"><?php echo PerchUtil::html($title); ?></a><input type="text" class="text s" name="item_<?php echo (int)$item['itemID']; ?>" value="<?php echo 1000+$position; ?>"></div></li><?php endforeach; ?></ol><button type="submit" class="button action">Save filtered order</button><?php else: ?><p class="fhd-collecter__status">No items match this category selection.</p><?php endif; ?></form><?php endif; ?>
  <?php endif; ?>
</div>
<?php include PERCH_CORE.'/inc/btm.php'; ?>
