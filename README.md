# Pure-PHP language tag library, conforming to [BCP-47][1]

## Usage

    use Bcp47\LanguageTag;
    $canonicalForm = LanguageTag::fromRaw('zh-classical')->getCanonical();
    print $canonicalForm;
    # 'lzh'

    $availablePageLanguages = array('zh', 'lzh');
    $browserAcceptedLanguages = array('en', 'zh-classical', 'fr');
    $renderLanguage = LanguageTag::lookupBestLang($browserAcceptedLanguages, $availablePageLanguages);
    print $renderLanguage;
    # 'lzh'


  [1]: http://tools.ietf.org/search/bcp47
