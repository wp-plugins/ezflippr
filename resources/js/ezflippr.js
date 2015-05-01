jQuery(document).ready(function(){
    jQuery("input[name='ezflippr-field-havekey']").change(function(){
        var showEmail   = jQuery('#ezflippr-field-havekey-no:checked,#ezflippr-field-havekey-forgot:checked').length > 0;
        var showKey     = jQuery('#ezflippr-field-havekey-yes:checked').length > 0;
        if(showEmail){
            jQuery("#tr-email").fadeIn();
        }else{
            jQuery("#tr-email").stop(true,true).hide();
        }

        if(showKey){
            jQuery("#tr-key").fadeIn();
        }else{
            jQuery("#tr-key").stop(true,true).hide();
        }
    });

    jQuery("#tr-key").hide();
});
