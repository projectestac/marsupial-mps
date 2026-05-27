<?php
/// Define one exclusive separator that we'll use in the temp saved tags
/// keys. It must be something rare enough to avoid having matches with
/// filterobjects. MDL-18165
define ('EXCL_SEPARATOR', '-%-');

/**
 * This is just a little object to define a phrase and some instructions 
 * for how to process it.  Filters can create an array of these to pass 
 * to the filter_phrases function below.
 **/
class filterobject {
    var $phrase;
    var $hreftagbegin;
    var $hreftagend;
    var $casesensitive;
    var $fullmatch;
    var $replacementphrase;
    var $work_phrase;
    var $work_hreftagbegin;
    var $work_hreftagend;
    var $work_casesensitive;
    var $work_fullmatch;
    var $work_replacementphrase;
    var $work_calculated;

    /// a constructor just because I like constructing
    function __construct($phrase, $hreftagbegin='<span class="highlight">',
                         $hreftagend='</span>',
                         $casesensitive=false,
                         $fullmatch=false,
                         $replacementphrase=NULL) {

        $this->phrase           = $phrase;
        $this->hreftagbegin     = $hreftagbegin;
        $this->hreftagend       = $hreftagend;
        $this->casesensitive    = $casesensitive;
        $this->fullmatch        = $fullmatch;
        $this->replacementphrase= $replacementphrase;
        $this->work_calculated  = false;

    }
}
