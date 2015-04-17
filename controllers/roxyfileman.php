<?php

require_once FUEL_PATH . '/controllers/module.php';
include dirname(__DIR__) . '/views/system.inc.php';
include dirname(__DIR__) . '/views/php/functions.inc.php';

class Roxyfileman extends Module {

	public $nav_selected = 'fileman/roxyfileman';

	function __construct() {
		parent::__construct();
	}

	function index() {

		$vars['page_title'] = $this->fuel->admin->page_title(array(lang('module_fileman')), FALSE);
		$crumbs = array('tools' => lang('section_tools'), lang('module_fileman'));

		$this->fuel->admin->set_titlebar($crumbs, 'ico_fileman');
		$this->fuel->admin->render(FILEMAN_FOLDER . '/index', $vars, '', FUEL_FOLDER);
	}
	// DIRETREE
	function dirtree() {

		verifyAction('DIRLIST');
		checkAccess('DIRLIST');

		$type = (empty($_GET['type']) ? '' : strtolower($_GET['type']));
		if ($type != 'image' && $type != 'flash') {
			$type = '';
		}

		echo "[\n";
		$tmp = $this->getFilesNumber(fixPath(getFilesPath()), $type);
		echo '{"p":"' . mb_ereg_replace('"', '\\"', getFilesPath()) . '","f":"' . $tmp['files'] . '","d":"' . $tmp['dirs'] . '"}';
		$this->GetDirs(getFilesPath(), $type);
		echo "\n]";
	}

	function getFilesNumber($path, $type) {
		$files = 0;
		$dirs = 0;
		$tmp = listDirectory($path);
		foreach ($tmp as $ff) {
			if ($ff == '.' || $ff == '..') {
				continue;
			} elseif (is_file($path . '/' . $ff) && ($type == '' || ($type == 'image' && RoxyFile::IsImage($ff)) || ($type == 'flash' && RoxyFile::IsFlash($ff)))) {
				$files++;
			} elseif (is_dir($path . '/' . $ff)) {
				$dirs++;
			}

		}

		return array('files' => $files, 'dirs' => $dirs);
	}
	function GetDirs($path, $type) {
		$ret = $sort = array();
		$files = listDirectory(fixPath($path), 0);
		foreach ($files as $f) {
			$fullPath = $path . '/' . $f;
			if (!is_dir(fixPath($fullPath)) || $f == '.' || $f == '..') {
				continue;
			}

			$tmp = $this->getFilesNumber(fixPath($fullPath), $type);
			$ret[$fullPath] = array('path' => $fullPath, 'files' => $tmp['files'], 'dirs' => $tmp['dirs']);
			$sort[$fullPath] = $f;
		}
		natcasesort($sort);
		foreach ($sort as $k => $v) {
			$tmp = $ret[$k];
			echo ',{"p":"' . mb_ereg_replace('"', '\\"', $tmp['path']) . '","f":"' . $tmp['files'] . '","d":"' . $tmp['dirs'] . '"}';
			$this->GetDirs($tmp['path'], $type);
		}
	}
	// END DIRTREE

	// CREATEDIR
	function createdir() {
		verifyAction('CREATEDIR');
		checkAccess('CREATEDIR');

		$path = trim(empty($_POST['d']) ? '' : $_POST['d']);
		$name = trim(empty($_POST['n']) ? '' : $_POST['n']);
		verifyPath($path);

		if (is_dir(fixPath($path))) {
			if (mkdir(fixPath($path) . '/' . $name, octdec(DIRPERMISSIONS))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_CreateDirFailed') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_CreateDirInvalidPath'));
		}

	}

	function deletedir() {
		verifyAction('DELETEDIR');
		checkAccess('DELETEDIR');

		$path = trim(empty($_GET['d']) ? '' : $_GET['d']);
		verifyPath($path);

		if (is_dir(fixPath($path))) {
			if (fixPath($path . '/') == fixPath(getFilesPath() . '/')) {
				echo getErrorRes(t('E_CannotDeleteRoot'));
			} elseif (count(glob(fixPath($path) . "/*"))) {
				echo getErrorRes(t('E_DeleteNonEmpty'));
			} elseif (rmdir(fixPath($path))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_CannotDeleteDir') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_DeleteDirInvalidPath') . ' ' . $path);
		}

	}

	function movedir() {
		verifyAction('MOVEDIR');
		checkAccess('MOVEDIR');

		$path = trim(empty($_GET['d']) ? '' : $_GET['d']);
		$newPath = trim(empty($_GET['n']) ? '' : $_GET['n']);
		verifyPath($path);
		verifyPath($newPath);

		if (is_dir(fixPath($path))) {
			if (mb_strpos($newPath, $path) === 0) {
				echo getErrorRes(t('E_CannotMoveDirToChild'));
			} elseif (file_exists(fixPath($newPath) . '/' . basename($path))) {
				echo getErrorRes(t('E_DirAlreadyExists'));
			} elseif (rename(fixPath($path), fixPath($newPath) . '/' . basename($path))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_MoveDir') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_MoveDirInvalisPath'));
		}

	}

	function renamedir() {
		verifyAction('RENAMEDIR');
		checkAccess('RENAMEDIR');

		$path = trim(empty($_POST['d']) ? '' : $_POST['d']);
		$name = trim(empty($_POST['n']) ? '' : $_POST['n']);
		verifyPath($path);

		if (is_dir(fixPath($path))) {
			if (fixPath($path . '/') == fixPath(getFilesPath() . '/')) {
				echo getErrorRes(t('E_CannotRenameRoot'));
			} elseif (rename(fixPath($path), dirname(fixPath($path)) . '/' . $name)) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_RenameDir') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_RenameDirInvalidPath'));
		}

	}

	// FILESLIST
	function fileslist() {

		verifyAction('FILESLIST');
		checkAccess('FILESLIST');

		$path = (empty($_POST['d']) ? getFilesPath() : $_POST['d']);
		$type = (empty($_POST['type']) ? '' : strtolower($_POST['type']));
		if ($type != 'image' && $type != 'flash') {
			$type = '';
		}

		verifyPath($path);

		$files = listDirectory(fixPath($path), 0);
		natcasesort($files);
		$str = '';
		echo '[';
		foreach ($files as $f) {
			$fullPath = $path . '/' . $f;
			if (!is_file(fixPath($fullPath)) || ($type == 'image' && !RoxyFile::IsImage($f)) || ($type == 'flash' && !RoxyFile::IsFlash($f))) {
				continue;
			}

			$size = filesize(fixPath($fullPath));
			$time = filemtime(fixPath($fullPath));
			$w = 0;
			$h = 0;
			if (RoxyFile::IsImage($f)) {
				$tmp = @getimagesize(fixPath($fullPath));
				if ($tmp) {
					$w = $tmp[0];
					$h = $tmp[1];
				}
			}
			$str .= '{"p":"' . mb_ereg_replace('"', '\\"', $fullPath) . '","s":"' . $size . '","t":"' . $time . '","w":"' . $w . '","h":"' . $h . '"},';
		}
		$str = mb_substr($str, 0, -1);
		echo $str;
		echo ']';
	}

	function upload() {

		verifyAction('UPLOAD');
		checkAccess('UPLOAD');

		$isAjax = (isset($_POST['method']) && $_POST['method'] == 'ajax');
		$path = trim(empty($_POST['d']) ? getFilesPath() : $_POST['d']);
		verifyPath($path);
		$res = '';
		if (is_dir(fixPath($path))) {
			if (!empty($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
				$errors = $errorsExt = array();
				foreach ($_FILES['files']['tmp_name'] as $k => $v) {
					$filename = $_FILES['files']['name'][$k];
					$filename = RoxyFile::MakeUniqueFilename(fixPath($path), $filename);
					$filePath = fixPath($path) . '/' . $filename;
					$isUploaded = true;
					if (!RoxyFile::CanUploadFile($filename)) {
						$errorsExt[] = $filename;
						$isUploaded = false;
					} elseif (!move_uploaded_file($v, $filePath)) {
						$errors[] = $filename;
						$isUploaded = false;
					}
					if (is_file($filePath)) {
						@chmod($filePath, octdec(FILEPERMISSIONS));
					}
					if ($isUploaded && RoxyFile::IsImage($filename) && (intval(MAX_IMAGE_WIDTH) > 0 || intval(MAX_IMAGE_HEIGHT) > 0)) {
						RoxyImage::Resize($filePath, $filePath, intval(MAX_IMAGE_WIDTH), intval(MAX_IMAGE_HEIGHT));
					}
				}
				if ($errors && $errorsExt) {
					$res = getSuccessRes(t('E_UploadNotAll') . ' ' . t('E_FileExtensionForbidden'));
				} elseif ($errorsExt) {
					$res = getSuccessRes(t('E_FileExtensionForbidden'));
				} elseif ($errors) {
					$res = getSuccessRes(t('E_UploadNotAll'));
				} else {
					$res = getSuccessRes();
				}

			} else {
				$res = getErrorRes(t('E_UploadNoFiles'));
			}

		} else {
			$res = getErrorRes(t('E_UploadInvalidPath'));
		}

		if ($isAjax) {
			if ($errors || $errorsExt) {
				$res = getErrorRes(t('E_UploadNotAll'));
			}

			echo $res;
		} else {
			echo '
			<script>
				parent.fileUploaded(' . $res . ');
			</script>';
		}
	}

	function download() {
		verifyAction('DOWNLOAD');
		checkAccess('DOWNLOAD');

		$path = trim($_GET['f']);
		verifyPath($path);

		if (is_file(fixPath($path))) {
			$file = urldecode(basename($path));
			header('Content-Disposition: attachment; filename="' . $file . '"');
			header('Content-Type: application/force-download');
			readfile(fixPath($path));
		}
	}

	function downloaddir() {
		@ini_set('memory_limit', -1);
		verifyAction('DOWNLOADDIR');
		checkAccess('DOWNLOADDIR');

		$path = trim($_GET['d']);
		verifyPath($path);
		$path = fixPath($path);

		if (!class_exists('ZipArchive')) {
			echo '<script>alert("Cannot create zip archive - ZipArchive class is missing. Check your PHP version and configuration");</script>';
		} else {
			try {
				$filename = basename($path);
				$zipFile = $filename . '.zip';
				$zipPath = BASE_PATH . '/tmp/' . $zipFile;
				RoxyFile::ZipDir($path, $zipPath);

				header('Content-Disposition: attachment; filename="' . $zipFile . '"');
				header('Content-Type: application/force-download');
				readfile($zipPath);
				function deleteTmp($zipPath) {
					@unlink($zipPath);
				}
				register_shutdown_function('deleteTmp', $zipPath);
			} catch (Exception $ex) {
				echo '<script>alert("' . addslashes(t('E_CreateArchive')) . '");</script>';
			}
		}
	}

	function deletefile() {
		verifyAction('DELETEFILE');
		checkAccess('DELETEFILE');

		$path = trim($_POST['f']);
		verifyPath($path);

		if (is_file(fixPath($path))) {
			if (unlink(fixPath($path))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_DeletеFile') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_DeleteFileInvalidPath'));
		}

	}

	function movefile() {
		verifyAction('MOVEFILE');
		checkAccess('MOVEFILE');

		$path = trim(empty($_POST['f']) ? '' : $_POST['f']);
		$newPath = trim(empty($_POST['n']) ? '' : $_POST['n']);
		if (!$newPath) {
			$newPath = getFilesPath();
		}

		verifyPath($path);
		verifyPath($newPath);

		if (is_file(fixPath($path))) {
			if (file_exists(fixPath($newPath))) {
				echo getErrorRes(t('E_MoveFileAlreadyExists') . ' ' . basename($newPath));
			} elseif (rename(fixPath($path), fixPath($newPath))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_MoveFile') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_MoveFileInvalisPath'));
		}

	}

	function copyfile() {
		verifyAction('COPYFILE');
		checkAccess('COPYFILE');

		$path = trim(empty($_POST['f']) ? '' : $_POST['f']);
		$newPath = trim(empty($_POST['n']) ? '' : $_POST['n']);
		if (!$newPath) {
			$newPath = getFilesPath();
		}

		verifyPath($path);
		verifyPath($newPath);

		if (is_file(fixPath($path))) {
			$newPath = $newPath . '/' . RoxyFile::MakeUniqueFilename(fixPath($newPath), basename($path));
			if (copy(fixPath($path), fixPath($newPath))) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_CopyFile'));
			}

		} else {
			echo getErrorRes(t('E_CopyFileInvalisPath'));
		}

	}

	function renamefile() {
		verifyAction('RENAMEFILE');
		checkAccess('RENAMEFILE');

		$path = trim(empty($_POST['f']) ? '' : $_POST['f']);
		$name = trim(empty($_POST['n']) ? '' : $_POST['n']);
		verifyPath($path);

		if (is_file(fixPath($path))) {
			if (!RoxyFile::CanUploadFile($name)) {
				echo getErrorRes(t('E_FileExtensionForbidden') . ' ".' . RoxyFile::GetExtension($name) . '"');
			} elseif (rename(fixPath($path), dirname(fixPath($path)) . '/' . $name)) {
				echo getSuccessRes();
			} else {
				echo getErrorRes(t('E_RenameFile') . ' ' . basename($path));
			}

		} else {
			echo getErrorRes(t('E_RenameFileInvalidPath'));
		}

	}

	function thumb() {
		header("Pragma: cache");
		header("Cache-Control: max-age=3600");

		verifyAction('GENERATETHUMB');
		checkAccess('GENERATETHUMB');

		$path = urldecode(empty($_GET['f']) ? '' : $_GET['f']);
		verifyPath($path);

		@chmod(fixPath(dirname($path)), octdec(DIRPERMISSIONS));
		@chmod(fixPath($path), octdec(FILEPERMISSIONS));

		$w = intval(empty($_GET['width']) ? '100' : $_GET['width']);
		$h = intval(empty($_GET['height']) ? '0' : $_GET['height']);

		header('Content-type: ' . RoxyFile::GetMIMEType(basename($path)));
		if ($w && $h) {
			RoxyImage::CropCenter(fixPath($path), null, $w, $h);
		} else {
			RoxyImage::Resize(fixPath($path), null, $w, $h);
		}

	}

	function copydir() {
		verifyAction('COPYDIR');
		checkAccess('COPYDIR');

		$path = trim(empty($_POST['d']) ? '' : $_POST['d']);
		$newPath = trim(empty($_POST['n']) ? '' : $_POST['n']);
		verifyPath($path);
		verifyPath($newPath);

		function copyDir($path, $newPath) {
			$items = listDirectory($path);
			if (!is_dir($newPath)) {
				mkdir($newPath, octdec(DIRPERMISSIONS));
			}

			foreach ($items as $item) {
				if ($item == '.' || $item == '..') {
					continue;
				}

				$oldPath = RoxyFile::FixPath($path . '/' . $item);
				$tmpNewPath = RoxyFile::FixPath($newPath . '/' . $item);
				if (is_file($oldPath)) {
					copy($oldPath, $tmpNewPath);
				} elseif (is_dir($oldPath)) {
					copyDir($oldPath, $tmpNewPath);
				}
			}
		}

		if (is_dir(fixPath($path))) {
			copyDir(fixPath($path . '/'), fixPath($newPath . '/' . basename($path)));
			echo getSuccessRes();
		} else {
			echo getErrorRes(t('E_CopyDirInvalidPath'));
		}

	}
}