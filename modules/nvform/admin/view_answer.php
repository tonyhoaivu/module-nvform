<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 24-06-2011 10:35
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$id = $nv_Request->get_int( 'id', 'get', 0 );

$answer_data = $db->query( 'SELECT t1.*, t2.username, t2.last_name, t2.first_name FROM ' . NV_PREFIXLANG . '_' . $module_data . '_answer t1 LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' t2 ON t1.who_answer = t2.userid WHERE t1.id = ' . $id )->fetch();
if( empty( $answer_data ) )
{
	nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] );
}

$form_info = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $answer_data['fid'] )->fetch();
$question_data = $db->query( 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_question WHERE fid=' . $answer_data['fid'] . ' AND status=1 ORDER BY weight' )->fetchAll();

$xtpl = new XTemplate( $op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'FORM_INFO', $form_info );

if( !empty( $question_data ) )
{
	foreach( $question_data as $data )
	{
		$qid = $data['qid'];
		$data['title'] = nv_get_plaintext( $data['title'] );
		$xtpl->assign( 'QUESTION', $data );

		$answer = unserialize( $answer_data['answer'] );
		if( isset( $answer[$qid] ) and $data['report'] )
		{
			$ans = $answer[$qid];
			$question_type = $data['question_type'];

			if( $question_type == 'plaintext' ) continue;

			if( $question_type == 'multiselect' OR $question_type == 'select' OR $question_type == 'radio' OR $question_type == 'checkbox' )
			{
				$data = unserialize( $data['question_choices'] );
				if( $question_type == 'checkbox' )
				{
					$result = explode( ',', $ans );
					foreach( $result as $key )
					{
						$answer_result .= $data[$key] . "<br />";
					}
				}
				else
				{
					$answer_result = $data[$ans];
				}
			}
			elseif( $question_type == 'date' and !empty( $ans ) )
			{
				$answer_result = nv_date( 'd/m/Y', $ans );
			}
			elseif( $question_type == 'time' and !empty( $ans ) )
			{
				$answer_result = nv_date( 'H:i', $ans );
			}
			else
			{
				$answer_result = $ans;
			}

			$answer['username'] = empty( $answer['username'] ) ? $lang_module['report_guest'] : nv_show_name_user( $answer['first_name'], $answer['last_name'], $answer['username'] );

			$xtpl->assign( 'ANSWER', $answer_result );

			if( $question_type == 'table' )
			{
				$data = unserialize( $data['question_choices'] );

				// Loop collumn
				if( !empty( $data['col'] ) )
				{
					foreach( $data['col'] as $choices )
					{
						$xtpl->assign( 'COL', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );
						$xtpl->parse( 'main.question.answer.table.col' );
					}
				}

				// Loop row
				if( !empty( $data['row'] ) )
				{
					foreach( $data['row'] as $choices )
					{
						$xtpl->assign( 'ROW', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );

						if( !empty( $data['col'] ) )
						{
							foreach( $data['col'] as $col )
							{
								$xtpl->assign( 'NAME', array( 'col' => $col['key'], 'row' => $choices['key'] ) );
								$xtpl->assign( 'VALUE', isset( $answer[$qid][$col['key']][$choices['key']] ) ? $answer[$qid][$col['key']][$choices['key']] : '' );
								$xtpl->parse( 'main.question.answer.table.row.td' );
							}
						}

						$xtpl->parse( 'main.question.answer.table.row' );
					}
				}
				$xtpl->parse( 'main.question.answer.table' );
			}
			elseif( $question_type == 'grid' )
			{
				$data = unserialize( $data['question_choices'] );

				// Loop collumn
				if( !empty( $data['col'] ) )
				{
					foreach( $data['col'] as $choices )
					{
						$xtpl->assign( 'COL', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );
						$xtpl->parse( 'main.question.answer.grid.col' );
					}
				}

				// Loop row
				if( !empty( $data['row'] ) )
				{
					foreach( $data['row'] as $choices )
					{
						$xtpl->assign( 'ROW', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );

						if( !empty( $data['col'] ) )
						{
							foreach( $data['col'] as $col )
							{
								$value = $col['key'] . '||' . $choices['key'];
								if( $answer[$qid] == $value )
								{
									$xtpl->parse( 'main.question.answer.grid.row.td.check' );
								}
								else
								{
									$xtpl->parse( 'main.question.answer.grid.row.td.no_check' );
								}
								$xtpl->parse( 'main.question.answer.grid.row.td' );
							}
						}

						$xtpl->parse( 'main.question.answer.grid.row' );
					}
				}

				$xtpl->parse( 'main.question.answer.grid' );
			}
			else
			{
				$xtpl->parse( 'main.question.answer.other' );
			}

			$xtpl->parse( 'main.question.answer' );
		}

		$answer['answer_time'] = nv_date( 'd/m/Y H:i', $answer['answer_time'] );
		$answer['answer_edit_time'] = ! $answer['answer_edit_time'] ? '-' : nv_date( 'd/m/Y H:i', $answer['answer_edit_time'] );
		$xtpl->assign( 'ANSWER', $answer );

		$xtpl->parse( 'main.question' );
	}
}

$xtpl->assign( 'FID', $fid );
$xtpl->assign( 'COUNT', sprintf( $lang_module['report_count'], count( $answer_data ) ) );

unset( $answer_data, $question_data );
$page_title = sprintf( $lang_module['report_page_title'], $form_info['title'] );

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents, false );
include NV_ROOTDIR . '/includes/footer.php';