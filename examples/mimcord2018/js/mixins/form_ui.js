'use strict';

var $ = require('jquery');

var exports = {};

function checkNumberInput($input){
    if($input.val() == '') $input.val(0);
    if($input.val() <= 0 && !$input.hasClass('isZero')){
        $input.addClass('isZero');
        $input.trigger('isZero');
    } else if($input.val() > 0 && $input.hasClass('isZero')) {
        $input.removeClass('isZero');
        $input.trigger('notZero');
    }
    
}

global.setNumberInputs = function($wrapper){
    var $wrapper = (typeof $wrapper != 'undefined') ?  $wrapper : $(body);
    var $inputs = $wrapper.find('.qty-input');
    $inputs.each(function(){
        var $input_wrapper = $(this);
        var $input = $input_wrapper.find('input');
        if($input_wrapper.data('setNumberInputs')) return;
        var $up = $('<div class="qty-input-up"></div>');
        var $down = $('<div class="qty-input-down"></div>');

        $up.appendTo($input_wrapper);
        $down.appendTo($input_wrapper);

        $up.click(function(){
            $input.val(parseInt($input.val())+1);
            $input.trigger('change');
        });

        $down.click(function(){
            if($input.val() <= 0) return false; 
            $input.val(parseInt($input.val())-1);
            $input.trigger('change');
        });
        
        $input.on('change',function(){
            checkNumberInput($(this));
        });
        
        if($input_wrapper.hasClass('qty-input-avoid-enter')){
            $input.on('keypress keyup',function(e){
                e.stopPropagation();
                
                // Enter pressed
                if (e.keyCode == '13'){
                    var $input = $(this);
                    var $submit = $input.parents('.cross-sells-tr').find('.add_to_cart_button');
                    if($input.val() > 0){
                         $submit.click();
                    }
                    return false;
                }
            });
        };

        $input.on('click keyup mouseup',function(){
            $(this).trigger('change');
        });

        checkNumberInput($input);
        
        $input_wrapper.addClass('qty-input-ui').data('setNumberInputs',true);
    });

    
};

exports.setNumberInputs = global.setNumberInputs;

module.exports = exports;