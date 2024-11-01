jQuery(document).on('change','.wpform-icon-select',function(){
    var v = jQuery(this).val();
    jQuery(this).closest('.wpforms-field-option-row').find('.toggle-unfoldable-cont').html('<i class="'+v+'"></i>');
});