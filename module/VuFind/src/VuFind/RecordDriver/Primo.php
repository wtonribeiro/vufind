<?php
/**
 * Model for Primo Central records.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
namespace VuFind\RecordDriver;

/**
 * Model for Primo Central records.
 *
 * @category VuFind2
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:record_drivers Wiki
 */
class Primo extends SolrDefault
{
    /**
     * Date converter
     *
     * @var \VuFind\Date\Converter
     */
    protected $dateConverter = null;

    /**
     * Get the full title of the record.
     *
     * @return string
     */
    public function getTitle()
    {
        return isset($this->fields['title']) ?
            $this->fields['title'] : '';
    }
 
    /**
     * Get the authors of the record.
     *
     * @return array
     */
    public function getCreators()
    {
        return isset($this->fields['creator']) ?
            $this->fields['creator'] : array();
    }

   /**
     * Get an array of all subject headings associated with the record 
     * (may be empty).
     *
     * @return array
     */
    public function getAllSubjectHeadings()
    {
        $subjects = array();
        if (isset($this->fields['subjects'])) {
            $subjects = $this->fields['subjects'];
        }

        return $subjects;
    }

    /**
     * Get the item's source.
     *
     * @return array
     */
    public function getSource()
    {
        return isset($this->fields['ispartof']) ?
            $this->fields['ispartof'] : array();
    }

    /**
     * Get the item's description.
     *
     * @return array
     */
    public function getDescription()
    {
        return isset($this->fields['description']) ?
            $this->fields['description'] : array();
    }

    /**
     * Get the item's collection.
     *
     * @return array
     */
    public function getCollection()
    {
        return isset($this->fields['source']) ?
            $this->fields['source'] : array();
    }

    /**
     * Get an array of all ISSNs associated with the record (may be empty).
     *
     * @return array
     */
    public function getISSNs()
    {
        $issns = array();
        if (isset($this->fields['issn'])) {
            $issns = $this->fields['issn'];
        }
        return $issns;
    }

    /**
     * Get an array of all the languages associated with the record.
     *
     * @return array
     */
    public function getLanguages()
    {
        return isset($this->fields['language']) ?
            (array)$this->fields['language'] : array();
    }

    /**
     * Pass in a date converter
     *
     * @param \VuFind\Date\Converter $dc Date converter
     *
     * @return void
     */
    public function setDateConverter(\VuFind\Date\Converter $dc)
    {
        $this->dateConverter = $dc;
    }

    /**
     * Get a date converter
     *
     * @return \VuFind\Date\Converter
     */
    protected function getDateConverter()
    {
        // No object passed in yet?  Build one with default settings:
        if (null === $this->dateConverter) {
            $this->dateConverter = new \VuFind\Date\Converter();
        }
        return $this->dateConverter;
    }

    /**
     * Returns one of three things: a full URL to a thumbnail preview of the record
     * if an image is available in an external system; an array of parameters to
     * send to VuFind's internal cover generator if no fixed URL exists; or false
     * if no thumbnail can be generated.
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     * default).
     *
     * @return string|array|bool
     */
    public function getThumbnail($size = 'small')
    {
        $formats = $this->getFormats();
        if (($isbn = $this->getCleanISBN()) || !empty($formats)) {
            $params = array('size' => $size);
            if ($isbn) {
                $params['isn'] = $isbn;
            }
            if (!empty($formats)) {
                $params['contenttype'] = $formats[0];
            }
            return $params;
        }
        return false;
    }

    /**
     * Return an array of associative URL arrays with one or more of the following
     * keys:
     *
     * <li>
     *   <ul>desc: URL description text to display (optional)</ul>
     *   <ul>url: fully-formed URL (required if 'route' is absent)</ul>
     *   <ul>route: VuFind route to build URL with (required if 'url' is absent)</ul>
     *   <ul>routeParams: Parameters for route (optional)</ul>
     *   <ul>queryString: Query params to append after building route (optional)</ul>
     * </li>
     *
     * @return array
     */
    public function getURLs()
    { 
        $retVal = array();
        if (isset($this->fields['fulltext'])){
           $desc = $this->fields['fulltext'];

           if ($desc == 'fulltext'){
              $desc = "Get Full Text";
           }else{
              $desc = "Request Full Text in Find It";
           }
        }

        if (isset($this->fields['url'])) {
            $retVal[] =
                array(
                    'url' => $this->fields['url'],
                    'desc' => $this->translate($desc)
                );
        }
        return $retVal;
    }

    /**
     * Return the unique identifier of this record within the Solr index;
     * useful for retrieving additional information (like tags and user
     * comments) from the external MySQL database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        return $this->fields['recordid'];
    }

}
