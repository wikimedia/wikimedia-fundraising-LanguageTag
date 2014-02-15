<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: NonGrandfathered.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
 * Disallows grandfathered tags
 *
 * @package LanguageTag
 * @subpackage Validator
 */
class Goatherd_LanguageTag_Validator_NonGrandfathered
implements Goatherd_LanguageTag_Validator_ValidatorInterface
{
    /**
     * (non-PHPdoc)
     * @see library/Goatherd/LanguageTag/Validator/Goatherd_LanguageTag_Validator_ValidatorInterface#validate()
     */
    public function validate($match, array $messages = array())
    {
        if (is_array($match) && array_key_exists('grandfathered', $match)) {
            $messages[] = sprintf('Grandfathered tag found: "%s".', $match['grandfathered']);
        }

        return $messages;
    }
}