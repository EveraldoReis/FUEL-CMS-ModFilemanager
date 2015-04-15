<?php

class Fileman_custom_fields {

	function fileman($params) {

		$id = md5(rand() . microtime());

		if (!isset($params['filetype'])) {
			$params['filetype'] = null;
		}
		if (!isset($params['preview_width'])) {
			$params['preview_width'] = '120px';
		}

		$ext = strtolower(end(explode('.', $params['value'])));

		$output = '<script>
        function openCustomRoxy(){
          $(\'#roxyCustomPanel\').dialog({modal:true, width:875,height:600});
      }
      function closeCustomRoxy(){
          $(\'#roxyCustomPanel\').dialog(\'close\');
      }
  </script><div><input id="field' . $id . '" type="hidden" value="' . $params['value'] . '" name="' . $params['key'] . '" />';
		$output .= '<a href="javascript:openCustomRoxy()">';
		$output .= '<img  id="img' . $id . '" style="max-width:' . $params['preview_width'] . ';" src="' . (in_array($ext, array('png', 'gif', 'jpg', 'jpeg', 'svg')) ? $params['value'] : site_url('fuel/modules/fileman/views/assets/images/filetypes/big/file_extension_' . $ext . '.png')) . '" />';
		$output .= '</a>';
		$output .= '</div>
  <div id="roxyCustomPanel" style="display: none;">
      <iframe src="' . fuel_url('fileman/roxyfileman?type=' . $params['filetype'] . '&inline=1&integration=custom&field=field' . $id . '&img=img' . $id) . '" style="width:100%;height:100%" frameborder="0"></iframe>
  </div>';

		return $output;

	}

}
