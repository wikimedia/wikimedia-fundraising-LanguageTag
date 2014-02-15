<?php
namespace Bcp47;

/**
 * Ported from http://www.mediawiki.org/w/index.php?title=Template:BCP47&oldid=891982
 */
class MediaWikiAnomalies {
    static $anomalies = array(
        /* pseudocodes */
        'default' => 'und',

        /* current BCP47 violations by Wikimedia sites, which can be fixed using standard tags when they exist */
        'als' => 'gsw',
        'bat-smg' => 'sgs',
        'fiu-vro' => 'vro',
        'roa-rup' => 'rup',
        'simple' => 'en<!-- could be "en-x-simple" but actually a subset within standard "en" for HTML -->',
        'sr-sc' => 'sr-cyrl',
        'sr-sl' => 'sr-latn',
        'zh-classical' => 'lzh',

        /* other current BCP47 violations by Wikimedia sites, fixed using private-use extensions (if they are needed, labels are limited to 8 letters/digits) */
        'cbk-zam' => 'cbk-x-zam',
        'de-formal' => 'de', /* could be "de-x-formal", but actually a subset within standard "de" for HTML/XML */
        'eml' => 'it-x-eml', /* retired code, two competing standard codes for these Emilian variants of Italian */
        'map-bms' => 'map-x-bms',
        'mo' => 'ro-cyrl', /* retired, best fit on Wikimedia sites, but no longer working in interwikis (Wikipedia project locked down) */
        'nl-informal' => 'nl', /* could be "nl-x-informal", but actually a subset within standard "nl" for HTML/XML */
        'nrm' => 'fr-x-nrm', /* could be roa-x-nrm using a family subtag, but a "private-use" extension of French is still much better for language/script fallbacks */
        'roa-tara' => 'it-x-tara',

        /* conforming BCP47 "private-use" extensions used by Wikimedia, which are no longer needed, and improved using now standard codes */
        'be-x-old' => 'be-tarask',

        /* conforming but ambiguous BCP47 codes used by Wikimedia in a more restrictive way, with more precision */
        'no' => 'nb', /* "no" means Bokmål on Wikimedia sites, "nb" is not used */
        'bh' => 'bho', /* "bh"="bih" is a language family, interpreted in Wikimedia as the single language "bho", even if its interwiki code remains bh) */
        'tgl' => 'tl-tglg', /* "tgl" on Wikimedia is the historic variant of the Tagalog macrolanguage ("tl" or "tgl", "tl" recommended for BCP47), written in the Baybayin script ("tglg") */

        /* conforming BCP47 "inherited" tags, strongly discouraged and replaced by their recommended tags (complete list that should not be augmented now) */
        'art-lojban' => 'jbo', /* still used in some old Wikimedia templates */
        'en-gb-oed' => 'en-gb', /* no preferred replacement, could be "en-gb-x-oed" but actually a subset within standard "en-gb" */
        'i-ami' => 'ami',
        'i-bnn' => 'bnn',
        'i-hak' => 'hak',
        'i-klingon' => 'tlh',
        'i-lux' => 'lb',
        'i-navajo' => 'nv',
        'i-pwn' => 'pwn',
        'i-tao' => 'tao',
        'i-tay' => 'tay',
        'i-tsu' => 'tsu',
        'no-bok' => 'nb', /* still used in some old Wikimedia templates */
        'no-nyn' => 'nn', /* still used in some old Wikimedia templates */
        'sgn-be-fr' => 'sfb',
        'sgn-be-nl' => 'vgt',
        'sgn-ch-de' => 'sgg',
        'zh-guoyu' => 'cmn', /* this could be an alias of "zh" on Wikimedia sites, which do not use "cmn" but assume "zh" is Mandarin */
        'zh-hakka' => 'hak',
        'zh-min' => 'zh-tw', /* no preferred replacement, could be "zh-x-min", but actually a subset within standard "zh-tw"; not necessarily "nan" */
        'zh-min-nan' => 'nan', /* used in some old Wikimedia templates and in interwikis */
        'zh-xiang' => 'hsn',

     /* conforming BCP47 "redundant" tags, discouraged and replaced by their recommended tags (complete list that should not be augmented now) */
        'sgn-br' => 'bzs',
        'sgn-co' => 'csn',
        'sgn-de' => 'gsg',
        'sgn-dk' => 'dsl',
        'sgn-es' => 'ssp',
        'sgn-fr' => 'fsl', /* still used in some old Wikimedia templates */
        'sgn-gb' => 'bfi',
        'sgn-gr' => 'gss',
        'sgn-ie' => 'isg',
        'sgn-it' => 'ise',
        'sgn-jp' => 'jsl',
        'sgn-mx' => 'mfs',
        'sgn-ni' => 'ncs',
        'sgn-nl' => 'dse',
        'sgn-no' => 'nsl',
        'sgn-pt' => 'psr',
        'sgn-se' => 'swl',
        'sgn-us' => 'ase', /* still used in some old Wikimedia templates */
        'sgn-za' => 'sfs',
        'zh-cmn' => 'cmn', /* still used in some old Wikimedia templates, this could be an alias of "zh" on Wikimedia sites, which do not use "cmn" but assume "zh" is Mandarin */
        'zh-cmn-Hans' => 'cmn-hans', /* still used in some old Wikimedia templates, this could be an alias of "zh-hans" on Wikimedia sites, which do not use "cmn" but assume "zh" is Mandarin */
        'zh-cmn-Hant' => 'cmn-hant', /* still used in some old Wikimedia templates, this could be an alias of "zh-hant" on Wikimedia sites, which do not use "cmn" but assume "zh" is Mandarin */
        'zh-gan' => 'gan', /* still used in some old Wikimedia templates */
        'zh-wuu' => 'wuu', /* still used in some old Wikimedia templates */
        'zh-yue' => 'yue', /* still used in some old Wikimedia templates and in interwikis */

        /* other "inherited" tags of the standard, strongly discouraged as they are deleted, but with no defined replacement there are left unaffected (complete list that should not be augmented now) */
        'cel-gaulish' => 'cel-x-gaulish?',
        'i-default' => 'und-x-default?', /* still used in some old Wikimedia templates and in interwikis */
        'i-enochian' => 'x-enochian?',
        'i-mingo' => 'x-mingo?',
    );

    static function preprocess(&$raw) {
        if (array_key_exists($raw, MediaWikiAnomalies::$anomalies)) {
            $raw = MediaWikiAnomalies::$anomalies[$raw];
        }
    }
}
