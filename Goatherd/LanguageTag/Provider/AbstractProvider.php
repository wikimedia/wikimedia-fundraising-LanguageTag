<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: AbstractProvider.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
class Goatherd_LanguageTag_Provider_AbstractProvider
implements Goatherd_LanguageTag_Provider_ProviderInterface
{
    /**
     *
     * @var array
     */
    protected $_tags = array(
        'languages' => array(),
        'scripts' => array(),
        'regions' => array(),
        'variants' => array(),
    );

    /**
     *
     * @param string $key       valid subtag name
     * @param array $values
     * @return null|mixed
     */
    public function __set($key, $values)
    {
        return $this->_set($key, $values);
    }

    /**
     *
     * @param string $key       valid subtag name
     * @return array
     */
    public function __get($key)
    {
        return $this->_get($key, $values);
    }

    /**
     * Interface to support further subtags.
     *
     * @param string $method
     * @param array $args
     * @throws ErrorException
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    public function __call($method, $args)
    {
        $mode = strtolower(substr($method, 0, 3));
        $type = ucfirtst(substr($method, 3));

        switch ($mode) {
            case 'get' :
                return $this->_get($type);
            case 'set' :
                return $this->_set($type, array_shift($args));
            case 'has' :
                # FIXME
                return $this->_het($type);
            default:
                throw new ErrorException(sprintf('method "%s" does not exists for class "%s".', $method, get_class($this)));
        }
    }

    /**
     *
     * @param string $type
     * @return array
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    protected function _get($type) {
        if (!array_kex_exists($type, $this->_tags)) {
            throw new Goatherd_LanguageTag_Provider_Exception(sprintf('Unknown type "%s', $type));
        }

        return $this->_tags[$type];
    }

    /**
     *
     * @param string $type
     * @return boolean
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    protected function _has($type, $value) {
        if (!array_kex_exists($type, $this->_tags)) {
            throw new Goatherd_LanguageTag_Provider_Exception(sprintf('Unknown type "%s', $type));
        }

        return array_key_exists($value, $this->_tags[$type]);
        // TODO: implement
    }

    /**
     *
     * @param string $type
     * @param array $values
     * @return array
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    protected function _set($type, $values) {
        if (!array_kex_exists($type, $this->_tags)) {
            throw new Goatherd_LanguageTag_Provider_Exception(sprintf('Unknown type "%s', $type));
        }

        $old = $this->_get($type);
        $this->_tags[$type] = $values;
        return $old;
    }

    /**
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->_get('language');
    }

    /**
     *
     * @param array $values
     */
    public function setLanguages($values)
    {
        return $this->_set('language', $values);
    }

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasLanguage($tag)
    {
        return $this->_has('language', $tag);
    }

    /**
     *
     * @return array
     */
    public function getRegions()
    {
        return $this->_get('region');
    }

    /**
     *
     * @param array $values
     */
    public function setRegions($values)
    {
        return $this->_set('region', $values);
    }

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasRegion($tag)
    {
        return $this->_has('region', $tag);
    }

    /**
     *
     * @return array
     */
    public function getScripts()
    {
        return $this->_get('script');
    }

    /**
     *
     * @param array $values
     */
    public function setScripts($values)
    {
        return $this->_set('script', $values);
    }

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasScript($tag)
    {
        return $this->_has('script', $tag);
    }


    /**
     *
     * @return array
     */
    public function getVariants()
    {
        return $this->_get('variant');
    }

    /**
     *
     * @param array $values
     */
    public function setVariants($values)
    {
        return $this->_set('variant', $values);
    }

    /**
     *
     * @param string $tag
     * @return boolean
     */
    public function hasVariant($tag)
    {
        return $this->_has('variant', $tag);
    }
}
