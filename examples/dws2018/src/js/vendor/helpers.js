

export var TextHelper = {
    nl2br(str, is_xhtml) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },
    br2nl(str){
        var regex = /<br\s*[\/]?>/gi;
        return str.replace(regex, "\n");
    },
    stripbr(str){
        var regex = /<br\s*[\/]?>/gi;
        return str.replace(regex, " ");
    }
}

