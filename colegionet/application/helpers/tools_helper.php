<?php 
if (! function_exists('ordenarArr')) 
{
	function ordenarArr(array $arr,$orderBy)
	{
		$aux = array();
		foreach ($arr as $key => $value) {
			$aux[$key] = ucwords($value[$orderBy]);
		}
		array_multisort($aux,SORT_ASC,$arr);
		return $arr;
	}
}

?>