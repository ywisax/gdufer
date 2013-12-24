<?php
$view_file = '';
switch ($model->type)
{
	case 'Book':
		$view_file = 'Information.List.Book';
	default:
		throw new Information_Exception('Unknown information type');
}

include Kohana::find_file('View', $view_file);

