<?php
include '../../../core/inc/api.php';
$API = new PerchAPI(1.0, 'fhd_runway_collecter');
$HTML = $API->get('HTML'); $Form = $API->get('Form');
$Perch->add_css($API->app_path().'/assets/css/collecter.css');
$Perch->page_title = 'Collection Organizer settings';
require_once __DIR__.'/lib/Collecter_Config.class.php';
include_once PERCH_CORE.'/runway/apps/content/PerchContent_Collections.class.php';
if (!$CurrentUser->has_priv('fhd_runway_collecter.configure')) PerchUtil::redirect($API->app_path());
$Config = new Collecter_Config(PerchDB::fetch());
$profileID = (int)PerchRequest::get('id'); $profile = $profileID ? $Config->find($profileID) : null;
if ($Form->posted() && $Form->validate() && PerchRequest::post('action') === 'delete') {
    $Config->delete((int)PerchRequest::post('profileID'));
    PerchUtil::redirect($API->app_path().'/settings.php');
}
if ($Form->posted() && $Form->validate() && PerchRequest::post('action') !== 'delete') {
    try { $profileID = $Config->save($Form->receive(['profileLabel','collectionID','primarySetSlug','secondarySetSlug','titleField','orderField']), $profileID); $Alert->set('success', 'Organizer profile saved.'); $profile = $Config->find($profileID); }
    catch (InvalidArgumentException $e) { $Alert->set('error', $e->getMessage()); }
}
$Collections = (new PerchContent_Collections())->all();
include PERCH_CORE.'/inc/top.php';
?>
<div class="inner fhd-collecter">
<?php echo $HTML->title_panel(['heading'=>'Collection Organizer settings', 'button'=>['text'=>'Back to organizer', 'link'=>$API->app_path()]]); ?>
<form method="post" class="form-simple fhd-collecter__settings">
<div class="field-wrap"><label for="profileLabel">Profile label</label><div class="form-entry"><input id="profileLabel" name="profileLabel" class="text" required value="<?php echo PerchUtil::html($profile['profileLabel'] ?? ''); ?>"></div></div>
<div class="field-wrap"><label for="collectionID">Collection</label><div class="form-entry"><select id="collectionID" name="collectionID" required><option value="">Choose a Collection</option><?php foreach ($Collections as $Collection): ?><option value="<?php echo (int)$Collection->id(); ?>"<?php echo isset($profile['collectionID']) && (int)$profile['collectionID']===(int)$Collection->id() ? ' selected' : ''; ?>><?php echo PerchUtil::html($Collection->collectionKey()); ?></option><?php endforeach; ?></select></div></div>
<div class="field-wrap"><label for="primarySetSlug">Primary category-set slug</label><div class="form-entry"><input id="primarySetSlug" name="primarySetSlug" class="text" required value="<?php echo PerchUtil::html($profile['primarySetSlug'] ?? ''); ?>"></div></div>
<div class="field-wrap"><label for="secondarySetSlug">Secondary category-set slug <span class="hint">optional</span></label><div class="form-entry"><input id="secondarySetSlug" name="secondarySetSlug" class="text" value="<?php echo PerchUtil::html($profile['secondarySetSlug'] ?? ''); ?>"></div></div>
<div class="field-wrap"><label for="titleField">Item title field <span class="hint">optional; falls back to _title</span></label><div class="form-entry"><input id="titleField" name="titleField" class="text" value="<?php echo PerchUtil::html($profile['titleField'] ?? ''); ?>"></div></div>
<div class="field-wrap"><label for="orderField">Public ordering field</label><div class="form-entry"><input id="orderField" name="orderField" class="text" required value="<?php echo PerchUtil::html($profile['orderField'] ?? 'item_order'); ?>"><p class="hint">This field is written to item JSON and Collection index records. Your public template must sort by it.</p></div></div>
<div class="submit-bar"><button type="submit" class="button action">Save profile</button></div></form>
<?php $profiles=$Config->all(); if ($profiles): ?><h2>Configured organizers</h2><ul class="fhd-collecter__profiles"><?php foreach ($profiles as $row): ?><li><a href="?id=<?php echo (int)$row['profileID']; ?>"><?php echo PerchUtil::html($row['profileLabel']); ?></a> <small><?php echo PerchUtil::html($row['orderField']); ?></small><form method="post" class="fhd-collecter__delete"><input type="hidden" name="action" value="delete"><input type="hidden" name="profileID" value="<?php echo (int)$row['profileID']; ?>"><button type="submit" class="button button-simple">Delete</button></form></li><?php endforeach; ?></ul><?php endif; ?>
</div><?php include PERCH_CORE.'/inc/btm.php'; ?>
