<!-- BEGIN: main -->
<div style="margin: 5px 0 10px 0; display: block;">
	<a href="{ADD_QUESTION}" class="button button-h">{LANG.question_add}</a>
</div>
<table class="tab1">
	<colgroup>
		<col class="w50">
		<col span="1">
		<col span="2" class="w150">
	</colgroup>
	<thead>
		<tr class="center">
			<td>{LANG.order}</td>
			<td>{LANG.question_content}</td>
			<td>{LANG.question_type}</td>
			<td>{LANG.status}</td>
			<td width="120">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<!-- BEGIN: row -->
		<tr>
			<td class="center">
				<select id="change_weight_{ROW.qid}" onchange="nv_chang_weight('{ROW.qid}', '{ROW.fid}', 'question');">
					<!-- BEGIN: weight -->
					<option value="{WEIGHT.w}"{WEIGHT.selected}>{WEIGHT.w}</option>
					<!-- END: weight -->
				</select>
			</td>
			<td><a href="{ROW.url_view}" title="{ROW.title}" target="_blank">{ROW.title}</a></td>
			<td>{FIELD_TYPE_TEXT}</td>
			<td class="center">
				<select id="change_status_{ROW.qid}" onchange="nv_chang_status('{ROW.qid}', 'question');">
					<!-- BEGIN: status -->
					<option value="{STATUS.key}"{STATUS.selected}>{STATUS.val}</option>
					<!-- END: status -->
				</select>
			</td>
			<td class="center">
				<em class="icon-edit icon-large">&nbsp;</em> <a href="{ROW.url_edit}">{GLANG.edit}</a> &nbsp;
				<em class="icon-trash icon-large">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_question({ROW.qid});">{GLANG.delete}</a>
			</td>
		</tr>
		<!-- END: row -->
	</tbody>
</table>
<!-- END: main -->