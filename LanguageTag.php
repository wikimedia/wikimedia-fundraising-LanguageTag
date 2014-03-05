<?php
namespace Bcp47;

use \Goatherd_LanguageTag_Parser;
use \Locale;

class LanguageTag {
    const SPLIT_PRIORITY_LIST_REGEX = '/[, ]+/';

    protected $value;

    static function fromRaw($raw) {
        $tag = new LanguageTag();
        $tag->value = LanguageTag::canonicalize($raw);
        return $tag;
    }

    function __toString() {
        return $this->value;
    }

    /**
     * @return string the canonical code for this tag
     */
    function getCanonical() {
        return $this->value;
    }

    /**
     * @return boolean True if the tag is empty or wildcard
     */
    function isUnspecified() {
        return ($this->getCanonical() === '*'
            or $this->getCanonical() === '');
    }

    /**
     * Returns the language among those available that
     * best matches the preferred language list, using the lookup
     * algorithm of BCP 47: https://tools.ietf.org/html/rfc4647
     * If none of the available languages meets the user's preferences,
     * then "" is returned.
     * FIXME: Extended language ranges are not supported.
     *
     * @param LanguageTag preferredLanguages The list of preferred languages by
     * tag, in descending order of preference.
     *
     * @param LanguageTag availableLanguages The set of languages your
     * application supports in this context.
     *
     * @return LanguageTag The available language that best matches the
     * preferred language list, or "".
     *
     * This function adapted from http://alloyui.com/versions/1.0.x/api/intl-base.js.html
     */
    static function lookupBestLang(
        LanguageTag $preferredLanguages,
        LanguageTag $availableLanguages
    ) {

        foreach ($preferredLanguages->getLanguages() as $language) {
            // Skip meaningless bits such as "*"
            if ($language->isUnspecified()) {
                continue;
            }

            // Check the fallback sequence for one language
            while (!$language->isUnspecified()) {
                if ($availableLanguages->canSpeak($language)) {
                    return $language;
                }
                $language = LanguageTag::popSpecificPart($language);
            }
        }
        return LanguageTag::fromRaw('');
    }

    /**
     * @return boolean True if any of my tags appears among $alternatives
     */
    protected function canSpeak(LanguageTag $alternatives) {
        foreach ($alternatives->getLanguages() as $avail) {
            foreach ($this->getLanguages() as $mine) {
                if ((string)$avail === (string)$mine) {
                    return true;
                }
            }
        }
        return false;
    }

    static protected function popSpecificPart(LanguageTag $tag) {
        if (count($tag->getLanguages()) !== 1) {
            throw new Exception("Illegal popping of empty or plural language tag");
        }

        $parts = explode('-', $tag->getCanonical());
        if (count($parts)) {
            // Drop the most specific (rightmost) variation
            array_pop($parts);

            // Drop private-use subtags preceded by a single-character subtag
            if (count($parts) > 1
                and strlen($parts[0]) >= 2
                and strlen(reset(array_slice($parts, -2, 1))) == 1
            ) {
                $parts = array_slice($parts, 0, -2);
            }
        }
        return LanguageTag::fromRaw(implode('-', $parts));
    }

    /**
     * @param string $raw
     * @return array
     */
    static protected function parse($raw) {
        $parser = new Goatherd_LanguageTag_Parser();
        $parsed = $parser->parse($raw);

        return $parsed;
    }

    static protected function splitPriorityList($raw) {
        return preg_split(LanguageTag::SPLIT_PRIORITY_LIST_REGEX, $raw, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @return array of LanguageTag if this is a priority list, return all components
     *     as LanguageTag objects.  If a single tag, return that as a list.  If
     *     there are no languages, return an empty list.
     */
    function getLanguages() {
        $tagStrings = LanguageTag::splitPriorityList($this->value);
        $tags = array();
        foreach ($tagStrings as $str) {
            $tags[] = LanguageTag::fromRaw($str);
        }
        return $tags;
    }

    /**
     * Convert a language tag to the canonical form
     *
     * Note, you will lose language extension information.
     *
     * @param string $raw a single language tag or a language priority list
     * @return string
     */
    static protected function canonicalize($raw) {
        if (preg_match(LanguageTag::SPLIT_PRIORITY_LIST_REGEX, $raw)) {
            $tagList = LanguageTag::splitPriorityList($raw);
            $recursedlyCanonicalized = array();
            foreach ($tagList as $tag) {
                $recursedlyCanonicalized[] = LanguageTag::canonicalize($tag);
            }
            return implode(', ', $recursedlyCanonicalized);
        }

        Configuration::get()->runHook('preprocess', array(&$raw));

        $parsed = LanguageTag::parse($raw);

        $canonical = array();
        if (isset($parsed['language'])) {
            $canonical[] = strtolower($parsed['language']);
        }
        if (isset($parsed['script'])) {
            $canonical[] = ucfirst($parsed['script']);
        }
        if (isset($parsed['region'])) {
            $canonical[] = strtoupper($parsed['region']);
        }
        if (isset($parsed['variants'])) {
            foreach ($parsed['variants'] as $subtag => $variant) {
                if ($subtag === $variant or is_integer($subtag)) {
                    $canonical[] = strtolower($variant);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($variant);
                }
            }
        }
        if (isset($parsed['extension'])) {
            asort($parsed['extension']);
            foreach ($parsed['extension'] as $subtag => $extension) {
                if ($subtag === $extension or is_integer($subtag)) {
                    $canonical[] = strtolower($extension);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($extension);
                }
            }
        }
        if (isset($parsed['privateuse'])) {
            foreach ($parsed['privateuse'] as $subtag => $privateuse) {
                if ($subtag === $privateuse or is_integer($subtag)) {
                    $canonical[] = 'x';
                    $canonical[] = strtolower($privateuse);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($privateuse);
                }
            }
        }

        return implode('-', $canonical);
    }
}
