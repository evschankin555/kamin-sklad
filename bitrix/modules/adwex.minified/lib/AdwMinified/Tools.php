<?php
namespace AdwMinified;
IncludeTemplateLangFile(__FILE__);

class Tools {
    const partnerName	= 'adwex';
    const solutionName	= 'minified';
	const moduleID		= 'adwex.minified';
	const commonTabId	= 'adwCommon';
	static $arMetaParams = array();
    
    public static function getBasePath () {
        $uploadDir = \COption::GetOptionString('main', 'upload_dir', 'upload');
        $directory = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir . '/' . Tools::moduleID;
        return $directory;
    }
    
    public static function FileSizeConvert($fileSize) {
        $sizes = array('TB', 'GB', 'MB', 'KB', 'B');
        $total = count($sizes);
        while ($total-- && $fileSize > 1024) {
            $fileSize /= 1024;
        }
        return round($fileSize, $digits) . ' ' . $sizes[$total];
    }
    
    public static function drawOptionRow ($moduleId, $option, $siteID) {
        \CModule::IncludeModule('fileman');
		$arControllerOption = \CControllerClient::GetInstalledOptions($moduleId);
		if (!is_array($option)): ?>
		    <tr class="heading"><td colspan="2"><?= $option ?></td></tr>
        <? elseif (isset($option['note'])):
			$name = substr($option[0], 0, (strlen($option[0]) - strlen($siteID) - 1));
		?>
			<tr class="row-<?= strtolower($name) ?>">
				<td colspan="2" align="center">
					<?= BeginNote('align="left" style="text-align: left"');?>
					<?= $option['note'] ?>
					<?= EndNote();?>
				</td>
			</tr>
		<? else:
			$name  = substr($option[0], 0, (strlen($option[0]) - strlen($siteID)-1));
            if ($name === '') {
                $val = $option[2];
            } else {
                if ($siteID == self::commonTabId) {
                    $val = \COption::GetOptionString($moduleId, $name, $option[2]);
                } else {                
                    $val = \COption::GetOptionString($moduleId, $name, $option[2], $siteID);
                }
            }
			$type = $option[3];
			$disabled = array_key_exists(4, $option) && $option[4] == 'Y' ? ' disabled' : '';
			$supText = array_key_exists(5, $option) ? $option[5] : '';
			$helpText = array_key_exists(6, $option) ? $option[6] : '';
		?>
			<tr class="row-<?= strtolower($name) ?>">
				<td<?
                if ($type[0] == 'multiselectbox' || $type[0] == 'textarea' || $type[0] == 'statictext' || $type[0] == 'statichtml') {
                        echo ' class="adm-detail-valign-top"';
                } ?> width="30%"><?
					if ($type[0]=='checkbox') {
                        echo '<label for="' . htmlspecialcharsbx($option[0]) . '">' . $option[1] . '</label>';
                    } else {
                        echo $option[1];
                    }
					if (!empty($supText)) {
                        ?><span class="required"><sup><?= $supText ?></sup></span><?
                    }
				?></td>
				<td width="70%"><?
				if ($type[0] == 'checkbox') {
                    ?><input type="checkbox" <?if (isset($arControllerOption[$option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> id="<?= htmlspecialcharsbx($option[0])?>" name="<?= htmlspecialcharsbx($option[0])?>" value="Y"<?if ($val=="Y")echo" checked";?><?= $disabled?><?if ($type[2]<>'') echo " ".$type[2] ?>><?
				}
                elseif ($type[0] == 'text' || $type[0]=='password' || $type[0]=='number' || $type[0]=='color') {
                    ?><input class="adm-input" type="<?= $type[0] ?>"<?if (isset($arControllerOption[$option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> size="<?= $type[1] ?>" maxlength="255" value="<?= htmlspecialcharsbx($val)?>" name="<?= htmlspecialcharsbx($option[0])?>"<?= $disabled?><?=($type[0]=="password"? ' autocomplete="off"':'')?>><?
				}
                elseif ($type[0]=='select') {
					$arr = $type[1];
					if (!is_array($arr)) {
                        $arr = array();
                    }
					$arr_keys = array_keys($arr);
					?><select name="<?= htmlspecialcharsbx($option[0])?>" <?if (isset($arControllerOption[$option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> <?= $disabled?>><?
						$count = count($arr_keys);
						for($j=0; $j<$count; $j++): ?><option value="<?= $arr_keys[$j] ?>"<?if ($val==$arr_keys[$j])echo" selected"?>><?= htmlspecialcharsbx($arr[$arr_keys[$j]])?></option><? endfor;
						?></select><?
                }
				elseif ($type[0] == 'multiselect') {
					$arr = $type[1];
					if (!is_array($arr)) {
                        $arr = array();
                    }
					$arr_keys = array_keys($arr);
					$arr_val = explode(",", $val);

					?>
					<select size="<?=($type[2] ? $type[2] :5);?>" <?if (isset($arControllerOption[$option[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> multiple name="<?= htmlspecialcharsbx($option[0])?>[]"<?= $disabled?>><?
						$count = count($arr_keys);
						for($j=0; $j<$count; $j++): ?><option value="<?= $arr_keys[$j] ?>"<?if (in_array($arr_keys[$j],$arr_val)) echo " selected"?>><?= htmlspecialcharsbx($arr[$arr_keys[$j]])?></option><? endfor;
					?></select><?
                }
				elseif ($type[0] == 'includefile') {
					if (!is_array($type[1]['INCLUDEFILE'])) {
						$type[1]['INCLUDEFILE'] = array($type[1]['INCLUDEFILE']);
					}
					foreach($type[1]['INCLUDEFILE'] as $includefile) {
						$includefile = str_replace('//', '/', str_replace('#SITE_DIR#', $arTab['SITE_DIR'].'/', $includefile));
						if (strpos($includefile, '#') === false) {
							$template = (isset($type[1]['TEMPLATE']) && strlen($type[1]['TEMPLATE']) ? 'include_area.php' : $type[1]['TEMPLATE']);
							$href = (!strlen($includefile) ? "javascript:;" : "javascript: new BX.CAdminDialog({'content_url':'/bitrix/admin/public_file_edit.php?site=".$arTab['SITE_ID']."&bxpublic=Y&from=includefile&templateID=".TEMPLATE_NAME."&path=".$includefile."&lang=".LANGUAGE_ID."&template=".$template."&subdialog=Y&siteTemplateId=".TEMPLATE_NAME."','width':'1009','height':'503'}).Show();");
							?><a class="adm-btn" href="<?= $href?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" title="<?=GetMessage('OPTIONS_EDIT_BUTTON_TITLE')?>"><?=GetMessage('OPTIONS_EDIT_BUTTON_TITLE')?></a>&nbsp;<?
						}
					}
                }
				elseif ($type[0] == 'textarea') {
                    ?><textarea <? if (isset($arControllerOption[$option[0]])) {
                        echo ' disabled title="'.GetMessage('MAIN_ADMIN_SET_CONTROLLER_ALT').'"';
                    } ?> rows="<?= $type[1] ?>" cols="<?= $type[2] ?>" name="<?= htmlspecialcharsbx($option[0])?>"<?= $disabled?>><?= htmlspecialcharsbx($val)?></textarea><?
                }
				elseif ($type[0] == 'file') {
					$val = unserialize($val);
					$option['MULTIPLE'] = 'N';
					self::showFilePropertyField($option[0], $option, $val);
                }
				elseif ($type[0] == 'statictext') {
                    echo htmlspecialcharsbx($val);
                }
				elseif ($type[0] == 'statichtml') {
                    echo $val;
                }
				?></td>
			</tr>
		<?
		endif;
	}

	public static function drawOptionRowCustom ($html) {
		echo '<tr><td colspan="2">'.$html.'</td></tr>';
	}
    
    public function showFilePropertyField ($name, $arOption, $values) {
		global $bCopy, $historyId;

		if (!is_array($values)) {
			$values = array($values);
		}

		if ($bCopy || empty($values)) {
			$values = array('n0' => 0);
		}

		$optionWidth = $arOption['WIDTH'] ? $arOption['WIDTH'] : 200;
		$optionHeight = $arOption['HEIGHT'] ? $arOption['HEIGHT'] : 100;

		if ($arOption['MULTIPLE'] == 'N') {
			foreach($values as $key => $val) {
				if (is_array($val)) {
					$file_id = $val['VALUE'];
				}
				else {
					$file_id = $val;
				}
				if ($historyId > 0) {
					echo \CFileInput::Show($name.'['.$key.']', $file_id,
						array(
							'IMAGE' => $arOption['IMAGE'],
							'PATH' => 'Y',
							'FILE_SIZE' => 'Y',
							'DIMENSIONS' => 'Y',
							'IMAGE_POPUP' => 'Y',
							'MAX_SIZE' => array(
								'W' => $optionWidth,
								'H' => $optionHeight,
							),
						)
					);
				}
				else {
					echo \CFileInput::Show($name.'['.$key.']', $file_id,
						array(
							'IMAGE' => $arOption['IMAGE'],
							'PATH' => 'Y',
							'FILE_SIZE' => 'Y',
							'DIMENSIONS' => 'Y',
							'IMAGE_POPUP' => 'Y',
							'MAX_SIZE' => array(
							'W' => $optionWidth,
							'H' => $optionHeight,
							),
						),
						array(
							'upload' => true,
							'medialib' => true,
							'file_dialog' => true,
							'cloud' => true,
							'del' => true,
							'description' => $arOption['WITH_DESCRIPTION'] == 'Y',
						)
					);
				}
				break;
			}
		}
		else {
			$inputName = array();
			foreach($values as $key => $val) {
				if (is_array($val)) {
					$inputName[$name.'['.$key.']'] = $val['VALUE'];
				}
				else {
					$inputName[$name.'['.$key.']'] = $val;
				}
			}
			if ($historyId > 0) {
				echo \CFileInput::ShowMultiple($inputName, $name.'[n#IND#]',
					array(
						'IMAGE' => $arOption['IMAGE'],
						'PATH' => 'Y',
						'FILE_SIZE' => 'Y',
						'DIMENSIONS' => 'Y',
						'IMAGE_POPUP' => 'Y',
						'MAX_SIZE' => array(
							'W' => $optionWidth,
							'H' => $optionHeight,
						),
					),
				false);
			}
			else {
				echo \CFileInput::ShowMultiple($inputName, $name.'[n#IND#]',
					array(
						'IMAGE' => $arOption['IMAGE'],
						'PATH' => 'Y',
						'FILE_SIZE' => 'Y',
						'DIMENSIONS' => 'Y',
						'IMAGE_POPUP' => 'Y',
						'MAX_SIZE' => array(
							'W' => $optionWidth,
							'H' => $optionHeight,
						),
					),
				false,
					array(
						'upload' => true,
						'medialib' => true,
						'file_dialog' => true,
						'cloud' => true,
						'del' => true,
						'description' => $arOption['WITH_DESCRIPTION'] == 'Y',
					)
				);
			}
		}
	}

    public static function saveOptionRow($moduleId, $arOption) {
		if (!is_array($arOption)) {
			return false;
		}

		$arControllerOption = \CControllerClient::GetInstalledOptions($moduleId);
		if (isset($arControllerOption[$arOption[0]])) {
			return false;
		}

		$name = $arOption[0];
		$val = $_REQUEST[$name];        
		$siteID = end(explode('_', $name));
		$name = substr($name, 0, (strlen($name) - strlen($siteID) - 1));
        
        if (empty($name)) {
            return;
        }

		if (array_key_exists(4, $arOption) && $arOption[4] == 'Y') {
			if ($arOption[3][0] == 'checkbox') {
				$val = 'N';
			}
			else {
				return false;
			}
		}

		if ($arOption[3][0] == 'checkbox' && $val != 'Y') {
			$val = 'N';
		}
		elseif ($arOption[3][0] == 'multiselectbox') {
			$val = @implode(',', $val);
		}
		elseif ($arOption[3][0] == 'file') {
			$val = $arValueDefault = serialize(array());
			if (isset($_REQUEST[$name.'_'.$siteID.'_del']) || (isset($_FILES[$arOption[0]]) && strlen($_FILES[$arOption[0]]['tmp_name']['0']))) {
				$arValues = unserialize(\COption::GetOptionString($moduleId, $name, $arValueDefault, $siteID));
				$arValues = (array)$arValues;
				foreach($arValues as $fileID) {
					\CFile::Delete($fileID);
				}
			}

			if (isset($_FILES[$arOption[0]]) && (strlen($_FILES[$arOption[0]]['tmp_name']['n0']) || strlen($_FILES[$arOption[0]]['tmp_name']['0']))) {
				$arValues = array();
				$absFilePath = (strlen($_FILES[$arOption[0]]['tmp_name']['n0']) ? $_FILES[$arOption[0]]['tmp_name']['n0'] : $_FILES[$arOption[0]]['tmp_name']['0']);
				$arOriginalName = (strlen($_FILES[$arOption[0]]['name']['n0']) ? $_FILES[$arOption[0]]['name']['n0'] : $_FILES[$arOption[0]]['name']['0']);
				if (file_exists($absFilePath)) {
					$arFile = \CFile::MakeFileArray($absFilePath);
					$arFile['name'] = $arOriginalName; // for original file extension

					if ($bIsIco = strpos($arOriginalName, '.ico') !== false) {
						$script_files = \COption::GetOptionString('fileman', "~script_files", "php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,dll,exe,ico,shtm,shtml,fcg,fcgi,fpl,asmx,pht,py,psp,var");
						$arScriptFiles = explode(',', $script_files);
						if (($p = array_search('ico', $arScriptFiles)) !== false) {
							unset($arScriptFiles[$p]);
						}
						$tmp = implode(',', $arScriptFiles);
						\COption::SetOptionString('fileman', '~script_files', $tmp);
					}

					if ($fileID = \CFile::SaveFile($arFile, self::moduleID)) {
						$arValues[] = $fileID;
					}

					if ($bIsIco) {
						\COption::SetOptionString('fileman', '~script_files', $script_files);
					}
				}
				$val = serialize($arValues);
			}

			if (!isset($_FILES[$arOption[0]]) || (!strlen($_FILES[$arOption[0]]['tmp_name']['n0']) && !strlen($_FILES[$arOption[0]]['tmp_name']['0']) && !isset($_REQUEST[$name.'_'.$siteID.'_del']))) {
				return;
			}
		}
        if ($siteID == self::commonTabId) {
            if ($name == 'MINIFY_CSS_TOOLS') {
                if (\COption::GetOptionString($moduleId, $name) !== $val) {
                    self::clearFileCache('css');
                }
            }
            if ($name == 'MINIFY_JS_TOOLS') {
                if (\COption::GetOptionString($moduleId, $name) !== $val) {
                    self::clearFileCache('js');
                }
            }
            if ($name == 'QUALITY_WEBP') {
                if (\COption::GetOptionString($moduleId, $name) !== $val) {
                    WebP::serverPngTest();
                }
            }
            if ($name == 'CONVERT_PNG_TYPE') {
                if (\COption::GetOptionString($moduleId, $name) !== $val) {
                    WebP::clearFolder();
                }
            }
            \COption::SetOptionString($moduleId, $name, $val, $arOption[1]);
            if ($name == 'MINIFY_DOWNLOAD') {
				if ($val == 'Y') {
					RegisterModuleDependences('main', 'OnFileSave', $moduleId, 'OptiImgEvents', 'CompressOnSave', '100000');
				} else {
					UnRegisterModuleDependences('main', 'OnFileSave', $moduleId, 'OptiImgEvents', 'CompressOnSave');
				}
			}
			if ($name == 'MINIFY_RESIZE') {
				if ($val == 'Y') {
					RegisterModuleDependences('main', 'OnAfterResizeImage', $moduleId, 'OptiImgEvents', 'CompressOnResize', '100000');
				} else {
					UnRegisterModuleDependences('main', 'OnAfterResizeImage', $moduleId, 'OptiImgEvents', 'CompressOnResize');
				}
			}
        } else {
            \COption::SetOptionString($moduleId, $name, $val, $arOption[1], $siteID);
        }
	}
    
    private static function clearFileCache ($type) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/';
        if ($type == 'css' || $type == 'js') {
            $path .= $type . '/';
            if (is_dir($path)) {
                $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
                $files = array(); 
                foreach ($rii as $file) {
                    if ($file->isDir()) { 
                        continue;
                    }
                    if (strpos($file->getPathname(), '.min.' . $type) !== false) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
    }
    
    public static function getModuleOptionsList ($forSite = true) {        
        \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
        
        $optionList = array();
        
        $canCreateWebp = \AdwMinified\WebP::serverSupport();
        
        if ($forSite) {
            $optionList[] = GetMessage('OTHER_OPTIONS');
            // $optionList[] = array('USE_PARSER', GetMessage('USE_PARSER'), 'N', ['checkbox'], 'N', 'beta');
            $optionList[] = array('WORK_FOR_GROUP', GetMessage('WORK_FOR_GROUP'), 'ALL', ['select', [
                'NOONE' => GetMessage('WORK_FOR_GROUP_NOONE'),
                'ADMIN' => GetMessage('WORK_FOR_GROUP_ADMIN'),
                'ALL' => GetMessage('WORK_FOR_GROUP_ALL')
            ]]);
            $optionList[] = array('USE_LAZYLOAD', GetMessage('USE_LAZYLOAD'), 'N', ['checkbox'], 'N', 'beta - 1');
            $optionList[] = array('CREATE_WEBP', GetMessage('CREATE_WEBP'), 'Y', ['checkbox'], ($canCreateWebp ? 'N' : 'Y'), '2');
            $optionList[] = array('SEARCH_ALL_IMAGES', GetMessage('SEARCH_ALL_IMAGES'), 'N', ['checkbox'], 'N', '3');
            $optionList[] = array('INLINE_CSS', GetMessage('INLINE_CSS'), 'Y', ['checkbox'], 'N', '4');
            $optionList[] = array('INLINE_CSS_SMALL_ONLY', GetMessage('INLINE_CSS_SMALL_ONLY'), 'Y', ['checkbox']);
            // $optionList[] = array('INLINE_CSS_TO_BOTTOM', GetMessage('INLINE_CSS_TO_BOTTOM'), 'N', ['checkbox']);
            $optionList[] = array('MINIFIED_CSS', GetMessage('MINIFIED_CSS'), 'N', ['checkbox'], 'N', '5');
            $optionList[] = array('MINIFIED_JS', GetMessage('MINIFIED_JS'), 'N', ['checkbox'], 'N', '5');
            $optionList[] = array('MINIFIED_HTML', GetMessage('MINIFIED_HTML'), 'N', ['checkbox'], 'N', '6');
            $optionList[] = array('DELETE_BITRIX_OPENSANS', GetMessage('DELETE_BITRIX_OPENSANS'), 'Y', ['checkbox'], 'N', '7');
            $optionList[] = array('OPTIMIZE_GFONTS', GetMessage('OPTIMIZE_GFONTS'), 'Y', ['checkbox'], 'N', '8');
            $optionList[] = array('OPTIMIZE_GFONTS_INLINE', GetMessage('OPTIMIZE_GFONTS_INLINE'), 'N', ['checkbox'], 'N', '8');
            $optionList[] = array('note' => GetMessage('HINT_PRECONNECT'));
            $optionList[] = array('ADD_PRECONNECT_TAG_MANGER', GetMessage('ADD_PRECONNECT_TAG_MANGER'), 'N', ['checkbox']);
            $optionList[] = array('ADD_PRECONNECT_ANALITICS', GetMessage('ADD_PRECONNECT_ANALITICS'), 'N', ['checkbox']);
            $optionList[] = array('ADD_PRECONNECT_METRICA', GetMessage('ADD_PRECONNECT_METRICA'), 'N', ['checkbox']);
            $optionList[] = array('ADD_PRECONNECT_FACEBOOK', GetMessage('ADD_PRECONNECT_FACEBOOK'), 'N', ['checkbox']);
            $optionList[] = array('ADD_PRECONNECT_JIVOSITE', GetMessage('ADD_PRECONNECT_JIVOSITE'), 'N', ['checkbox']);
            $optionList[] = array('note' => GetMessage('HINT'));
        } else {
            $optionList[] = GetMessage('OPTIONS_WEBP');
            $optionList[] = array('QUALITY_WEBP', GetMessage('QUALITY_WEBP'), '90', ['number'], ($canCreateWebp ? 'N' : 'Y'));
            $optionList[] = array('CONVERT_PNG_TYPE', GetMessage('CONVERT_PNG_TYPE'), 'skip', array('select', array(
                'instant' => GetMessage('CONVERT_PNG_TYPE_INSTANT'),
                'convert' => GetMessage('CONVERT_PNG_TYPE_TO_JPG'),
                'skip' => GetMessage('CONVERT_PNG_TYPE_SKIP')
            )), ($canCreateWebp ? 'N' : 'Y'));
            if ($canCreateWebp) {
                $optionList[] = array('', GetMessage('OPTIONS_TEST_PNG_WEBP_EXAMPLE'), GetMessage('OPTIONS_TEST_PNG_WEBP', [
                    '#ORG_SIZE#' => self::FileSizeConvert(\COption::GetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_ORG', 0)),
                    '#WI_SIZE#' => self::FileSizeConvert(\COption::GetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_WI', 0)),
                    '#WC_SIZE#' => self::FileSizeConvert(\COption::GetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_WC', 0)),
                ]), ['statichtml']);
            }
            $optionList[] = GetMessage('OPTIONS_OTHER');
            $optionList[] = array('QUALITY_JPG', GetMessage('QUALITY_JPG'), '85', ['number']);
            $optionList[] = array('QUALITY_PNG', GetMessage('QUALITY_PNG'), '75', ['number']);
            $optionList[] = array('MINIFY_CSS_TOOLS', GetMessage('MINIFY_CSS_TOOLS'), GetMessage('MINIFY_CSS_TOOLS_MINIFY'), array('select', array(
                GetMessage('MINIFY_CSS_TOOLS_PHPWEE') => GetMessage('MINIFY_CSS_TOOLS_PHPWEE'),
                GetMessage('MINIFY_CSS_TOOLS_MINIFY') => GetMessage('MINIFY_CSS_TOOLS_MINIFY')
            )));
            $optionList[] = array('MINIFY_JS_TOOLS', GetMessage('MINIFY_JS_TOOLS'), GetMessage('MINIFY_HTML_TOOLS_PHPWEE'), array('select', array(
                GetMessage('MINIFY_JS_TOOLS_PATCHWORK') => GetMessage('MINIFY_JS_TOOLS_PATCHWORK'),
                GetMessage('MINIFY_JS_TOOLS_PHPWEE') => GetMessage('MINIFY_JS_TOOLS_PHPWEE'),
                GetMessage('MINIFY_JS_TOOLS_JSMIN') => GetMessage('MINIFY_JS_TOOLS_JSMIN'),
                GetMessage('MINIFY_JS_TOOLS_MINIFY') => GetMessage('MINIFY_JS_TOOLS_MINIFY')
            )));
            $optionList[] = array('MINIFY_HTML_TOOLS', GetMessage('MINIFY_HTML_TOOLS'), GetMessage('MINIFY_HTML_TOOLS_PHPWEE'), array('select', array(
                GetMessage('MINIFY_HTML_TOOLS_PHPWEE') => GetMessage('MINIFY_HTML_TOOLS_PHPWEE'),
                GetMessage('MINIFY_HTML_TOOLS_TINYHTML') => GetMessage('MINIFY_HTML_TOOLS_TINYHTML'),
                GetMessage('MINIFY_HTML_TOOLS_SHAUN') => GetMessage('MINIFY_HTML_TOOLS_SHAUN')
            )));
            $optionList[] = array('MINIFY_LOADELEMNT', GetMessage('MINIFY_LOADELEMNT'), 'N', ['checkbox']);
            $optionList[] = array('MINIFY_LOADSECTION', GetMessage('MINIFY_LOADSECTION'), 'N', ['checkbox']);
            $optionList[] = array('MINIFY_ADDINFILETABLE', GetMessage('MINIFY_ADDINFILETABLE'), 'N', ['checkbox']);
            $optionList[] = array('MINIFY_RESIZE', GetMessage('MINIFY_RESIZE'), 'N', ['checkbox']);
            $optionList[] = array('EXCEPT_FOLDER', GetMessage('EXCEPT_FOLDER'), 'tmp, wizards, install', ['text']);
        }
        
        return $optionList;
    }
    
    public static function getModuleSiteTabList($onlyTemplate = false) {  
        $messages = \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
		$by = 'id';
        $sort = 'asc';
        $arSites = array();
		$db_res = \CSite::GetList($by , $sort ,array('ACTIVE' => 'Y'));
		while($res = $db_res->GetNext()) {
			$arSites[] = $res;
        }
		$arTabs = array();
		$arTabs[] = array(
			'DIV' => 'editCommon',
			'TAB' => $messages['COMMONS_OPTIONS'],
			'ICON' => 'main_user_edit',
			'PAGE_TYPE' => 'site_settings',
			'SITE_ID' => self::commonTabId,
			'OPTIONS' => self::getModuleOptionsList(false),
		);
		foreach ($arSites as $key => $arSite) {
			$arTabs[] = array(
				'DIV' => 'edit' . ($key + 1),
				'TAB' => $arSite['NAME'],
				'ICON' => 'settings',
				'PAGE_TYPE' => 'site_settings',
				'SITE_ID' => $arSite['ID'],
				'OPTIONS' => self::getModuleOptionsList(),
			);
		}

		return array('TABS' => $arTabs);
	}
    
    public static function addMenu(&$adminMenu, &$moduleMenu) {
        $messages = \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);      
        $moduleMenu[] = array(
            'parent_menu' => 'global_menu_services',
            'section' => $messages['MENU_NAME'],
            'sort' => 100,
            'url' => '/bitrix/admin/adwex_minify_image.php?lang='.LANG,
            'text' => $messages['MENU_NAME'],
            'title' => $messages['MENU_NAME'],
            'items_id' => 'menu_' . self::moduleID,
            'items' => array()
        );
    }
}
