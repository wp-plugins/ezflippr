(function($){
	$(document).ready(function(){
		$("input[name='ezflippr-field-havekey']").change(function(){
			var showEmail   = $('#ezflippr-field-havekey-no:checked,#ezflippr-field-havekey-forgot:checked').length > 0;
			var showKey     = $('#ezflippr-field-havekey-yes:checked').length > 0;
			if(showEmail){
				$("#tr-email").fadeIn();
			}else{
				$("#tr-email").stop(true,true).hide();
			}

			if(showKey){
				$("#tr-key").fadeIn();
			}else{
				$("#tr-key").stop(true,true).hide();
			}
		});

		$("#tr-key").hide();


		$('.ez-btn-install,.ez-btn-reinstall,.ez-btn-uninstall').click(function(ev) {
			ev.preventDefault();

			var $row = $(this).parents('tr');
			var $status = $row.find('.column-status');

			$row.find('.ez-btn-install,.ez-btn-reinstall,.ez-btn-uninstall').css('color','#999').click(function(ev){ ev.preventDefault(); });

			$.ajax({
				url:$(this).attr('href')+'&json=1',
				success:function(data){
					document.location.reload();
				},
				error:function(a,b,c){
					alert(b);
				}
			});

			var text = $(this).hasClass('ez-btn-install') ? ez_str_installing :
				$(this).hasClass('ez-btn-reinstall') ? ez_str_reinstalling :
				$(this).hasClass('ez-btn-uninstall') ? ez_str_uninstalling :
					ez_str_please_wait;

			$status.text(text);
		});

	});
})(jQuery);
