import $ from 'jquery';
import _ from 'lodash';

function resizeFeatured(){
    $('.featured-posts-background-list.layout-middle').each(function(){
        
        var $bkg = $(this);
        var $img =  $('.featured-post-img-first');

        var dif = $bkg.innerHeight() + $bkg.offset().top - $img.offset().top - $img.innerHeight()/2;

        $bkg.height($bkg.height()-parseInt(dif));

        
    });

    $('.layout-modifier-simple-wrapper').each(function(){
       
        var $wrapper = $(this);
        var $bkg = $wrapper.find('.background-dark-blue.layout-modifier-simple');

        var height = $wrapper.innerHeight() - $wrapper.find('.featured-post-img-first').height()/2 - $wrapper.find('.featured-post-title.index-1').innerHeight() - parseInt($wrapper.find('.featured-post-title.index-1').css('margin-bottom'));
        $bkg.height(parseInt(height));
        
    });
}

$(window).on('resizing', function(){
    resizeFeatured();
});
