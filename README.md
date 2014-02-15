# Pure-PHP language tag library, conforming to [BCP-47][1]

## Usage

    use Bcp47\Bcp47;
    $canonicalForm = (new Bcp47)->canonicalize('zh-classical');
    print $canonicalForm;
    # 'lzh'

    $availablePageLanguages = array('zh', 'lzh');
    $browserAcceptedLanguages = array('en', 'zh-classical', 'fr');
    $renderLanguage = (new Bcp47)->lookupBestLang($browserAcceptedLanguages, $availablePageLanguages);
    print $renderLanguage;
    # 'lzh'


  [1]: http://tools.ietf.org/search/bcp47
