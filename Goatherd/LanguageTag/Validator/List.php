<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: List.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
 * Validation against a tag tree.
 *
 * For each tag the current tree level is searched for
 * a key of same value.
 *
 * If found the level is increaed, else an error message is created.
 *
 * The following example allows only american and british english with latin
 * script. For british english any variant, extension, etc is accepted,
 * for american english non allowed.
 *
 * $tree = array(
 *         'en' => array(                           # Language: en
 *                 'GB' => array(),                 # Region: GB
 *                 'US' => array(false),            # Region: US
 *                 'Latn' => array(                 # Script: Latin
 *                           'GB' => array(),       # Region: GB
 *                           'US' => array(false),  # Region: US
 *                 ),
 *         ),
 *         );
 * $validator = new Goatherd_LanguageTag_Validator_List();
 * $validator->setTree($tree);
 *
 * // you can also use a tag list to build the tree
 * $list = array(
 *  'en-GB',
 *  'en-latn-GB',
 *  'en-US-',
 *  'en-latn-US',
 * );
 * $tree = Goatherd_LanguageTag_Validator_List::treeFromTagList($list);
 *
 * @package LanguageTag
 * @subpackage Validator
 */
abstract class Goatherd_LanguageTag_Validator_List
implements Goatherd_LanguageTag_Validator_ValidatorInterface
{
    /**
     *
     * @var array
     */
    protected $_tree = array();

    /**
     *
     * @param array $tree
     */
    public function setTree($tree)
    {
        $this->_tree = (array) $tree;
    }

    /**
     *
     * @return array
     */
    public function getTree()
    {
        return $this->_tree;
    }



    /**
     * (non-PHPdoc)
     * @see library/Goatherd/LanguageTag/Validator/Goatherd_LanguageTag_Validator_ValidatorInterface#validate()
     */
    public function validate($match, array $messages = array())
    {
        $check = $this->_tree;
        foreach ($match as $tag) {
            if (!empty($check)) {
                if (array_key_exists($tag, $check)) {
                    $check = $check[$tag];
                } else {
                    $messages[] = sprintf('Any of [%s] expected but "%s" given.', implode(', ',array_keys($check)), $tag);
                }
            } else {
                break;
            }
        }

        return $messages;
    }

    /**
     * Simple tag list importer.
     *
     * @param string[] $tags
     * @return array
     */
    public static function treeFromTagList($tags)
    {
        $tree = array();
        foreach($tags as $key => $tag) {
            self::_addToTree(explode('-', $tag), $tree);
        }

        return $tree;
    }

    /**
     * Helper for tag list import.
     *
     * @param array $element
     * @param array $tree
     * @return array
     */
    protected static function _addToTree($element, $tree)
    {
        if (!empty($element)) {
            $tag = array_shift($element);
            array_key_exists($tag, $tree) or $tree[$tag] = array();
            $tree[$tag] = self::_addToTree($element, $tree[$tag]);
        }
        return $tree;
    }
}