<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="quote">
	<blockquote class="error"><span>{ERROR}</span></blockquote>
</div>
<!-- END: error -->
<form action="{FORM_ACTION}" method="post" class="confirm-reload">
	<input name="save" type="hidden" value="1" />
	<table class="tab1">
		<colgroup>
			<col class="w200" />
			<col />
		</colgroup>
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{LANG.form_add}"/></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td class="right strong">{LANG.form_title}</td>
				<td><input class="w500" type="text" value="{DATA.title}" name="title" id="idtitle" maxlength="255" /></td>
			</tr>
			<tr>
				<td class="right strong">{LANG.form_alias}</td>
				<td><input class="w500" type="text" value="{DATA.alias}" name="alias" id="idalias" maxlength="255" /> <em class="icon-refresh icon-large icon-pointer" onclick="get_alias('{ID}');">&nbsp;</em></td>
			</tr>
			<tr>
				<td class="right strong">{LANG.form_image}</td>
				<td><input class="w500" type="text" name="image" id="image" value="{DATA.image}"/> <input type="button" value="Browse server" name="selectimg"/></td>
			</tr>
			<tr>
				<td class="right strong">{LANG.form_description} </td>
				<td ><textarea class="w500" id="description" name="description" cols="100" rows="5">{DATA.description}</textarea></td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript">
	$("input[name=selectimg]").click(function() {
		var area = "image";
		var alt = "imagealt";
		var path = "{UPLOADS_DIR_USER}";
		var type = "image";
		nv_open_browse_file(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&alt=" + alt + "&path=" + path + "&type=" + type, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});
</script>
<!-- BEGIN: get_alias -->
<script type="text/javascript">
	$(document).ready(function() {
		$('#idtitle').change(function() {
			get_alias('{ID}');
		});
	});
</script>
<!-- END: get_alias -->
<!-- END: main -->
