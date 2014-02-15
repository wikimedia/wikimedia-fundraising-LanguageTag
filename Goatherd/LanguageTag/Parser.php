<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: Parser.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
 *
 * This file is part of Goatherd library.
 *
 * Goatherd library is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Goatherd library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Goatherd library. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The parser does check for well-formed language tags.
 * A match will be splitted, named and returned.
 *
 * Usage: (example taken from RFC 5646 documentation)
 *
 * $tag = new Goatherd_LanguageTag_Parser();
 * $details = $tag->parse('en-Latn-GB-boont-r-extended-sequence-x-private');
 *
 * @package LanguageTag
 */
class Goatherd_LanguageTag_Parser
{
    /**
     * Name of the latest RFC supported.
     *
     * @var string
     */
    const RFC_VERSION = "5646";

    /**
     *
     * @var Goatherd_LanguageTag_Parser_Validator_ValidatorInterface[]
     */
    protected $_validators = null;

    /**
     * List of error messages as returned by last validation.
     *
     * @var string[]]
     */
    protected $_error_messages = array();

    /**
     * Regular expression as defined by ABNF of the latest RFC (according to
     * class RFC_Version).
     *
     * Used to check for well-formed language tags and separate subtags as far
     * as possible. Recursive sub-tags are splitted afterwards (like variants,
     * extensions, privateuse and extlang).
     *
     * @var string  regular expression
     */
    protected $_production = "/^(?:(?P<grandfathered>          # non redundant tags registered during the RFC 3066 era
                                    (?P<irregular>          # do not match langtag-production but are considred well-formed
                                        en-GB-oed |
                                        (?:i-(?:ami | bnn | default | enochian | hak | klingon | lux | mingo))
                                    )|
                                    (?P<regular>            # match langtag-production, but subtags are not extlang or variant subtags
                                                            # they are all deprecated
                                        art-lojban | cel-gaulish |
                                        (?:no-(?:bok | nyn)) |
                                        (?:zh-(?:guoyu | hakka | min(?:-nan)? | xiang))
                                    )
                                )|(?P<langtag>
                                    (?:
                                        (?P<language>
                                            (?:
                                                [a-z]{2,3}   # shortest ISO 639
                                                (?P<extlang>
                                                    (?:-[a-z]{3,3}){1,3}
                                                )?                  # optional extlang tag
                                            )|
                                            (?:[a-z]{4}) |          # or reserved for future use
                                            (?:[a-z]{5,8})          # or registered language subtag
                                        )
                                        (?:-(?P<script>[a-z]{4}))?    # ISO 15924 code
                                        (?:-(?P<region>
                                            (?:[a-z]{2}) |        # ISO 3166-1 code
                                            (?:[0-9]{3})          # UN M.49 code
                                        ))?
                                        (?P<variants>
                                            (?:-(?:                 # 'variant'
                                                (?:[0-9a-z]{5,8})|  # registered variants
                                                (?:[0-9][a-z0-9]{3})
                                            ))+
                                        )?
                                        (?P<extensions>
                                            (?:-
                                                (?:                 # 'extension'
                                                    [0-9a-wy-z]     # 'singleton'
                                                    (?:-[0-9a-z]{2,8})+
                                                )
                                            )+
                                        )?
                                    )?
                                    (?:
                                        (?:(?<=^)|-)                # either alone or appended
                                        (?P<privateuse>
                                            [x](?:-[0-9a-z]{1,8})+
                                        )
                                    )?
                                )
                                )$/ix";

    /**
     *
     * @param string $raw   raw language tag
     * @return false|array  no tags| splitted language tag data
     * @throws InvalidArgumentException if subtags are not wellformed
     *  (for example: duplicate variants or extension singletons)
     */
    public function parse($raw)
    {
        // match productions
        $match = array();
        // will be corrected afterwards for uppercase and title case parts
        $raw = strtolower($raw);
        preg_match($this->_production, $raw, $match);

        // matching at all?
        if (empty($match)) {
            return false;
        }

        // strip empty entries, than strip numeric
        $match = array_filter($match);
        foreach ($match as $key => $v) {
            if (is_numeric($key)) {
                unset($match[$key]);
            }
        }

        // well-formed?
        $match = $this->_processLangtagMatch($match);

        return $match;
    }

    /**
     * Does ensure correct cases and splits recursive subtags.
     * Note however that the base match for 'languagetag' is the same case
     * as given to the parser.
     *
     * @param array     $match
     * @return array
     */
    protected function _processLangtagMatch($match)
    {
        if (array_key_exists('langtag', $match)) {
            // split variants
            if (array_key_exists('variants', $match)) {
                $variants = explode('-', substr($match['variants'],1));
                $match['variants'] = array();
                foreach ($variants as $variant) {
                    if (array_key_exists($variant, $match['variants'])) {
                        throw new Goatherd_LanguageTag_Exception(sprintf("Duplicate variant subtag '%'.", $variant));
                    }
                    $match['variants'][$variant] = $variant;
                }

            }

            // split extensions
            if (array_key_exists('extensions', $match)) {
                // separate extensions
                $extensions = preg_split('/-(?=[a-z0-9]-)/i', $match['extensions']);
                $match['extensions'] = array();
                array_shift($extensions);
                // process singleton
                foreach($extensions as $raw_extension) {
                    $values = explode('-', $raw_extension);
                    $key = array_shift($values);
                    // check for duplicate singleton
                    if (array_key_exists($key, $match['extensions'])) {
                        throw new Goatherd_LanguageTag_Exception(sprintf("Duplicate extension singleton '%'.", $key));
                    }
                    // split subtags
                    $match['extensions'][$key] = $values;
                }
            }

            // split private use subtags
            if (array_key_exists('privateuse', $match)) {
                $match['privateuse'] = substr($match['privateuse'], 2);
                $match['privateuse'] = explode('-', $match['privateuse']);
            }

            // process language subtag
            if (array_key_exists('language', $match)) {
                $match['language'] = strtolower($match['language']);
                if (array_key_exists('extlang', $match)) {
                    // split extlang
                    $match['extlang'] = explode('-', substr($match['extlang'],1));
                }
            }
            // correct script and region cases
            if (array_key_exists('script', $match)) {
                $match['script'] = ucfirst($match['script']);
            }
            if (array_key_exists('region', $match)) {
                $match['region'] = strtoupper($match['region']);
            }
        }

        return $match;
    }

    /**
     * @todo: might add feature to not break on first error
     *
     * @param array $match
     * @return boolean
     */
    public function validate($match)
    {
        $this->_error_messages = array();

        foreach($this->_validators as $validator) {
            $this->_error_messages = $validator->validate($match, $this->_error_messages);
        }

        return empty($this->_error_messages);
    }

    /**
     * List of error messages as returned by last validation.
     *
     * @return string[]
     */
    public function getErrorMessages()
    {
        return $this->_error_messages;
    }

    /**
     *
     * @param Goatherd_LanguageTag_Parser_Validator_ValidatorInterface $validator
     */
    public function addValidator(Goatherd_LanguageTag_Parser_Validator_ValidatorInterface $validator)
    {
        $this->_validators[spl_object_hash($validator)] = $validator;
    }

    /**
     * Validators can be unregistered by their spl hash or instance
     * @param string|object $validator
     */
    public function removeValidator($validator)
    {
        if (is_object($validator)) {
            $validator = spl_object_hash($validator);
        }

        if (array_key_exists($validator, $this->_validators)) {
            unset($this->_validators[$validator]);
        }
    }

    /**
     *
     * @return Goatherd_LanguageTag_Parser_Validator_ValidatorInterface
     */
    public function getValidators()
    {
        return $this->_validators;
    }
}