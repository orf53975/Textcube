<?php
/// Copyright (c) 2004-2014, Needlworks  / Tatter Network Foundation
/// All rights reserved. Licensed under the GPL.
/// See the GNU General Public License for more details. (/documents/LICENSE, /documents/COPYRIGHT)
class TData {
	/*@static@*/
	function removeAll($removeAttachments = true) {
		global $database;
		$blogid = getBlogId();
		POD::query("UPDATE {$database['prefix']}BlogStatistics SET visits = 0 WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}DailyStatistics WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Categories WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Attachments WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Comments WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}RemoteResponses WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}RemoteResponseLogs WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}TagRelations WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Entries WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Links WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Filters WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}RefererLogs WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}RefererStatistics WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}Plugins WHERE blogid = $blogid");
		
		POD::query("DELETE FROM {$database['prefix']}FeedStarred WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}FeedReads WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}FeedGroupRelations WHERE blogid = $blogid");
		POD::query("DELETE FROM {$database['prefix']}FeedGroups WHERE blogid = $blogid");
		
		if (file_exists(__TEXTCUBE_CACHE_DIR__."/rss/$blogid.xml"))
			unlink(__TEXTCUBE_CACHE_DIR__."/rss/$blogid.xml");
		
		if ($removeAttachments)
			Path::removeFiles(Path::combine(ROOT, 'attach', $blogid));
	}
}
?>
