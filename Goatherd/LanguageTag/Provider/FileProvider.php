<?php
/**
 * @package LanguageTag
 * @category Goatherd
 *
 * @author Copyright (c) 2010 Maik Penz <maik@phpkuh.de>
 * @version $Id: FileProvider.php 77 2012-03-27 07:53:54Z maik@phpkuh.de $
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
class Goatherd_LanguageTag_Provider_FileProvider
extends Goatherd_LanguageTag_Provider_AbstractProvider
{
    /**
     *
     * @var string
     */
    protected $_file = '';

    /**
     *
     * @param string $path
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    public function setFile($file)
    {
        $path = dirname($file);
        if (is_dir($path)) {
            $this->_file = $file;
        } else {
            throw new Goatherd_LanguageTag_Provider_Exception(sprintf('Invalid path "%s"', $path));
        }
    }

    /**
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Load taglists from file.
     *
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    public function load()
    {
        if (empty($this->_file)) {
            throw new Goatherd_LanguageTag_Provider_Exception('No file name given.');
        }
        if (!file_exists($this->_file)) {
            throw new Goatherd_LanguageTag_Provider_Exception(sprintf('File not found "%s"', $file));
        }

        $serial = file_get_contents($this->_file);
        $serial = gzinflate($serial);
        $data = unserialize($serial);
        if (!is_array($data)) {
            throw new Goatherd_LanguageTag_Provider_Exception('Invalid data type.');
        }

        $this->_tags = $data;
    }

    /**
     * Write taglists to file.
     *
     * @throws Goatherd_LanguageTag_Provider_Exception
     */
    public function store()
    {
        if (empty($this->_file)) {
            throw new Goatherd_LanguageTag_Provider_Exception('No file name given.');
        }

        $serial = serialize($this->_tags);
        $serial = gzdeflate($serial);
        file_put_contents($this->_file, $serial);
        // TODO: check if all data was written
    }
}