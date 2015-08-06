<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 08 Apr 2014 15:13:43 GMT
 */

if ( ! defined( 'NV_IS_MOD_NVFORM' ) ) die( 'Stop!!!' );

/**
 * nv_theme_nvform_main()
 *
 * @param mixed $form_info
 * @param mixed $question
 * @return
 */
function nv_theme_nvform_main ( $form_info, $question_info, $answer_info, $info )
{
    global $global_config, $module_name, $module_file, $lang_module, $module_config, $module_info, $op, $my_head;

	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/jquery/jquery.validate.min.js\"></script>\n";
	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/language/jquery.validator-" . NV_LANG_INTERFACE . ".js\"></script>\n";

	$my_head .= "<script type=\"text/javascript\">\n";
	$my_head .= "$(document).ready(function(){
					$('#question').validate({
					});
				 });";
	$my_head .= " </script>\n";

	if( ! empty( $form_info['end_time'] ) )
	{
		$form_info['close_info'] = sprintf( $lang_module['form_close_info'], date( 'd/m/Y H:i' ) );
	}

    $xtpl = new XTemplate( $op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
    $xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'FORM', $form_info );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

	if( $form_info['question_display'] == 'question_display_left' )
	{
		$xtpl->assign( 'FORM_LEFT', 'class="form-horizontal"' );
	}

	foreach( $question_info as $row )
	{
		// Giá trị mặc định
		$row['value'] = isset( $answer_info[$row['qid']] ) ? $answer_info[$row['qid']] : $row['default_value'];
		$row['required'] = ( $row['required'] ) ? 'required' : '';
		$xtpl->assign( 'QUESTION', $row );

		if( $row['required'] )
		{
			$xtpl->parse( 'main.loop.required' );
		}
		if( $row['question_type'] == 'textbox' or $row['question_type'] == 'number' )
		{
			if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
			{
				$row['readonly'] = 'readonly="readonly"';
			}
			$xtpl->assign( 'QUESTION', $row );
			$xtpl->parse( 'main.loop.textbox' );
		}
		elseif( $row['question_type'] == 'date' )
		{
			$row['value'] = ( empty( $row['value'] ) ) ? '' : date( 'd/m/Y', $row['value'] );
			$row['datepicker'] = ( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) ) ? '' : 'datepicker';
			$xtpl->assign( 'QUESTION', $row );
			$xtpl->parse( 'main.loop.date' );
		}
		elseif( $row['question_type'] == 'time' )
		{
			$row['question_choices'] = unserialize( $row['question_choices'] );
			$row['value'] = $row['question_choices']['current_time'] ? NV_CURRENTTIME : $row['value'];
			$row['value'] = ( empty( $row['value'] ) ) ? '' : date( 'H:i', $row['value'] );
			$xtpl->assign( 'QUESTION', $row );
			$xtpl->parse( 'main.loop.time' );
		}
		elseif( $row['question_type'] == 'textarea' )
		{
			if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
			{
				$row['readonly'] = 'readonly';
			}
			$row['value'] = nv_htmlspecialchars( nv_br2nl( $row['value'] ) );
			$xtpl->assign( 'QUESTION', $row );
			$xtpl->parse( 'main.loop.textarea' );
		}
		elseif( $row['question_type'] == 'editor' )
		{
			if( defined( 'NV_EDITOR' ) )
			{
				require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
			}
			elseif( ! nv_function_exists( 'nv_aleditor' ) and file_exists( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/ckeditor/ckeditor.js' ) )
			{
				define( 'NV_EDITOR', true );
				define( 'NV_IS_CKEDITOR', true );
				$my_head .= '<script type="text/javascript" src="' . NV_BASE_SITEURL . NV_EDITORSDIR . '/ckeditor/ckeditor.js"></script>';

				function nv_aleditor( $textareaname, $width = '100%', $height = '450px', $val = '', $customtoolbar = '' )
				{
					$return = '<textarea style="width: ' . $width . '; height:' . $height . ';" id="' . $module_data . '_' . $textareaname . '" name="' . $textareaname . '">' . $val . '</textarea>';
					$return .= "<script type=\"text/javascript\">
					CKEDITOR.replace( '" . $module_data . "_" . $textareaname . "', {" . ( ! empty( $customtoolbar ) ? 'toolbar : "' . $customtoolbar . '",' : '' ) . " width: '" . $width . "',height: '" . $height . "',});
					</script>";
					return $return;
				}
			}

			if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
			{
				$row['question_choices'] = unserialize( $row['question_choices'] );
				$row['value'] = nv_htmlspecialchars( nv_editor_br2nl( $row['value'] ) );

				$edits = nv_aleditor( 'question[' . $row['qid'] . ']', '100%', '350px' , $row['value'], !$row['question_choices']['editor_mode'] ? 'Basic' : '' );
				$xtpl->assign( 'EDITOR', $edits );
				$xtpl->parse( 'main.loop.editor' );
			}
			else
			{
				$row['value'] = nv_htmlspecialchars( nv_br2nl( $row['value'] ) );
				$row['class'] = '';
				$xtpl->assign( 'QUESTION', $row );
				$xtpl->parse( 'main.loop.textarea' );
			}
		}
		elseif( $row['question_type'] == 'select' )
		{
			$row['question_choices'] = unserialize( $row['question_choices'] );
			foreach( $row['question_choices'] as $key => $value )
			{
				$xtpl->assign( 'QUESTION_CHOICES', array(
					'key' => $key,
					'selected' => ( $key == $row['value'] ) ? ' selected="selected"' : '',
					"value" => $value
				) );
				$xtpl->parse( 'main.loop.select.loop' );
			}

			if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
			{
				$row['readonly'] = 'readonly="readonly"';
			}
			$xtpl->assign( 'QUESTION', $row );

			$xtpl->parse( 'main.loop.select' );
		}
		elseif( $row['question_type'] == 'radio' )
		{
			$number = 0;
			$row['question_choices'] = unserialize( $row['question_choices'] );
			foreach( $row['question_choices'] as $key => $value )
			{
				$row['readonly'] = '';
				if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
				{
					$row['readonly'] = 'onclick="return false;"';
				}
				$xtpl->assign( 'QUESTION_CHOICES', array(
					'id' => $row['qid'] . '_' . $number++,
					'key' => $key,
					'checked' => ( $key == $row['value'] ) ? ' checked="checked"' : '',
					'readonly' => $row['readonly'],
					"value" => $value
				) );
				$xtpl->parse( 'main.loop.radio' );
			}
		}
		elseif( $row['question_type'] == 'checkbox' )
		{
			$row['readonly'] = '';
			if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
			{
				$row['readonly'] = 'onclick="return false;"';
			}

			$number = 0;
			$row['question_choices'] = unserialize( $row['question_choices'] );
			$valuecheckbox = ( ! empty( $row['value'] ) ) ? explode( ',', $row['value'] ) : array();
			foreach( $row['question_choices'] as $key => $value )
			{
				$xtpl->assign( 'QUESTION_CHOICES', array(
					'id' => $row['qid'] . '_' . $number++,
					'key' => $key,
					'checked' => ( in_array( $key, $valuecheckbox ) ) ? ' checked="checked"' : '',
					'readonly' => $row['readonly'],
					"value" => $value
				) );
				$xtpl->parse( 'main.loop.checkbox' );
			}
		}
		elseif( $row['question_type'] == 'multiselect' )
		{
			$valueselect = ( ! empty( $row['value'] ) ) ? explode( ',', $row['value'] ) : array();
			$row['question_choices'] = unserialize( $row['question_choices'] );
			foreach( $row['question_choices'] as $key => $value )
			{
				$xtpl->assign( 'QUESTION_CHOICES', array(
					'key' => $key,
					'selected' => ( in_array( $key, $valueselect ) ) ? ' selected="selected"' : '',
					"value" => $value
				) );
				$xtpl->parse( 'main.loop.multiselect.loop' );
			}

			if( $answer_info and ! $row['user_editable'] and isset( $form_info['filled'] ) )
			{
				$row['readonly'] = 'readonly="readonly"';
			}

			$xtpl->assign( 'QUESTION', $row );

			$xtpl->parse( 'main.loop.multiselect' );
		}
		elseif( $row['question_type'] == 'grid' )
		{
			$question_choices = unserialize( $row['question_choices'] );

			// Loop collumn
			if( !empty( $question_choices['col'] ) )
			{
				foreach( $question_choices['col'] as $choices )
				{
					$xtpl->assign( 'COL', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );
					$xtpl->parse( 'main.loop.grid.col' );
				}
			}

			// Loop row
			if( !empty( $question_choices['row'] ) )
			{
				foreach( $question_choices['row'] as $choices )
				{
					$xtpl->assign( 'ROW', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );

					if( !empty( $question_choices['col'] ) )
					{
						foreach( $question_choices['col'] as $col )
						{
							$value = $col['key'] . '||' . $choices['key'];
							$ck = $row['value'] == $value ? 'checked' : '';
							$xtpl->assign( 'GRID', array( 'value' => $value, 'checked' => $ck ) );
							$xtpl->parse( 'main.loop.grid.row.td' );
						}
					}

					$xtpl->parse( 'main.loop.grid.row' );
				}
			}

			$xtpl->parse( 'main.loop.grid' );
		}
		elseif( $row['question_type'] == 'table' )
		{
			$question_choices = unserialize( $row['question_choices'] );
			$row['value'] = isset( $answer_info[$row['qid']] ) ? $answer_info[$row['qid']] : '';

			// Loop collumn
			if( !empty( $question_choices['col'] ) )
			{
				foreach( $question_choices['col'] as $choices )
				{
					$xtpl->assign( 'COL', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );
					$xtpl->parse( 'main.loop.table.col' );
				}
			}

			// Loop row
			if( !empty( $question_choices['row'] ) )
			{
				foreach( $question_choices['row'] as $choices )
				{
					$xtpl->assign( 'ROW', array( 'key' => $choices['key'], 'value' => $choices['value'] ) );

					if( !empty( $question_choices['col'] ) )
					{
						foreach( $question_choices['col'] as $col )
						{
							$xtpl->assign( 'NAME', array( 'col' => $col['key'], 'row' => $choices['key'] ) );
							$xtpl->assign( 'VALUE', isset( $row['value'][$col['key']][$choices['key']] ) ? $row['value'][$col['key']][$choices['key']] : '' );
							$xtpl->parse( 'main.loop.table.row.td' );
						}
					}

					$xtpl->parse( 'main.loop.table.row' );
				}
			}

			$xtpl->parse( 'main.loop.table' );
		}
		elseif( $row['question_type'] == 'file' )
		{
			$row['value'] = str_replace( 'form_' . $row['qid'] . '/', '', $row['value'] );
			$row['question_choices'] = unserialize( $row['question_choices'] );
			$row['file_type'] = str_replace( ',', ', ', $row['question_choices']['type'] );
			$xtpl->assign( 'QUESTION', $row );

			$xtpl->parse( 'main.loop.file' );
			$xtpl->parse( 'main.enctype' );
		}

		if( $form_info['question_display'] == 'question_display_left' )
		{
			$xtpl->assign( 'LEFT', array( 'label' => 'class="col-sm-6 control-label"', 'div' => 'class="col-sm-18"' ) );
		}

		$xtpl->parse( 'main.loop' );
	}

	if( !empty( $info ) )
	{
		$xtpl->assign( 'INFO', $info );
		$xtpl->parse( 'main.info' );
	}

    $xtpl->parse( 'main' );
    return $xtpl->text( 'main' );
}

/**
 * nv_theme_nvform_alert()
 *
 * @param mixed $message
 * @param mixed $type
 * @return
 */
function nv_theme_nvform_alert( $message_title, $message_content, $type = 'info', $link_back = '', $time_back = 0 )
{
    global $module_file, $module_info, $page_title;

    $xtpl = new XTemplate( 'info.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );

	if( $type == 'success' )
	{
		$class = 'class="alert alert-success"';
	}
	elseif( $type == 'warning' )
	{
		$class = 'class="alert alert-warning"';
	}
	elseif( $type == 'danger' )
	{
		$class = 'class="alert alert-danger"';
	}
	else
	{
		$class = 'class="alert alert-info"';
	}

	if( ! empty( $message_title ) )
	{
		$page_title = $message_title;
		$xtpl->assign( 'TITLE', $message_title );
		$xtpl->parse( 'main.title' );
	}
	else
	{
		$page_title = $module_info['custom_title'];
	}
	$xtpl->assign( 'CONTENT', $message_content );
	$xtpl->assign( 'CLASS', $class );
    $xtpl->parse( 'main' );
    $contents = $xtpl->text( 'main' );

	include ( NV_ROOTDIR . "/includes/header.php" );
	echo nv_site_theme( $contents );
	include ( NV_ROOTDIR . "/includes/footer.php" );
	exit();
}