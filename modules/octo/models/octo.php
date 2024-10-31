<?php
class octoModelNbs extends modelNbs {
	private $_linksReplacement = array();
	public function __construct() {
		$this->_setTbl('octo');
	}
	private function _getLinksReplacement() {
		if(empty($this->_linksReplacement)) {
			$this->_linksReplacement = array(
				'modUrl' => array('url' => $this->getModule()->getModPath(), 'key' => 'NBS_MOD_URL'),
				'siteUrl' => array('url' => NBS_SITE_URL, 'key' => 'NBS_SITE_URL'),
				'assetsUrl' => array('url' => $this->getModule()->getAssetsUrl(), 'key' => 'NBS_ASSETS_URL'),
				'assetsRootUrl' => array('url' => frameNbs::_()->getModule('templates')->getCdnUrl(), 'key' => 'NBS_ASSETS_ROOT_URL'),
			);
		}
		return $this->_linksReplacement;
	}
	/*protected function _escTplData($data) {
		$data['html'] = dbNbs::escape($data['html']);
		$data['css'] = dbNbs::escape($data['css']);
		return $data;
	}*/
	protected function _afterRemove($ids = array()) {
		parent::_afterRemove( $ids );
		if(!is_array($ids)) $ids = array($ids);
		foreach($ids as $id) {
			$this->getModule()->getModel('octo_blocks')->remove(array('oid' => $id));
		}
	}
	/**
	 * Do not remove pre-set templates
	 */
	public function clear() {
		if(frameNbs::_()->getTable( $this->_tbl )->delete(array('additionalCondition' => 'original_id != 0'))) {
			return true;
		} else
			$this->pushError (__('Database error detected', NBS_LANG_CODE));
		return false;
	}
	public function switchActive($d = array()) {
		$d['active'] = isset($d['active']) ? (int)$d['active'] : 0;
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['id'])) {
			$tbl = $this->getTbl();
			return frameNbs::_()->getTable($tbl)->update(array(
				'active' => $d['active'],
			), array(
				'id' => $d['id'],
			));
		} else
			$this->pushError (__('Invalid ID', NBS_LANG_CODE));
		return false;
	}
	public function isPostConverted($pid) {
		return frameNbs::_()->getTable( $this->getTbl() )->exists($pid, 'pid');
	}
	public function getForPost($pid) {
		$octo = $this->setWhere(array('pid' => $pid))->getFromTbl(array('return' => 'row'));
		if($octo) {
			$octo['blocks'] = $this->getBlocksForOcto($octo['id']);
			return $octo;
		}
		return false;
	}
	public function getOriginalOctoId( $oid ) {
		return  frameNbs::_()->getTable( $this->getTbl() )->get('original_id', array('id' => $oid), '', 'one');
	}
	public function getBlocksForOcto($oid) {
		$blocksModel = $this->getModule()->getModel('octo_blocks');
		return $blocksModel->setOrderBy('sort_order')->setSortOrder('ASC')->addWhere(array('oid' => $oid))->getFromTbl();
	}
	public function save($data = array()) {
		$oid = isset($data['id']) ? (int) $data['id'] : 0;
		if($oid) {
			if(isset($data['octo'])) {
				if(!$this->updateById($data['octo'], $oid)) {
					return false;
				}
			}
			// TODO: Add remove blocks here
			$blocksModel = $this->getModule()->getModel('octo_blocks');
			$currentBlockIds = array();
			$idSortArr = $blocksModel->getIdSortData($oid);
			if(!empty($idSortArr)) {
				foreach($idSortArr as $idSortData) {
					$currentBlockIds[ $idSortData['id'] ] = 1;
				}
			}
			if(isset($data['blocks']) && !empty($data['blocks'])) {
				foreach($data['blocks'] as $b) {
					if(!$blocksModel->save($b, $oid)) {
						$this->pushError( $blocksModel->getErrors() );
						return false;
					} else {
						if(isset($b['id']) && $b['id'] && isset($currentBlockIds[ $b['id'] ])) {
							unset( $currentBlockIds[ $b['id'] ] );
						}
					}
				}
			}
			if(!empty($currentBlockIds)) {
				$blocksModel->removeGroup(array_keys( $currentBlockIds ));
			}
			// Clear cache
			$this->removeCache( $oid );
			return true;
		} else
			$this->pushError (__('Invalid Octo ID', NBS_LANG_CODE));
		return false;
	}
	public function getUsedBlocksNumForPost($pid) {
		return (int) dbNbs::get('SELECT COUNT(*) AS total FROM @__octo, @__octo_blocks WHERE @__octo.id = @__octo_blocks.oid AND @__octo.pid = '. (int)$pid, 'one');
	}
	public function getPresetTemplates( $original = true ) {
		if($original) {	// Original tempaltes
			$where = array('original_id' => 0, 'is_base' => 1, 'active' => 1);
		} else {	// User created templates
			$where = 'original_id != 0 AND active = 1';
		}
		return $this->setWhere( $where )
			->setSelectFields('id, label, img, is_pro')
			->getFromTbl();
	}
	protected function _afterGetFromTbl($row) {
		$row = parent::_afterGetFromTbl($row);
		static $imgsPath = false;
		if(!$imgsPath) {
			$imgsPath = $this->getModule()->getAssetsUrl(). 'img/tpl_prev/';
		}
		if(isset($row['img'])) {
			$row['img_preview_url'] = $imgsPath. $row['img'];
		}
		if(isset($row['params'])) {
			if (base64_decode($row['params'], true)) {
					//base 64
			    $row['params'] = empty($row['params']) ? array() : utilsNbs::unserialize(base64_decode($row['params']), true);
			} else {
					//not base 64
					$row['params'] = empty($row['params']) ? array() : utilsNbs::unserialize($row['params'], true);
			}
			$row['params'] = $this->_afterDbReplace($this->_afterDbParams( $row['params'] ));
		}
		return $row;
	}
	private function _afterDbParams($params) {
		if(empty($params)) return $params;
		if(is_array($params)) {
			foreach($params as $k => $v) {
				$params[ $k ] = $this->_afterDbParams($v);
			}
			return $params;
		} else
			return stripslashes ($params);
	}
	protected function _afterDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_afterDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				/*Tmp fix - for quick replace all mode URL to assets URL*/
				$replaceFrom[] = '['. $this->_linksReplacement['modUrl']['key']. ']';
				$replaceTo[] = '['. $this->_linksReplacement['assetsUrl']['key']. ']';
				/*****/
				foreach($this->_linksReplacement as $k => $rData) {
					$replaceFrom[] = '['. $rData['key']. ']';
					$replaceTo[] = $rData['url'];
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _beforeDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_beforeDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				foreach($this->_linksReplacement as $k => $rData) {
					$replaceFrom[] = $rData['url'];
					$replaceTo[] = '['. $rData['key']. ']';
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _dataSave($data, $update = false) {
		$data = $this->_beforeDbReplace($data);
		if(isset($data['params'])) {
			//$data['params'] = base64_encode(utilsNbs::serialize($data['params']));
			$data['params'] = utilsNbs::serialize($data['params']);
		}
		return $data;
	}
	public function copy($originalId, $data = array(), $originalData = array()) {
		$original = is_numeric($originalId) ? $this->getById($originalId) : $originalId;	// It can get object too
		$originalId = $original['id'];
		unset($original['id']);
		unset($original['date_created']);
		$original['is_base'] = 0;
		$original['original_id'] = $originalId;
		if(!empty($data)) {
			if(isset($data['params'])) {
				$data['params'] = array_merge($original['params'], $data['params']);
			}
			$original = array_merge($original, $data);
		}
		$oid = $this->insert( $original );
		if($oid) {
			$originalBlocks = !empty($originalData) && isset($originalData['oid']) ? $this->getBlocksForOcto( $originalData['oid'] ) : $this->getBlocksForOcto( $originalId );
			if(!empty($originalBlocks)) {
				$blocksModel = $this->getModule()->getModel('octo_blocks');
				foreach($originalBlocks as $block) {
					$blocksModel->save(array('original_id' => $block['id']), $oid);
				}
			}
			return $oid;
		}
		return false;
	}
	public function getOctoForOriginal($originalId) {
		return $this->setWhere(array('original_id' => $originalId))->getFromTbl(array('return' => 'row'));
	}
	public function getFullById($id) {
		$octo = $this->getById( $id );
		if($octo) {
			$octo['blocks'] = $this->getBlocksForOcto($octo['id']);
			return $octo;
		}
		return false;
	}
	public function resetTpl($d = array()) {
		$oid = isset($d['id']) ? (int) $d['id'] : 0;
		if($oid) {
			return $this->remove($oid);	// Just remove it from now, after reload - it will be re-created
			//return true;
		} else
			$this->pushError (__('Invalid Octo ID', NBS_LANG_CODE));
		return false;
	}
	public function generateInline($oid) {
		// Try to retrive it from cache
		$data = $this->getDataFromCache($oid);
		if(!empty($data)) return $data;
		// Disable scripts connecting
		frameNbs::_()->ignoreJs( true );
		$content = $this->getView()->renderForPost($oid, array('returnContent' => true, 'simple' => true));
		// Remove all scripts - if there will be some
		$content = preg_replace('/<script.+<\/script>/isU', '', $content);
		// Convert all CSS assets - into <style> tags
		preg_match_all("/<link.*href=[\"']?(?P<URL>.+)[\"'].*>/iU", $content, $outStylesMatch);
		if(!empty($outStylesMatch) && isset($outStylesMatch[ 0 ]) && !empty($outStylesMatch[ 0 ])) {
			foreach($outStylesMatch[ 0 ] as $i => $outStyleTag) {
				$outStyleUrl = str_replace(array('"', "'"), '', $outStylesMatch['URL'][ $i ]);
				//var_dump($outStyleUrl); continue;
				$ignoreBuildIn = array('fonts.googleapis.com', 'font-awesome', 'animate', 'gmpg.org', 'xmlrpc.php');
				$ignore = false;
				foreach($ignoreBuildIn as $igbn) {
					if(strpos($outStyleUrl, $igbn) !== false) {
						$ignore = true;
						break;
					}
				}
				if($ignore) continue;
				preg_match("/rel=[\"'](?P<REL>.+)[\"']/iU", $outStyleTag, $relMatch);
				if(empty($relMatch) || !in_array($relMatch['REL'], array('profile', 'pingback'))) {
					$outStyleContent = wp_remote_get($outStyleUrl);
					if($outStyleContent
						&& !is_wp_error($outStyleContent)
						&& isset($outStyleContent['response'])
						&& (int) $outStyleContent['response']['code'] < 400	// Content was loaded Ok
					) {
						$content .= "<style>{$outStyleContent['body']}</style>";
					}
				}
				$content = str_replace($outStyleTag, '', $content);
			}
		}
		frameNbs::_()->ignoreJs( false );

		/*if(!class_exists('EmogrifierNbs')) {
			importNbs(NBS_HELPERS_DIR. 'emogrifier.php');
		}
		$emogrifier = new EmogrifierNbs( $content );
		$data = $emogrifier->emogrify();*/
		// Try to avoid using this lib
		$data = $content;
		// Save in cache for future usage
		$this->saveCache($oid, $data);
		return $data;
	}
	public function saveCache($oid, $data) {
		return frameNbs::_()->getTable('octo_cache')->insert(array('oid' => $oid, 'data' => dbNbs::escape($data)));
	}
	public function removeCache($oid) {
		if(!is_array($oid))
			$oid = array( $oid );
		return frameNbs::_()->getTable('octo_cache')->delete("oid IN (". implode(',', $oid). ")");
	}
	public function getDataFromCache($oid) {
		return frameNbs::_()->getTable('octo_cache')->get('data', array('oid' => $oid), '', 'one');
	}
	public function addDynPostIds( $oid, $postIds ) {
		$currPostIds = $this->getDynPostIds( $oid );
		if($currPostIds)
			$currPostIds = array_unique(array_merge($currPostIds, $postIds));
		else
			$currPostIds = $postIds;
		$this->setDynPostIds( $oid, $currPostIds );
	}
	public function setDynPostIds( $oid, $postIds ) {
		$dynPostIds = frameNbs::_()->getModule('options')->get('dyn_post_ids');
		if(!$dynPostIds)
			$dynPostIds = array();
		$dynPostIds[ $oid ] = $postIds;
		frameNbs::_()->getModule('options')->getModel()->save( 'dyn_post_ids', $dynPostIds );
	}
	public function getDynPostIds( $oid ) {
		$dynPostIds = frameNbs::_()->getModule('options')->get('dyn_post_ids');
		if(!$dynPostIds) return false;
		return isset($dynPostIds[ $oid ]) && $dynPostIds[ $oid ] ? $dynPostIds[ $oid ] : false;
	}
	public function clearDynPostIds( $oid ) {
		$this->setDynPostIds( $oid, false );
	}
}
