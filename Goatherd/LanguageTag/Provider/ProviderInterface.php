<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: ProviderInterface.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
 *
 * @package LanguageTag
 * @subpackage Provider
 */
interface Goatherd_LanguageTag_Provider_ProviderInterface
{
    /**
     *
     * @return array
     */
    public function getLanguages();

    /**
     *
     * @param array $values
     */
    public function setLanguages($values);

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasLanguage($tag);

    /**
     *
     * @return array
     */
    public function getRegions();

    /**
     *
     * @param array $values
     */
    public function setRegions($values);

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasRegion($tag);

    /**
     *
     * @return array
     */
    public function getScripts();

    /**
     *
     * @param array $values
     */
    public function setScripts($values);

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasScript($tag);

    /**
     *
     * @return array
     */
    public function getVariants();

    /**
     *
     * @param array $values
     */
    public function setVariants($values);

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasVariant($tag);
}