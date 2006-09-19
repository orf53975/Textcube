<?php
if (!defined('ROOT'))
	define('ROOT', '../../../../..');
if (isset($_POST['page']))
	$_GET['page'] = $_POST['page'];

if (isset($_GET['category'])) $_POST['category'] = $_GET['category'];
if (isset($_GET['name'])) $_POST['name'] = $_GET['name'];
if (isset($_GET['ip'])) $_POST['ip'] = $_GET['ip'];
if (isset($_GET['withSearch'])) $_POST['withSearch'] = $_GET['withSearch'];
if (isset($_GET['search'])) $_POST['search'] = $_GET['search'];
if (isset($_GET['trashType'])) $_POST['trashType'] = $_GET['trashType'];

$IV = array(
	'GET' => array(
		'page' => array('int', 1, 'default' => 1)
	),
	'POST' => array(
		'category' => array('int', 'default' => 0),
		'site' => array('string', 'default' => ''),
		'ip' => array('ip', 'default' => ''),
		'withSearch' => array(array('on'), 'mandatory' => false),
		'search' => array('string', 'default' => ''),
		'perPage' => array('int', 10, 30, 'mandatory' => false)
	)
);

require ROOT . '/lib/includeForOwner.php';
$categoryId = empty($_POST['category']) ? 0 : $_POST['category'];
$site = empty($_GET['site']) ? '' : $_GET['site'];
$ip = empty($_GET['ip']) ? '' : $_GET['ip'];
$search = empty($_POST['withSearch']) || empty($_POST['search']) ? '' : trim($_POST['search']);
$perPage = getUserSetting('rowsPerPage', 10); 
if (isset($_POST['perPage']) && is_numeric($_POST['perPage'])) {
	$perPage = $_POST['perPage'];
	setUserSetting('rowsPerPage', $_POST['perPage']);
}
list($trackbacks, $paging) = getTrashTrackbackWithPagingForOwner($owner, $categoryId, $site, $ip, $search, $suri['page'], $perPage);
require ROOT . '/lib/piece/owner/header0.php';
require ROOT . '/lib/piece/owner/contentMenu06.php';
?>
						<script type="text/javascript">
							//<![CDATA[
								function changeState(caller, value, mode) {
									try {			
										if (caller.className == 'block-icon bullet') {
											var command 	= 'unblock';
										} else {
											var command 	= 'block';
										}
										var name 		= caller.id.replace(/\-[0-9]+$/, '');
										param  	=  '?value='	+ encodeURIComponent(value);
										param 	+= '&mode=' 	+ mode;
										param 	+= '&command=' 	+ command;
										var request = new HTTPRequest("GET", "<?php echo $blogURL;?>/owner/setting/filter/change/" + param);
										var iconList = document.getElementsByTagName("a");	
										for (var i = 0; i < iconList.length; i++) {
											icon = iconList[i];
											if(icon.id == null || icon.id.replace(/\-[0-9]+$/, '') != name) {
												continue;
											} else {
												if (command == 'block') {
													icon.className = 'block-icon bullet';
													icon.innerHTML = '<span class="text"><?php echo _t('[차단됨]');?><\/span>';
													icon.setAttribute('title', "<?php echo _t('이 사이트는 차단되었습니다. 클릭하시면 차단을 해제합니다.');?>");
												} else {
													icon.className = 'unblock-icon bullet';
													icon.innerHTML = '<span class="text"><?php echo _t('[허용됨]');?><\/span>';
													icon.setAttribute('title', "<?php echo _t('이 사이트는 차단되지 않았습니다. 클릭하시면 차단합니다.');?>");
												}
											}
										}
										request.send();
									} catch(e) {
										alert(e.message);
									}
								}
								
								function deleteTrackback(id) {
									if (!confirm("<?php echo _t('선택된 글걸기를 삭제합니다. 계속 하시겠습니까?');?>"))
										return;
									var request = new HTTPRequest("GET", "<?php echo $blogURL;?>/owner/entry/trash/trackback/delete/" + id);
									request.onSuccess = function() {
										document.getElementById('list-form').submit();
									}
									request.onError = function () {
										alert("<?php echo _t('글걸기를 삭제하지 못했습니다.');?>");
									}
									request.send();
								}
								
								function deleteTrackbacks() {
									try {
										if (!confirm("<?php echo _t('선택된 글걸기를 삭제합니다. 계속 하시겠습니까?');?>"))
											return false;
										var oElement;
										var targets = '';
										for (i = 0; document.getElementById('list-form').elements[i]; i ++) {
											oElement = document.getElementById('list-form').elements[i];
											if ((oElement.name == "entry") && oElement.checked) {
												targets+=oElement.value+'~*_)';
											}
										}
										var request = new HTTPRequest("POST", "<?php echo $blogURL;?>/owner/entry/trash/trackback/delete/");
										request.onSuccess = function() {
											document.getElementById('list-form').submit();
										}
										request.onError = function () {
											alert("<?php echo _t('글걸기를 삭제하지 못했습니다.');?>");
										}
										request.send("targets=" + targets);
									} catch(e) {
										alert(e.message);
									}
								}

								function revertTrackback(id) {
									if (!confirm("<?php echo _t('선택된 글걸기를 복원합니다. 계속 하시겠습니까?');?>"))
										return;
									var request = new HTTPRequest("GET", "<?php echo $blogURL;?>/owner/entry/trash/trackback/revert/" + id);
									request.onSuccess = function() {
										document.getElementById('list-form').submit();
									}
									request.onError = function () {
										alert("<?php echo _t('글걸기를 삭제하지 못했습니다.');?>");
									}
									request.send();
								}
								
								function revertTrackbacks() {
									try {
										if (!confirm("<?php echo _t('선택된 글걸기를 복원합니다. 계속 하시겠습니까?');?>"))
											return false;
										var oElement;
										var targets = '';
										for (i = 0; document.getElementById('list-form').elements[i]; i ++) {
											oElement = document.getElementById('list-form').elements[i];
											if ((oElement.name == "entry") && oElement.checked) {
												targets+=oElement.value+'~*_)';
											}
										}
										var request = new HTTPRequest("POST", "<?php echo $blogURL;?>/owner/entry/trash/trackback/revert/");
										request.onSuccess = function() {
											document.getElementById('list-form').submit();
										}
										request.onError = function () {
											alert("<?php echo _t('글걸기를 삭제하지 못했습니다.');?>");
										}
										request.send("targets=" + targets);
									} catch(e) {
										alert(e.message);
									}
								}

								function checkAll(checked) {
									for (i = 0; document.getElementById('list-form').elements[i]; i++) {
										if (document.getElementById('list-form').elements[i].name == "entry") {
											if (document.getElementById('list-form').elements[i].checked != checked) {
												document.getElementById('list-form').elements[i].checked = checked;
												toggleThisTr(document.getElementById('list-form').elements[i]);
											}
										}
									}
								}
								
								window.addEventListener("load", execLoadFunction, false);
								function execLoadFunction() {
									document.getElementById('allChecked').disabled = false;
									removeItselfById('category-move-button');
									
									trashSelect = document.createElement("SELECT");
									trashSelect.id = "category";
									trashSelect.name = "category";
									trashSelect.setAttribute("onchange", "document.getElementById('trash-form').page.value=1; document.getElementById('trash-form').submit()");
									//trashSelect.setAttribute("disabled", "disabled");
									
									trashOption = document.createElement("OPTION");
									trashOption.innerHTML = "<?php echo _t('전체');?>";
									trashOption.value = "0";
									trashSelect.appendChild(trashOption);
<?php
foreach (getCategories($owner) as $category) {
?>
									trashOption = document.createElement("OPTION");
									trashOption.innerHTML = "<?php echo htmlspecialchars($category['name']);?>";
									trashOption.value = "<?php echo $category['id'];?>";
<?php
	if ($category['id'] == $categoryId) {
?>

									trashOption.setAttribute("selected", "selected");
<?php
	}
?>
									trashSelect.appendChild(trashOption);
<?php
	foreach ($category['children'] as $child) {
?>
									trashOption = document.createElement("OPTION");
									trashOption.innerHTML = " ― <?php echo htmlspecialchars($child['name']);?>";
									trashOption.value = "<?php echo $child['id'];?>";
<?php
		if ($child['id'] == $categoryId) {
?>
									trashOption.setAttribute("selected", "selected");
<?php
		}
?>
									trashSelect.appendChild(trashOption);
<?php
	}
}
?>
									
									document.getElementById('track-radio-trackback').appendChild(trashSelect);
								}
								
								function toggleThisTr(obj) {
									objTR = getParentByTagName("TR", obj);
									
									if (objTR.className.match('inactive')) {
										objTR.className = objTR.className.replace('inactive', 'active');
									} else {
										objTR.className = objTR.className.replace('active', 'inactive');
									}
								}
							//]]>
						</script>
						
						<div id="part-post-trash" class="part">
							<h2 class="caption">
								<span class="main-text"><?php echo _t('삭제 대기중인 글걸기입니다');?></span>
<?php
if (strlen($site) > 0 || strlen($ip) > 0) {
	if (strlen($site) > 0) {
?>
								<span class="filter-condition"><?php echo htmlspecialchars($site);?></span>
<?php
	}
	
	if (strlen($ip) > 0) {
?>
								<span class="filter-condition"><?php echo htmlspecialchars($ip);?></span>
<?php
	}
}
?>
							</h2>
							
							<form id="trash-form" method="post" action="<?php echo $blogURL;?>/owner/entry/trash">
								<fieldset class="section">
									<legend><?php echo _t('삭제된 파일 보기 설정');?></legend>
									
									<input type="hidden" name="page" value="<?php echo $suri['page'];?>" />
									
									<dl id="trash-type-line" class="line">
										<dt><?php echo _t('종류');?></dt>
										<dd>
											<div id="track-radio-comment">
												<input type="radio" class="radio" id="track-type-comment" name="trashType" value="comment" onclick="document.getElementById('trash-form').submit()" /> <label for="track-type-comment"><?php echo _t('댓글');?></label>
											</div>
											<div id="track-radio-trackback">
												<input type="radio" class="radio" id="track-type-trackback" name="trashType" value="trackback" onclick="document.getElementById('trash-form').submit()" checked="checked" /> <label for="track-type-trackback"><?php echo _t('글걸기');?></label>
											</div>
										</dd>
									</dl>
									
									<input type="submit" id="category-move-button" value="<?php echo _t('이동');?>" />
								</fieldset>
							</form>
							
							<form id="list-form" method="post" action="<?php echo $blogURL;?>/owner/entry/trash/trackback">
								<table class="data-inbox" cellspacing="0" cellpadding="0">
									<thead>
										<tr>
											<th class="selection"><input type="checkbox" id="allChecked" class="checkbox" onclick="checkAll(this.checked);" disabled="disabled" /></th>
											<th class="date"><span class="text"><?php echo _t('등록일자');?></span></th>
											<th class="site"><span class="text"><?php echo _t('사이트명');?></span></th>
											<th class="category"><span class="text"><?php echo _t('분류');?></span></th>
											<th class="title"><span class="text"><?php echo _t('제목');?></span></th>
											<th class="ip"><acronym title="Internet Protocol">ip</acronym></th>
											<th class="delete"><span class="text"><?php echo _t('복원');?></span></th>
											<th class="delete"><span class="text"><?php echo _t('삭제');?></span></th>
										</tr>
									</thead>
									<tbody>
<?php
$siteNumber = array();
for ($i=0; $i<sizeof($trackbacks); $i++) {
	$trackback = $trackbacks[$i];
	
	requireComponent('Tattertools.Data.Filter');
	$isFilterURL = Filter::isFiltered('url', $trackback['url']);
	$filteredURL = getURLForFilter($trackback['url']);

	$filter = new Filter();
	if (Filter::isFiltered('ip', $trackback['ip'])) {
		$isIpFiltered = true;
	} else {
		$isIpFiltered = false;
	}
	
	if (!isset($siteNumber[$trackback['site']])) {
		$siteNumber[$trackback['site']] = $i;
		$currentSite = $i;
	} else {
		$currentSite = $siteNumber[$trackback['site']];
	}
	
	$className = ($i % 2) == 1 ? 'even-line' : 'odd-line';
	$className .= ($i == sizeof($trackbacks) - 1) ? ' last-line' : '';
?>
										<tr class="<?php echo $className;?> inactive-class" onmouseover="rolloverClass(this, 'over')" onmouseout="rolloverClass(this, 'out')">
											<td class="selection"><input type="checkbox" class="checkbox" name="entry" value="<?php echo $trackback['id'];?>" onclick="document.getElementById('allChecked').checked=false; toggleThisTr(this);" /></td>
											<td class="date"><?php echo Timestamp::formatDate($trackback['written']);?></td>
											<td class="site">
<?php
	if ($isFilterURL) {
?>
												<a id="urlFilter<?php echo $currentSite;?>-<?php echo $i;?>" class="block-icon bullet" href="<?php echo $blogURL;?>/owner/setting/etc/filter/change/?value=<?php echo urlencode($filteredURL);?>&amp;mode=url&amp;command=unblock" onclick="changeState(this,'<?php echo $filteredURL;?>','url'); return false;" title="<?php echo _t('이 사이트는 차단되었습니다. 클릭하시면 차단을 해제합니다.');?>"><span class="text"><?php echo _t('[차단됨]');?></span></a>
<?php
	} else {
?>
												<a id="urlFilter<?php echo $currentSite;?>-<?php echo $i;?>" class="unblock-icon bullet" href="<?php echo $blogURL;?>/owner/setting/etc/filter/change/?value=<?php echo urlencode($filteredURL);?>&amp;mode=url&amp;command=block" onclick="changeState(this,'<?php echo $filteredURL;?>','url'); return false;" title="<?php echo _t('이 사이트는 차단되지 않았습니다. 클릭하시면 차단합니다.');?>"><span class="text"><?php echo _t('[허용됨]');?></span></a>
<?php
	}
?>
												<a href="<?php echo $blogURL;?>/owner/entry/trash/trackback?site=<?php echo urlencode(escapeJSInAttribute($trackback['site']));?>" title="<?php echo _t('이 사이트에서 보낸 글걸기 목록을 보여줍니다.');?>"><?php echo htmlspecialchars($trackback['site']);?></a>
											</td>
											<td class="category">
<?php
	if (!empty($trackback['categoryName'])) {
?>
												<span class="categorized"><?php echo $trackback['categoryName'];?></span>
<?php
	} else {
?>
												<span class="uncategorized"><?php echo $trackback['categoryName'];?></span>
<?php
	}
?>
											</td>
											<td class="title">
												<a href="<?php echo $trackback['url'];?>" onclick="window.open(this.href); return false;" title="<?php echo _t('트랙백을 보낸 포스트를 보여줍니다.');?>"><?php echo htmlspecialchars($trackback['subject']);?></a>
											</td>
											<td class="ip">
<?php
	if ($isIpFiltered) {
?>
												<a id="ipFilter<?php echo urlencode($trackback['ip']);?>-<?php echo $i;?>" class="block-icon bullet" href="<?php echo $blogURL;?>/owner/setting/etc/filter/change/?value=<?php echo urlencode($trackback['ip']);?>&amp;mode=ip&amp;command=unblock" onclick="changeState(this,'<?php echo urlencode($trackback['ip']);?>', 'ip'); return false;" title="<?php echo _t('이 IP는 차단되었습니다. 클릭하시면 차단을 해제합니다.');?>"><span class="text"><?php echo _t('[차단됨]');?></span></a>
<?php
	} else {
?>
												<a id="ipFilter<?php echo urlencode($trackback['ip']);?>-<?php echo $i;?>" class="unblock-icon bullet" href="<?php echo $blogURL;?>/owner/setting/etc/filter/change/?value=<?php echo urlencode($trackback['ip']);?>&amp;mode=ip&amp;command=block" onclick="changeState(this,'<?php echo urlencode($trackback['ip']);?>', 'ip'); return false;" title="<?php echo _t('이 IP는 차단되지 않았습니다. 클릭하시면 차단합니다.');?>"><span class="text"><?php echo _t('[허용됨]');?></span></a>
<?php
	}
?>
												<a href="<?php echo $blogURL;?>/owner/entry/trash/trackback?ip=<?php echo urlencode(escapeJSInAttribute($trackback['ip']));?>" title="<?php echo _t('이 IP로 등록된 트랙백 목록을 보여줍니다.');?>"><span class="text"><?php echo $trackback['ip'];?></span></a>
											</td>
											<td class="revert">
												<a class="revert-button button" href="<?php echo $blogURL;?>/owner/entry/trash/trackback/revert/<?php echo $trackback['id'];?>" onclick="revertTrackback(<?php echo $trackback['id'];?>); return false;" title="<?php echo _t('이 트랙백을 복원합니다.');?>"><span class="text"><?php echo _t('복원');?></span></a>
											</td>
											<td class="delete">
												<a class="delete-button button" href="<?php echo $blogURL;?>/owner/entry/trash/trackback/delete/<?php echo $trackback['id'];?>" onclick="deleteTrackback(<?php echo $trackback['id'];?>); return false;" title="<?php echo _t('이 트랙백을 삭제합니다.');?>"><span class="text"><?php echo _t('삭제');?></span></a>
											</td>
										</tr>
<?php
}
?>
									</tbody>
								</table>
								
								<hr class="hidden" />
								
								<div class="data-subbox">
									<input type="hidden" name="page" value="<?php echo $suri['page'];?>" />
									<input type="hidden" name="site" value="" />
									<input type="hidden" name="ip" value="" />
									
									<div id="delete-section" class="section">
										<span class="label"><?php echo _t('선택한 글걸기를');?></span>
										<input type="submit" class="delete-button input-button" value="<?php echo _t('삭제');?>" onclick="deleteTrackbacks(); return false;" />
									</div>
									
									<div id="page-section" class="section">
										<div id="page-navigation">
											<span id="total-count"><?php echo _f('총 %1건', empty($paging['total']) ? "0" : $paging['total']);?></span>
											<span id="page-list">
<?php
//$paging['url'] = 'document.getElementById('list-form').page.value=';
//$paging['prefix'] = '';
//$paging['postfix'] = '; document.getElementById('list-form').submit()';
$pagingTemplate = '[##_paging_rep_##]';
$pagingItemTemplate = '<a [##_paging_rep_link_##]>[[##_paging_rep_link_num_##]]</a>';
print getPagingView($paging, $pagingTemplate, $pagingItemTemplate);
?>
											</span>
										</div>
										<div class="page-count">
											<?php echo getArrayValue(explode('%1', _t('한 페이지에 글 %1건 표시')), 0);?>

											<select name="perPage" onchange="document.getElementById('list-form').page.value=1; document.getElementById('list-form').submit()">
<?php
for ($i = 10; $i <= 30; $i += 5) {
	if ($i == $perPage) {
?>
												<option value="<?php echo $i;?>" selected="selected"><?php echo $i;?></option>
<?php
	} else {
?>
												<option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php
	}
}
?>
											</select>
											
											<?php echo getArrayValue(explode('%1', _t('한 페이지에 글 %1건 표시')), 1);?>
										</div>
									</div>
								</div>
							</form>
							
							<hr class="hidden" />
							
							<form id="search-form" class="data-subbox" method="post" action="<?php echo $blogURL;?>/owner/entry/trash/trackback">
								<h2><?php echo _t('검색');?></h2>
								
								<div class="section">
									<label for="search"><?php echo _t('제목');?>, <?php echo _t('사이트명');?>, <?php echo _t('내용');?></label>
									<input type="text" id="search" class="input-text" name="search" value="<?php echo htmlspecialchars($search);?>" onkeydown="if (event.keyCode == '13') { document.getElementById('search-form').withSearch.value = 'on'; document.getElementById('search-form').submit(); }" />
									<input type="hidden" name="withSearch" value="" />
									<input type="submit" class="search-button input-button" value="<?php echo _t('검색');?>" onclick="document.getElementById('search-form').withSearch.value = 'on'; document.getElementById('search-form').submit();" />
								</div>
							</form>
						</div>
<?php
require ROOT . '/lib/piece/owner/footer1.php';
?>
