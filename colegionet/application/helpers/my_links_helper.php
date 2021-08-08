<?php 
if (! function_exists('my_link')) {
	function my_link($text, $url = '', $title = '', $icon = '',$js = '',&$html = ''){
		
		if (is_array($text)) {
			foreach ($text as $key => $value) {
				my_link(
					isset($value['text']) ? $value['text'] : '',
					isset($value['url']) ? $value['url'] : '',
					isset($value['title']) ? $value['title'] : '',
					isset($value['icon']) ? $value['icon'] : '', 
					isset($value['js']) ? $value['js'] : '',
					$html
				);
			}
		} else {
			$html .= '<a style="padding: 2px; color:inherit;" title="'.$title.'" '.$js.'
						href="'.site_url($url).'">'.$text.'<span class="'.$icon.'"></span></a>';
		}
		return $html;
	}
}

?>