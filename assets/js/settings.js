$(document).ready(function () {

	$("button.genpwdbtn").click(function () {
		$('.genptext').show();
		$('.genptext,.n_pwd,.rtn_pwd').val(returnPassword());
	});

});