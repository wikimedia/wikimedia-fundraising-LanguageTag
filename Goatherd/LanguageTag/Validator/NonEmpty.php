<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: NonEmpty.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
 * Basic match syntax check.
 *
 * @package LanguageTag
 * @subpackage Validator
 */
class Goatherd_LanguageTag_Validator_NonEmpty
implements Goatherd_LanguageTag_Validator_ValidatorInterface
{
    /**
     * (non-PHPdoc)
     * @see library/Goatherd/LanguageTag/Validator/Goatherd_LanguageTag_Validator_ValidatorInterface#validate()
     */
    public function validate($match, array $messages = array())
    {
        // check argument
        if (!is_array($match)) {
            $messages[] = sprintf('Match must be of type array. "%s" given.', gettype($match));
        }

        if (empty($match)) {
            $messages[] = 'Syntax error: match is empty.';
        }

        return $messages;
    }
}